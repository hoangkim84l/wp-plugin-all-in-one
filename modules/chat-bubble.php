<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once MYAIO_PATH . 'core/ChatService.php';

class MyAIO_Chat_Bubble_Module
{
    private $chat_service;

    public function __construct()
    {
        $this->chat_service = new MyAIO_Chat_Service();
        $this->init_hooks();
    }

    private function init_hooks()
    {
        // Khởi tạo bảng dữ liệu
        add_action('admin_init', [$this, 'install_db']);

        // Giao diện admin
        add_action('admin_menu', [$this, 'add_admin_menu'], 20);

        // Giao diện frontend
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_footer', [$this, 'render_bubble_html']);

        // AJAX xử lý tin nhắn
        add_action('wp_ajax_myaio_submit_chat', [$this, 'handle_submit_chat']);
        add_action('wp_ajax_nopriv_myaio_submit_chat', [$this, 'handle_submit_chat']);
        add_action('wp_ajax_myaio_mark_chat_read', [$this, 'handle_mark_read']);
    }

    public function install_db()
    {
        $installed_ver = get_option('myaio_chat_db_version');
        if ($installed_ver != MYAIO_VERSION) {
            $this->chat_service->create_table();
            update_option('myaio_chat_db_version', MYAIO_VERSION);
        }
    }

    public function add_admin_menu()
    {
        add_submenu_page(
            'myaio-dashboard',
            __('Tin nhắn', 'my-woo-aio'),
            __('Tin nhắn', 'my-woo-aio'),
            'manage_options',
            'myaio-chat-messages',
            [$this, 'render_admin_page']
        );
    }

    public function enqueue_assets()
    {
        wp_enqueue_style('myaio-chat-bubble-css', MYAIO_URL . 'frontend/assets/css/chat-bubble.css', [], MYAIO_VERSION);
        wp_enqueue_script('myaio-chat-bubble-js', MYAIO_URL . 'frontend/assets/js/chat-bubble.js', ['jquery'], MYAIO_VERSION, true);
        
        wp_localize_script('myaio-chat-bubble-js', 'myaioChat', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('myaio-chat-nonce')
        ]);
    }

    public function render_bubble_html()
    {
        ?>
        <div id="myaio-chat-widget">
            <div id="myaio-chat-popup" class="myaio-hidden">
                <div class="myaio-chat-header">
                    <h4><?php _e('Để lại lời nhắn', 'my-woo-aio'); ?></h4>
                    <button id="myaio-chat-close">&times;</button>
                </div>
                <div class="myaio-chat-body">
                    <form id="myaio-chat-form">
                        <input type="text" name="myaio_chat_name" placeholder="<?php _e('Tên của bạn', 'my-woo-aio'); ?>" required>
                        <input type="email" name="myaio_chat_email" placeholder="<?php _e('Email của bạn', 'my-woo-aio'); ?>" required>
                        <textarea name="myaio_chat_message" placeholder="<?php _e('Lời nhắn...', 'my-woo-aio'); ?>" required></textarea>
                        <button type="submit"><?php _e('Gửi tin nhắn', 'my-woo-aio'); ?></button>
                    </form>
                    <div id="myaio-chat-response" style="display:none;"></div>
                </div>
            </div>
            <div id="myaio-chat-button">
                <span class="dashicons dashicons-format-chat"></span>
            </div>
        </div>
        <?php
    }

    public function handle_submit_chat()
    {
        check_ajax_referer('myaio-chat-nonce', 'nonce');

        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';

        if (empty($name) || empty($email) || empty($message)) {
            wp_send_json_error(['message' => __('Vui lòng điền đủ thông tin.', 'my-woo-aio')]);
        }

        $user_info = [
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];

        $inserted = $this->chat_service->save_message($name, $email, $message, $user_info);

        if ($inserted) {
            wp_send_json_success(['message' => __('Cảm ơn bạn đã để lại lời nhắn!', 'my-woo-aio')]);
        } else {
            wp_send_json_error(['message' => __('Có lỗi xảy ra, vui lòng thử lại sau.', 'my-woo-aio')]);
        }
    }

    public function handle_mark_read()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id > 0) {
            $this->chat_service->mark_as_read($id);
            wp_send_json_success();
        }
        wp_send_json_error();
    }

    public function render_admin_page()
    {
        $messages = $this->chat_service->get_messages();
        ?>
        <div class="wrap">
            <h1><?php _e('Tin nhắn khách hàng', 'my-woo-aio'); ?></h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Lời nhắn</th>
                        <th>Thông tin</th>
                        <th>Trạng thái</th>
                        <th>Thời gian</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($messages)) : ?>
                        <tr><td colspan="8"><?php _e('Chưa có tin nhắn nào.', 'my-woo-aio'); ?></td></tr>
                    <?php else : ?>
                        <?php foreach ($messages as $msg) : 
                            $info = json_decode($msg->user_info, true);
                        ?>
                            <tr id="myaio-msg-<?php echo esc_attr($msg->id); ?>">
                                <td><?php echo esc_html($msg->id); ?></td>
                                <td><?php echo esc_html($msg->name); ?></td>
                                <td><?php echo esc_html($msg->email); ?></td>
                                <td><?php echo nl2br(esc_html($msg->message)); ?></td>
                                <td>
                                    <?php if ($info): ?>
                                        IP: <?php echo esc_html($info['ip'] ?? ''); ?><br>
                                    <?php endif; ?>
                                </td>
                                <td class="msg-status">
                                    <?php if ($msg->status == 'unread'): ?>
                                        <span class="dashicons dashicons-warning" style="color:red;" title="Chưa xem"></span> Chưa xem
                                    <?php else: ?>
                                        <span class="dashicons dashicons-yes" style="color:green;" title="Đã xem"></span> Đã xem
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html($msg->created_at); ?></td>
                                <td>
                                    <?php if ($msg->status == 'unread'): ?>
                                        <button class="button myaio-mark-read" data-id="<?php echo esc_attr($msg->id); ?>"><?php _e('Đã xem', 'my-woo-aio'); ?></button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <script>
            jQuery(document).ready(function($) {
                $('.myaio-mark-read').on('click', function(e) {
                    e.preventDefault();
                    var btn = $(this);
                    var id = btn.data('id');
                    $.post(ajaxurl, {
                        action: 'myaio_mark_chat_read',
                        id: id
                    }, function(res) {
                        if (res.success) {
                            btn.closest('tr').find('.msg-status').html('<span class="dashicons dashicons-yes" style="color:green;"></span> Đã xem');
                            btn.remove();
                        }
                    });
                });
            });
        </script>
        <?php
    }
}

new MyAIO_Chat_Bubble_Module();
