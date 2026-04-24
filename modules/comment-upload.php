<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once MYAIO_PATH . 'core/CommentImageService.php';

class MyAIO_Comment_Upload_Module
{
    private $image_service;

    public function __construct()
    {
        $this->image_service = new MyAIO_Comment_Image_Service();
        $this->init_hooks();
    }

    private function init_hooks()
    {
        // Enqueue assets
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);

        // Frontend: Inject file input into comment form and add enctype via JS
        add_filter('comment_form_submit_field', [$this, 'add_upload_field'], 10, 2);
        
        // Handle upload on comment submission
        add_action('comment_post', [$this, 'handle_comment_upload'], 10, 2);

        // Display images in WooCommerce product reviews (frontend)
        add_action('woocommerce_review_after_comment_text', [$this, 'display_frontend_images']);
        
        // Display images in WP Admin comment list (backend)
        add_filter('comment_text', [$this, 'display_admin_images'], 10, 2);
    }

    public function enqueue_assets()
    {
        if (is_product()) {
            wp_enqueue_style('myaio-comment-upload-css', MYAIO_URL . 'frontend/assets/css/comment-upload.css', [], MYAIO_VERSION);
        }
    }

    public function add_upload_field($submit_field, $args)
    {
        if (!is_product()) {
            return $submit_field;
        }

        // JS to add enctype to the form
        $script = '<script>document.addEventListener("DOMContentLoaded", function() {
            var form = document.getElementById("commentform");
            if (form) form.setAttribute("enctype", "multipart/form-data");
        });</script>';

        // HTML for the file input
        $upload_html = '<div class="myaio-comment-image-upload">
            <label for="myaio_comment_images">' . __('Đính kèm hình ảnh (tối đa 3 ảnh, JPG/PNG, <2MB)', 'my-woo-aio') . '</label>
            <input type="file" id="myaio_comment_images" name="myaio_comment_images[]" accept="image/jpeg, image/png, image/jpg" multiple>
        </div>';

        return $script . $upload_html . $submit_field;
    }

    public function handle_comment_upload($comment_id, $comment_approved)
    {
        if (!isset($_FILES['myaio_comment_images'])) {
            return;
        }

        $result = $this->image_service->upload_images($_FILES['myaio_comment_images']);

        if (!empty($result['urls'])) {
            $this->image_service->save_comment_images($comment_id, $result['urls']);
        }
        
        // Note: errors are currently ignored on frontend to not interrupt the comment flow.
        // In a more robust system, we would interrupt and show errors.
    }

    public function display_frontend_images($comment)
    {
        $images = $this->image_service->get_comment_images($comment->comment_ID);
        if (empty($images)) {
            return;
        }

        echo '<div class="myaio-review-images">';
        foreach ($images as $img_url) {
            echo '<a href="' . esc_url($img_url) . '" target="_blank">';
            echo '<img src="' . esc_url($img_url) . '" class="myaio-review-img" alt="Review Image">';
            echo '</a>';
        }
        echo '</div>';
    }

    public function display_admin_images($comment_text, $comment = null)
    {
        if (!is_admin() || !$comment) {
            return $comment_text;
        }

        $images = $this->image_service->get_comment_images($comment->comment_ID);
        if (empty($images)) {
            return $comment_text;
        }

        $html = '<div style="margin-top:10px; display:flex; gap:10px;">';
        foreach ($images as $img_url) {
            $html .= '<a href="' . esc_url($img_url) . '" target="_blank">';
            $html .= '<img src="' . esc_url($img_url) . '" style="max-width: 100px; height: auto; border: 1px solid #ddd; border-radius: 4px;" alt="Attached">';
            $html .= '</a>';
        }
        $html .= '</div>';

        return $comment_text . $html;
    }
}

new MyAIO_Comment_Upload_Module();
