<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_init', function () {

    register_setting(
        'myaio_modules_group',
        'myaio_active_modules',
        [
            'type' => 'array',
            'sanitize_callback' => 'myaio_sanitize_modules',
            'default' => []
        ]
    );
});

function myaio_sanitize_modules($input)
{
    if (!is_array($input)) {
        return [];
    }

    $allowed = MyAIO_Module_Manager::get_all_modules();
    return array_values(array_intersect($allowed, $input));
}

add_action('update_option_myaio_active_modules', function () {
    flush_rewrite_rules();
});
