<?php
if (!defined('ABSPATH')) {
    exit;
}

class MyAIO_Comment_Image_Service
{
    private $max_images = 3;
    private $max_size = 2097152; // 2MB in bytes
    private $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];

    /**
     * Uploads images from $_FILES format.
     *
     * @param array $file_data The $_FILES['name'] element, e.g., $_FILES['myaio_comment_images']
     * @return array Array of uploaded image URLs or error messages.
     */
    public function upload_images($file_data)
    {
        $uploaded_urls = [];
        $errors = [];

        if (empty($file_data['name'][0])) {
            return ['urls' => [], 'errors' => []];
        }

        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $count = count($file_data['name']);
        if ($count > $this->max_images) {
            $count = $this->max_images; // Limit to max images
        }

        for ($i = 0; $i < $count; $i++) {
            if ($file_data['error'][$i] === UPLOAD_ERR_OK) {
                // Validation
                if ($file_data['size'][$i] > $this->max_size) {
                    $errors[] = sprintf(__('File %s is too large. Max size is 2MB.', 'my-woo-aio'), $file_data['name'][$i]);
                    continue;
                }
                if (!in_array($file_data['type'][$i], $this->allowed_types)) {
                    $errors[] = sprintf(__('File %s has an invalid type. Only JPG and PNG are allowed.', 'my-woo-aio'), $file_data['name'][$i]);
                    continue;
                }

                // Prepare file array for wp_handle_upload
                $single_file = [
                    'name'     => $file_data['name'][$i],
                    'type'     => $file_data['type'][$i],
                    'tmp_name' => $file_data['tmp_name'][$i],
                    'error'    => $file_data['error'][$i],
                    'size'     => $file_data['size'][$i]
                ];

                $upload_overrides = ['test_form' => false];
                $movefile = wp_handle_upload($single_file, $upload_overrides);

                if ($movefile && !isset($movefile['error'])) {
                    $uploaded_urls[] = $movefile['url'];
                } else {
                    $errors[] = $movefile['error'];
                }
            }
        }

        return ['urls' => $uploaded_urls, 'errors' => $errors];
    }

    /**
     * Saves image URLs to comment meta.
     *
     * @param int $comment_id
     * @param array $image_urls
     */
    public function save_comment_images($comment_id, $image_urls)
    {
        if (!empty($image_urls)) {
            add_comment_meta($comment_id, 'myaio_comment_images', $image_urls);
        }
    }

    /**
     * Retrieves image URLs for a comment.
     *
     * @param int $comment_id
     * @return array
     */
    public function get_comment_images($comment_id)
    {
        $images = get_comment_meta($comment_id, 'myaio_comment_images', true);
        return is_array($images) ? $images : [];
    }
}
