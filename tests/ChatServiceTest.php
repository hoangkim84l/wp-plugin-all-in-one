<?php

use PHPUnit\Framework\TestCase;

// Dummy classes/functions for WP environment
if (!defined('ABSPATH')) {
    define('ABSPATH', '/dummy/path/');
}
if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str) { return htmlspecialchars($str); }
}
if (!function_exists('sanitize_email')) {
    function sanitize_email($str) { return filter_var($str, FILTER_SANITIZE_EMAIL); }
}
if (!function_exists('sanitize_textarea_field')) {
    function sanitize_textarea_field($str) { return htmlspecialchars($str); }
}
if (!function_exists('wp_json_encode')) {
    function wp_json_encode($data) { return json_encode($data); }
}
if (!function_exists('current_time')) {
    function current_time($type) { return '2023-10-10 10:10:10'; }
}

require_once dirname(__DIR__) . '/core/ChatService.php';

class ChatServiceTest extends TestCase
{
    private $chat_service;
    private $wpdb_mock;

    protected function setUp(): void
    {
        // Mock global $wpdb
        $this->wpdb_mock = $this->createMock(\stdClass::class);
        $this->wpdb_mock->prefix = 'wp_';
        
        // Add required methods to mock dynamically
        $this->wpdb_mock = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['insert', 'prepare', 'get_results', 'update', 'get_charset_collate'])
            ->getMock();
            
        $this->wpdb_mock->prefix = 'wp_';

        $GLOBALS['wpdb'] = $this->wpdb_mock;

        $this->chat_service = new MyAIO_Chat_Service();
    }

    public function testSaveMessage()
    {
        $name = 'John Doe';
        $email = 'john@example.com';
        $message = 'Hello world!';
        $userInfo = ['ip' => '127.0.0.1'];

        // Expect insert to be called with correct arguments
        $this->wpdb_mock->expects($this->once())
            ->method('insert')
            ->with(
                $this->equalTo('wp_myaio_chat_messages'),
                $this->callback(function($data) use ($name, $email, $message) {
                    return $data['name'] === $name 
                        && $data['email'] === $email
                        && $data['message'] === $message
                        && $data['status'] === 'unread'
                        && json_decode($data['user_info'], true)['ip'] === '127.0.0.1';
                })
            )
            ->willReturn(1);

        $result = $this->chat_service->save_message($name, $email, $message, $userInfo);
        $this->assertEquals(1, $result);
    }

    public function testGetMessages()
    {
        $this->wpdb_mock->expects($this->once())
            ->method('prepare')
            ->willReturn('PREPARED_QUERY');

        $this->wpdb_mock->expects($this->once())
            ->method('get_results')
            ->with($this->equalTo('PREPARED_QUERY'))
            ->willReturn(['msg1', 'msg2']);

        $messages = $this->chat_service->get_messages();
        $this->assertCount(2, $messages);
    }

    public function testMarkAsRead()
    {
        $id = 5;

        $this->wpdb_mock->expects($this->once())
            ->method('update')
            ->with(
                $this->equalTo('wp_myaio_chat_messages'),
                $this->equalTo(['status' => 'read']),
                $this->equalTo(['id' => $id]),
                $this->equalTo(['%s']),
                $this->equalTo(['%d'])
            )
            ->willReturn(1);

        $result = $this->chat_service->mark_as_read($id);
        $this->assertEquals(1, $result);
    }
}
