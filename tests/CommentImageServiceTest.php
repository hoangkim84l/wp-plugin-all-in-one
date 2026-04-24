<?php

use PHPUnit\Framework\TestCase;

if (!defined('ABSPATH')) {
    define('ABSPATH', '/dummy/path/');
}
if (!defined('UPLOAD_ERR_OK')) {
    define('UPLOAD_ERR_OK', 0);
}

// Mock WordPress functions
if (!function_exists('wp_handle_upload')) {
    function wp_handle_upload(&$file, $overrides) {
        if ($file['name'] === 'fail.jpg') {
            return ['error' => 'Upload failed'];
        }
        return ['url' => 'http://example.com/uploads/' . $file['name']];
    }
}
if (!function_exists('add_comment_meta')) {
    function add_comment_meta($comment_id, $meta_key, $meta_value, $unique = false) {
        $GLOBALS['mock_comment_meta'][$comment_id][$meta_key] = $meta_value;
        return true;
    }
}
if (!function_exists('get_comment_meta')) {
    function get_comment_meta($comment_id, $key = '', $single = false) {
        if (isset($GLOBALS['mock_comment_meta'][$comment_id][$key])) {
            return $GLOBALS['mock_comment_meta'][$comment_id][$key];
        }
        return false;
    }
}
if (!function_exists('__')) {
    function __($text, $domain) { return $text; }
}

require_once dirname(__DIR__) . '/core/CommentImageService.php';

class CommentImageServiceTest extends TestCase
{
    private $service;

    protected function setUp(): void
    {
        $this->service = new MyAIO_Comment_Image_Service();
        $GLOBALS['mock_comment_meta'] = [];
    }

    public function testUploadImagesSuccess()
    {
        $file_data = [
            'name'     => ['test1.jpg', 'test2.png'],
            'type'     => ['image/jpeg', 'image/png'],
            'tmp_name' => ['/tmp/1', '/tmp/2'],
            'error'    => [UPLOAD_ERR_OK, UPLOAD_ERR_OK],
            'size'     => [1024, 2048]
        ];

        $result = $this->service->upload_images($file_data);

        $this->assertEmpty($result['errors']);
        $this->assertCount(2, $result['urls']);
        $this->assertEquals('http://example.com/uploads/test1.jpg', $result['urls'][0]);
        $this->assertEquals('http://example.com/uploads/test2.png', $result['urls'][1]);
    }

    public function testUploadImagesExceedsMaxSize()
    {
        $file_data = [
            'name'     => ['large.jpg'],
            'type'     => ['image/jpeg'],
            'tmp_name' => ['/tmp/1'],
            'error'    => [UPLOAD_ERR_OK],
            'size'     => [3000000] // 3MB
        ];

        $result = $this->service->upload_images($file_data);

        $this->assertCount(1, $result['errors']);
        $this->assertEmpty($result['urls']);
        $this->assertStringContainsString('too large', $result['errors'][0]);
    }

    public function testUploadImagesInvalidType()
    {
        $file_data = [
            'name'     => ['doc.pdf'],
            'type'     => ['application/pdf'],
            'tmp_name' => ['/tmp/1'],
            'error'    => [UPLOAD_ERR_OK],
            'size'     => [1024]
        ];

        $result = $this->service->upload_images($file_data);

        $this->assertCount(1, $result['errors']);
        $this->assertEmpty($result['urls']);
        $this->assertStringContainsString('invalid type', $result['errors'][0]);
    }

    public function testUploadImagesLimitMaxImages()
    {
        // Service limits to 3 images max
        $file_data = [
            'name'     => ['1.jpg', '2.jpg', '3.jpg', '4.jpg'],
            'type'     => ['image/jpeg', 'image/jpeg', 'image/jpeg', 'image/jpeg'],
            'tmp_name' => ['/t', '/t', '/t', '/t'],
            'error'    => [0, 0, 0, 0],
            'size'     => [10, 10, 10, 10]
        ];

        $result = $this->service->upload_images($file_data);

        $this->assertCount(3, $result['urls']);
    }

    public function testSaveAndGetCommentImages()
    {
        $comment_id = 99;
        $urls = ['http://example.com/1.jpg', 'http://example.com/2.jpg'];

        $this->service->save_comment_images($comment_id, $urls);

        $retrieved = $this->service->get_comment_images($comment_id);

        $this->assertEquals($urls, $retrieved);
    }
}
