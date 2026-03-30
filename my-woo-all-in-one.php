<?php
/*
Plugin Name: My Woo All In One
Description: Custom WooCommerce system (no dependency, full control)
Version: 1.0.0
Author: 7_les
Text Domain: my-woo-aio
Domain Path: /languages
*/

if (!defined('ABSPATH')) exit;

// Constants
define('MYAIO_VERSION', '1.0.0');
define('MYAIO_PATH', plugin_dir_path(__FILE__));
define('MYAIO_URL', plugin_dir_url(__FILE__));
define('MYAIO_BASENAME', plugin_basename(__FILE__));

// Load core
require_once MYAIO_PATH . 'core/loader.php';

// Boot
add_action('plugins_loaded', function () {
    MyAIO_Loader::init();
});
