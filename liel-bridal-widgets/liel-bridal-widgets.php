<?php
/**
 * Plugin Name:       Liel Bridal Widgets
 * Plugin URI:        https://github.com/xGRiFTeRx/liel-yamin
 * Description:       Custom Elementor widgets for the Liel Yamin bridal site (hero slider with image/video, collection tiles, etc.).
 * Version:           1.0.0
 * Author:            Liel
 * Text Domain:       liel-bridal
 * Domain Path:       /languages
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Requires Plugins:  elementor
 *
 * Stable identity (NEVER change between releases — see liel-plugin-architecture memory):
 *   - Folder:      liel-bridal-widgets/
 *   - Main file:   liel-bridal-widgets.php
 *   - Constants:   LIEL_BRIDAL_*
 *   - Handles:     liel-* / liel-{slug}
 *   - Category:    liel
 * Only the `Version:` header above and the LIEL_BRIDAL_VERSION constant below are
 * bumped each release. Keep them in sync. Build with build-zip.ps1 at repo root.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LIEL_BRIDAL_VERSION', '1.0.0' );
define( 'LIEL_BRIDAL_FILE',    __FILE__ );
define( 'LIEL_BRIDAL_PATH',    plugin_dir_path( __FILE__ ) );
define( 'LIEL_BRIDAL_URL',     plugin_dir_url( __FILE__ ) );

require_once LIEL_BRIDAL_PATH . 'includes/class-liel-plugin.php';

Liel_Plugin::instance();
