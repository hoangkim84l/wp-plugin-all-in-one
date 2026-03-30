<?php
if (!defined('ABSPATH')) exit;

class MyAIO_Loader {

    public static function init() {
        self::load_i18n();
        self::load_core();
        self::load_admin();
        self::load_ajax();
        self::load_modules();
    }

    private static function load_i18n() {
        require_once MYAIO_PATH . 'core/i18n.php';
        MyAIO_i18n::load_textdomain();
    }

    private static function load_core() {
        require_once MYAIO_PATH . 'core/module-manager.php';
    }

    private static function load_admin() {
        if (is_admin()) {
            require_once MYAIO_PATH . 'admin/admin.php';
        }
    }

    private static function load_ajax() {
        require_once MYAIO_PATH . 'ajax/ajax.php';
    }

    private static function load_modules() {
        MyAIO_Module_Manager::load_active_modules();
    }
}
