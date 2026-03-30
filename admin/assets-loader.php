<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_enqueue_scripts', function ($hook) {

    if (strpos($hook, 'myaio') === false) {
        return;
    }

    wp_enqueue_style(
        'myaio-admin',
        MYAIO_URL . 'admin/assets/css/admin.css',
        [],
        MYAIO_VERSION
    );

    wp_enqueue_script(
        'myaio-admin',
        MYAIO_URL . 'admin/assets/js/admin.js',
        ['jquery'],
        MYAIO_VERSION,
        true
    );

    wp_localize_script('myaio-admin', 'myaioAdmin', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('myaio_nonce'),
    ]);
});
