<?php
/**
 * Liel Video Hero — single full-screen video hero (no carousel).
 *
 * One video, one fallback image (used as poster), optional brand-logo overlay
 * positioned bottom-center per the home-page brief. Supports BunnyCDN /
 * YouTube / Vimeo iframe embeds and direct .mp4/.webm/.ogg/.mov files.
 *
 * Slug:  video-hero
 * get_name() -> liel-video-hero
 * CSS:   assets/css/widgets/video-hero.css   (auto-registered)
 * JS:    assets/js/video-hero.js             (manual register in Liel_Plugin)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;

class Liel_Video_Hero_Widget extends Widget_Base {

	public function get_name() {
		return 'liel-video-hero';
	}

	public function get_title() {
		return __( 'Liel Video Hero', 'liel-bridal' );
	}

	public function get_icon() {
		return 'eicon-youtube';
	}

	public function get_categories() {
		return array( Liel_Plugin::CATEGORY_SLUG );
	}

	public function get_keywords() {
		return array( 'liel', 'video', 'hero', 'bunnycdn', 'youtube', 'vimeo', 'bridal' );
	}

	public function get_script_depends() {
		return array( 'liel-video-hero' );
	}

	public function get_style_depends() {
		return array( 'liel-video-hero' );
	}

	protected function register_controls() {

		/* ============================ CONTENT ============================ */
		$this->start_controls_section(
			'section_video',
			array( 'label' => __( 'Video', 'liel-bridal' ) )
		);

		$this->add_control(
			'video_url',
			array(
				'label'         => __( 'Video URL', 'liel-bridal' ),
				'description'   => __( 'BunnyCDN player URL, YouTube/Vimeo URL, or a direct .mp4/.webm file URL.', 'liel-bridal' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => 'https://player.mediadelivery.net/play/…',
				'show_external' => false,
				'default'       => array( 'url' => '' ),
				'dynamic'       => array( 'active' => true ),
			)
		);

		$this->add_control(
			'fallback_image',
			array(
				'label'       => __( 'Fallback / Poster Image', 'liel-bridal' ),
				'description' => __( 'Shown while the video loads and as the background if the video fails.', 'liel-bridal' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array( 'url' => Utils::get_placeholder_image_src() ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control( 'autoplay', array( 'label' => __( 'Autoplay', 'liel-bridal' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'return_value' => 'yes' ) );
		$this->add_control( 'loop',     array( 'label' => __( 'Loop',     'liel-bridal' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'return_value' => 'yes' ) );
		$this->add_control( 'muted',    array( 'label' => __( 'Muted',    'liel-bridal' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'return_value' => 'yes', 'description' => __( 'Required for autoplay in most browsers.', 'liel-bridal' ) ) );
		$this->add_control( 'controls', array( 'label' => __( 'Show Controls', 'liel-bridal' ), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes' ) );

		$this->end_controls_section();

		/* ============================ LOGO =============================== */
		$this->start_controls_section(
			'section_logo',
			array( 'label' => __( 'Logo Overlay', 'liel-bridal' ) )
		);

		$this->add_control(
			'logo_image',
			array(
				'label'   => __( 'Logo Image', 'liel-bridal' ),
				'description' => __( 'Optional. Centered along the bottom of the hero (per the home-page brief).', 'liel-bridal' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => '' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_responsive_control(
			'logo_width',
			array(
				'label'      => __( 'Logo Width', 'liel-bridal' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array( 'min' => 40, 'max' => 600 ),
					'%'  => array( 'min' => 5,  'max' => 60 ),
					'vw' => array( 'min' => 5,  'max' => 40 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 200 ),
				'selectors'  => array( '{{WRAPPER}} .liel-video-hero__logo' => 'width:{{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'logo_bottom_offset',
			array(
				'label'      => __( 'Distance from Bottom', 'liel-bridal' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 200 ),
					'vh' => array( 'min' => 0, 'max' => 30 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 40 ),
				'selectors'  => array( '{{WRAPPER}} .liel-video-hero__logo' => 'bottom:{{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();

		/* ============================ LAYOUT ============================= */
		$this->start_controls_section(
			'section_layout',
			array( 'label' => __( 'Layout', 'liel-bridal' ), 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_responsive_control(
			'height',
			array(
				'label'      => __( 'Height', 'liel-bridal' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'vh', 'px', '%' ),
				'range'      => array(
					'vh' => array( 'min' => 30, 'max' => 100 ),
					'px' => array( 'min' => 300, 'max' => 1200 ),
				),
				'default'    => array( 'unit' => 'vh', 'size' => 100 ),
				'selectors'  => array( '{{WRAPPER}} .liel-video-hero' => '--liel-video-hero-h:{{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'overlay',
			array(
				'label'        => __( 'Image Overlay', 'liel-bridal' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => __( 'Overlay Color', 'liel-bridal' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.25)',
				'selectors' => array( '{{WRAPPER}} .liel-video-hero__overlay' => 'background:{{VALUE}};' ),
				'condition' => array( 'overlay' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$video_url = ! empty( $settings['video_url']['url'] ) ? $settings['video_url']['url'] : '';
		$image_url = ! empty( $settings['fallback_image']['url'] ) ? $settings['fallback_image']['url'] : '';
		$logo_url  = ! empty( $settings['logo_image']['url'] ) ? $settings['logo_image']['url'] : '';

		$autoplay = ( 'yes' === $settings['autoplay'] );
		$loop     = ( 'yes' === $settings['loop'] );
		$muted    = ( 'yes' === $settings['muted'] );
		$controls = ( 'yes' === $settings['controls'] );
		$embed    = $video_url ? $this->build_embed_url( $video_url, $autoplay, $loop, $muted, $controls ) : null;

		$wrap_classes = 'liel-video-hero';
		if ( ! $controls ) {
			$wrap_classes .= ' liel-video-hero--no-controls';
		}
		?>
		<div class="<?php echo esc_attr( $wrap_classes ); ?>">

			<?php if ( $image_url ) : ?>
				<div class="liel-video-hero__bg" role="img"
					style="background-image:url('<?php echo esc_url( $image_url ); ?>');"></div>
			<?php endif; ?>

			<?php if ( $video_url ) : ?>
				<?php if ( $embed ) : ?>
					<iframe class="liel-video-hero__video liel-video-hero__video--iframe"
						src="<?php echo esc_url( $embed ); ?>"
						frameborder="0"
						allow="autoplay; encrypted-media; picture-in-picture; fullscreen"
						allowfullscreen
						loading="lazy"
						title="<?php esc_attr_e( 'Hero video', 'liel-bridal' ); ?>"></iframe>
				<?php else : ?>
					<video class="liel-video-hero__video"
						<?php echo $image_url ? 'poster="' . esc_url( $image_url ) . '"' : ''; ?>
						<?php echo $autoplay ? 'autoplay' : ''; ?>
						<?php echo $loop ? 'loop' : ''; ?>
						<?php echo $muted ? 'muted' : ''; ?>
						<?php echo $controls ? 'controls' : ''; ?>
						playsinline
						preload="metadata">
						<source src="<?php echo esc_url( $video_url ); ?>" />
					</video>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( 'yes' === $settings['overlay'] ) : ?>
				<div class="liel-video-hero__overlay"></div>
			<?php endif; ?>

			<?php if ( $logo_url ) : ?>
				<img class="liel-video-hero__logo"
					src="<?php echo esc_url( $logo_url ); ?>"
					alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
					loading="lazy" />
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Convert a supported provider URL into an iframe embed URL with the
	 * autoplay/loop/muted/controls flags applied. Returns null for direct
	 * media files so the renderer falls back to a <video> element.
	 */
	private function build_embed_url( $url, $autoplay = true, $loop = true, $muted = true, $controls = false ) {
		if ( empty( $url ) ) {
			return null;
		}

		// BunnyCDN Stream
		if ( preg_match( '#mediadelivery\.net/(?:play|embed)/([^/]+)/([^/?#]+)#i', $url, $m ) ) {
			$params = http_build_query( array(
				'autoplay'   => $autoplay ? 'true' : 'false',
				'loop'       => $loop ? 'true' : 'false',
				'muted'      => $muted ? 'true' : 'false',
				'preload'    => 'true',
				'responsive' => 'true',
				'controls'   => $controls ? 'true' : 'false',
			) );
			return sprintf( 'https://iframe.mediadelivery.net/embed/%s/%s?%s', $m[1], $m[2], $params );
		}

		// YouTube
		if ( preg_match( '#(?:youtube\.com/(?:watch\?v=|embed/)|youtu\.be/)([A-Za-z0-9_-]{6,})#i', $url, $m ) ) {
			$id = $m[1];
			$params = http_build_query( array_filter( array(
				'autoplay'       => $autoplay ? 1 : 0,
				'mute'           => $muted ? 1 : 0,
				'loop'           => $loop ? 1 : 0,
				'playlist'       => $loop ? $id : null,
				'controls'       => $controls ? 1 : 0,
				'modestbranding' => 1,
				'rel'            => 0,
				'playsinline'    => 1,
			), function( $v ) { return $v !== null; } ) );
			return sprintf( 'https://www.youtube.com/embed/%s?%s', $id, $params );
		}

		// Vimeo
		if ( preg_match( '#vimeo\.com/(?:video/)?(\d+)#i', $url, $m ) ) {
			$params = http_build_query( array(
				'autoplay'   => $autoplay ? 1 : 0,
				'loop'       => $loop ? 1 : 0,
				'muted'      => $muted ? 1 : 0,
				'controls'   => $controls ? 1 : 0,
				'playsinline'=> 1,
				'background' => ( $autoplay && $loop && $muted && ! $controls ) ? 1 : 0,
			) );
			return sprintf( 'https://player.vimeo.com/video/%s?%s', $m[1], $params );
		}

		// Direct media file -> use <video>
		if ( preg_match( '#\.(mp4|webm|ogg|ogv|mov|m4v)(?:\?|$)#i', $url ) ) {
			return null;
		}

		return $url;
	}
}
