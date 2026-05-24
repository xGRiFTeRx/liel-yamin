<?php
/**
 * Plugin Name: Liel Bridal Widgets
 * Description: Custom Elementor widgets for the Liel bridal site, built section by section.
 * Version:     0.1.0
 * Author:      Liel
 * Text Domain: liel-bridal
 *
 * Requires Plugins: elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'LIEL_BW_VERSION', '0.1.0' );
define( 'LIEL_BW_PATH', plugin_dir_path( __FILE__ ) );
define( 'LIEL_BW_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main plugin bootstrap.
 */
final class Liel_Bridal_Widgets {

	const MIN_ELEMENTOR_VERSION = '3.5.0';
	const MIN_PHP_VERSION       = '7.4';

	/**
	 * Each section we add gets one entry here:
	 * 'file' => class file under /widgets, 'class' => the widget class name.
	 */
	private $widgets = array(
		array( 'file' => 'class-liel-hero-slider.php', 'class' => 'Liel_Hero_Slider_Widget' ),
	);

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	public function init() {
		// Bail with an admin notice if Elementor is missing/old.
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'notice_missing_elementor' ) );
			return;
		}
		if ( ! version_compare( ELEMENTOR_VERSION, self::MIN_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'notice_old_elementor' ) );
			return;
		}

		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );

		// Register assets early so widgets can declare them via get_*_depends().
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ), 5 );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_editor_assets' ) );
	}

	/**
	 * Adds a "Liel" category to the Elementor widget panel.
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'liel',
			array(
				'title' => __( 'Liel', 'liel-bridal' ),
				'icon'  => 'eicon-woman',
			)
		);
	}

	/**
	 * Loads + registers every widget listed in $this->widgets.
	 */
	public function register_widgets( $widgets_manager ) {
		foreach ( $this->widgets as $widget ) {
			$path = LIEL_BW_PATH . 'widgets/' . $widget['file'];
			if ( file_exists( $path ) ) {
				require_once $path;
				if ( class_exists( $widget['class'] ) ) {
					$widgets_manager->register( new $widget['class']() );
				}
			}
		}
	}

	const FONTS_URL = 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400&family=Frank+Ruhl+Libre:wght@300;400;500;700&family=Heebo:wght@300;400;500&display=swap';
	const SWIPER_VERSION = '8.4.7';

	/**
	 * Registers all assets. Interactive deps (Swiper, liel.js) are only
	 * pulled in by widgets that declare them via get_script/style_depends();
	 * the brand stylesheet + fonts load globally so styling is always present.
	 */
	public function register_assets() {
		wp_register_style( 'swiper-bundle', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css', array(), self::SWIPER_VERSION );
		wp_register_script( 'swiper-bundle', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', array(), self::SWIPER_VERSION, true );

		wp_register_style( 'liel-bw', LIEL_BW_URL . 'assets/liel.css', array(), LIEL_BW_VERSION );
		wp_register_script( 'liel-bw', LIEL_BW_URL . 'assets/liel.js', array( 'jquery', 'swiper-bundle' ), LIEL_BW_VERSION, true );

		wp_enqueue_style( 'liel-bw-fonts', self::FONTS_URL, array(), null );
		wp_enqueue_style( 'liel-bw' );
	}

	public function enqueue_editor_assets() {
		wp_enqueue_style( 'liel-bw-fonts', self::FONTS_URL, array(), null );
		wp_enqueue_style( 'swiper-bundle', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css', array(), self::SWIPER_VERSION );
		wp_enqueue_style( 'liel-bw', LIEL_BW_URL . 'assets/liel.css', array(), LIEL_BW_VERSION );
	}

	public function notice_missing_elementor() {
		echo '<div class="notice notice-warning"><p>'
			. esc_html__( 'Liel Bridal Widgets requires Elementor to be installed and active.', 'liel-bridal' )
			. '</p></div>';
	}

	public function notice_old_elementor() {
		echo '<div class="notice notice-warning"><p>'
			. esc_html__( 'Liel Bridal Widgets requires Elementor 3.5.0 or newer.', 'liel-bridal' )
			. '</p></div>';
	}
}

new Liel_Bridal_Widgets();
