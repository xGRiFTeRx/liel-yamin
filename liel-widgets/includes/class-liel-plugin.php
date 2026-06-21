<?php
/**
 * Liel Bridal Widgets — plugin singleton.
 *
 * Wires Elementor category, widget registration, asset registration.
 * Per-widget CSS is auto-registered by looping the $widgets map;
 * per-widget JS must be added manually in register_scripts().
 *
 * Pattern adopted from Greengrass / Espressimo widget plugins.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Liel_Plugin {

	const MIN_ELEMENTOR_VERSION = '3.5.0';
	const MIN_PHP_VERSION       = '7.4';
	const CATEGORY_SLUG         = 'liel';
	const TEXT_DOMAIN           = 'liel-bridal';

	/**
	 * Slug => Widget class.
	 * Array order = order in the Elementor panel.
	 * Each slug auto-resolves to: includes/widgets/class-{slug}.php
	 * and a CSS file at assets/css/widgets/{slug}.css with handle "liel-{slug}".
	 */
	private $widgets = array(
		'hero-slider' => 'Liel_Hero_Slider_Widget',
		'video-hero'  => 'Liel_Video_Hero_Widget',
	);

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	public function init() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'notice_missing_elementor' ) );
			return;
		}
		if ( ! version_compare( ELEMENTOR_VERSION, self::MIN_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'notice_old_elementor' ) );
			return;
		}

		load_plugin_textdomain( self::TEXT_DOMAIN, false, dirname( plugin_basename( LIEL_BRIDAL_FILE ) ) . '/languages/' );

		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ), 5 );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 5 );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_editor_styles' ) );
	}

	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			self::CATEGORY_SLUG,
			array(
				'title' => __( 'Liel', 'liel-bridal' ),
				'icon'  => 'eicon-woman',
			)
		);
	}

	/**
	 * Lazy-load + register every widget class listed in $widgets.
	 */
	public function register_widgets( $widgets_manager ) {
		foreach ( $this->widgets as $slug => $class ) {
			$path = LIEL_BRIDAL_PATH . 'includes/widgets/class-' . $slug . '.php';
			if ( file_exists( $path ) ) {
				require_once $path;
				if ( class_exists( $class ) ) {
					$widgets_manager->register( new $class() );
				}
			}
		}
	}

	/**
	 * AUTO-register every per-widget stylesheet by looping the $widgets map.
	 * Handle: liel-{slug}. Depends on the shared brand stylesheet.
	 * Widgets pull these via get_style_depends() so they only load when used.
	 */
	public function register_styles() {
		// Brand fonts.
		wp_register_style(
			'liel-fonts',
			'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400&family=Frank+Ruhl+Libre:wght@300;400;500;700&family=Heebo:wght@300;400;500&display=swap',
			array(),
			null
		);

		// Shared brand tokens + buttons — every per-widget CSS depends on this.
		wp_register_style(
			'liel-shared',
			LIEL_BRIDAL_URL . 'assets/css/shared.css',
			array( 'liel-fonts' ),
			LIEL_BRIDAL_VERSION
		);

		// Per-widget CSS — auto-loop the registry.
		foreach ( $this->widgets as $slug => $class ) {
			$rel_path = 'assets/css/widgets/' . $slug . '.css';
			$abs_path = LIEL_BRIDAL_PATH . $rel_path;
			if ( file_exists( $abs_path ) ) {
				wp_register_style(
					'liel-' . $slug,
					LIEL_BRIDAL_URL . $rel_path,
					array( 'liel-shared' ),
					LIEL_BRIDAL_VERSION
				);
			}
		}
	}

	/**
	 * MANUAL register for per-widget JS. JS is NOT auto-looped — add one
	 * wp_register_script() per widget that ships JS, with handle liel-{slug}.
	 * Widgets pull these via get_script_depends().
	 */
	public function register_scripts() {
		// Swiper 8 — shared dep for any slider widget.
		wp_register_style(
			'swiper-bundle',
			'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css',
			array(),
			'8.4.7'
		);
		wp_register_script(
			'swiper-bundle',
			'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js',
			array(),
			'8.4.7',
			true
		);

		// Hero slider.
		wp_register_script(
			'liel-hero-slider',
			LIEL_BRIDAL_URL . 'assets/js/hero-slider.js',
			array( 'jquery', 'swiper-bundle' ),
			LIEL_BRIDAL_VERSION,
			true
		);

		// Video hero (single video, no carousel).
		wp_register_script(
			'liel-video-hero',
			LIEL_BRIDAL_URL . 'assets/js/video-hero.js',
			array(),
			LIEL_BRIDAL_VERSION,
			true
		);
	}

	public function enqueue_editor_styles() {
		wp_enqueue_style(
			'liel-fonts',
			'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400&family=Frank+Ruhl+Libre:wght@300;400;500;700&family=Heebo:wght@300;400;500&display=swap',
			array(),
			null
		);
		wp_enqueue_style( 'liel-shared', LIEL_BRIDAL_URL . 'assets/css/shared.css', array( 'liel-fonts' ), LIEL_BRIDAL_VERSION );
		wp_enqueue_style( 'liel-editor', LIEL_BRIDAL_URL . 'assets/css/editor.css', array( 'liel-shared' ), LIEL_BRIDAL_VERSION );
	}

	public function notice_missing_elementor() {
		echo '<div class="notice notice-warning"><p>' . esc_html__( 'Liel Bridal Widgets requires Elementor to be installed and active.', 'liel-bridal' ) . '</p></div>';
	}

	public function notice_old_elementor() {
		echo '<div class="notice notice-warning"><p>' . esc_html__( 'Liel Bridal Widgets requires Elementor 3.5.0 or newer.', 'liel-bridal' ) . '</p></div>';
	}
}
