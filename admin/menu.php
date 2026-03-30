<?php
if (!defined('ABSPATH')) exit;

add_action('admin_menu', function () {
    add_menu_page(
        __('My Woo AIO', 'my-woo-aio'),
        __('My Woo AIO', 'my-woo-aio'),
        'manage_options',
        'myaio-dashboard',
        'myaio_render_dashboard',
        'dashicons-admin-generic',
        56
    );

    add_submenu_page(
        'myaio-dashboard',
        __('Modules', 'my-woo-aio'),
        __('Modules', 'my-woo-aio'),
        'manage_options',
        'myaio-modules',
        'myaio_render_modules_page'
    );
});

require_once MYAIO_PATH . 'admin/pages/dashboard.php';

function myaio_render_dashboard() {
    include MYAIO_PATH . 'admin/pages/dashboard.php';
}

function myaio_render_modules_page() {
    include MYAIO_PATH . 'admin/pages/modules.php';
}
