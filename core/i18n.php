<?php
if (!defined('ABSPATH')) exit;

class MyAIO_i18n {

    public static function load_textdomain() {
        load_plugin_textdomain(
            'my-woo-aio',
            false,
            dirname(MYAIO_BASENAME) . '/languages'
        );
    }
}
