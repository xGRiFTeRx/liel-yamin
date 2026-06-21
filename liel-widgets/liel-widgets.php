<?php
/*
Plugin Name: Liel Widgets
Plugin URI:  https://github.com/xGRiFTeRx/liel-yamin
Description: Custom Elementor widgets for the Liel Yamin bridal site (hero slider with image/video, collection tiles, etc.).
Version:     1.0.7
Author:      Liel
Text Domain: liel-bridal
*/

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'LIEL_BRIDAL_VERSION', '1.0.7' );
define( 'LIEL_BRIDAL_FILE',    __FILE__ );
define( 'LIEL_BRIDAL_PATH',    plugin_dir_path( __FILE__ ) );
define( 'LIEL_BRIDAL_URL',     plugin_dir_url( __FILE__ ) );

require_once LIEL_BRIDAL_PATH . 'includes/class-liel-plugin.php';

Liel_Plugin::instance();
