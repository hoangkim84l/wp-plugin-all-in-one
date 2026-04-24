<?php
if (!defined('ABSPATH')) {
    exit;
}

class MyAIO_Chat_Service
{
    private $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'myaio_chat_messages';
    }

    public function create_table()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $this->table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            message text NOT NULL,
            user_info json DEFAULT NULL,
            status varchar(20) DEFAULT 'unread' NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function save_message($name, $email, $message, $user_info = [])
    {
        global $wpdb;
        
        $data = [
            'name' => sanitize_text_field($name),
            'email' => sanitize_email($email),
            'message' => sanitize_textarea_field($message),
            'user_info' => wp_json_encode($user_info),
            'status' => 'unread',
            'created_at' => current_time('mysql')
        ];

        return $wpdb->insert($this->table_name, $data);
    }

    public function get_messages($limit = 50, $offset = 0)
    {
        global $wpdb;
        $query = $wpdb->prepare("SELECT * FROM {$this->table_name} ORDER BY created_at DESC LIMIT %d OFFSET %d", $limit, $offset);
        return $wpdb->get_results($query);
    }

    public function mark_as_read($id)
    {
        global $wpdb;
        return $wpdb->update(
            $this->table_name,
            ['status' => 'read'],
            ['id' => intval($id)],
            ['%s'],
            ['%d']
        );
    }
}
