<?php
if (!defined('ABSPATH'))
    exit;

class MyAIO_Module_Manager
{

    private static $modules = [
        'permalink',
        'category-page',
        'tags',
        'loadmore',
        'price-engine',
        'comment-upload',
        'read-more',
        'scroll-to-top',
        'chat-bubble',
    ];

    public static function get_all_modules()
    {
        return self::$modules;
    }

    public static function get_active_modules()
    {
        $saved = get_option('myaio_active_modules', []);
        if (!is_array($saved)) {
            $saved = [];
        }
        return $saved;
    }

    public static function is_active($module)
    {
        $active = self::get_active_modules();
        return in_array($module, $active);
    }

    public static function load_active_modules()
    {
        foreach (self::$modules as $module) {
            if (self::is_active($module)) { // Kiểm tra xem tên module có nằm trong danh sách đã được lưu không
                $file = MYAIO_PATH . "modules/{$module}.php";
                if (file_exists($file)) {
                    require_once $file; // <<< CHỈ REQUIRE KHI ĐƯỢC BẬT
                }
            }
        }
    }
}