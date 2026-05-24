<?php
/**
 * Liel Hero Slider — full-screen Swiper carousel with description + button,
 * Ken Burns zoom, navigation arrows and pagination dots.
 *
 * Mirrors the site's Elementor "Slides" hero section.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Typography;

class Liel_Hero_Slider_Widget extends Widget_Base {

	public function get_name() {
		return 'liel_hero_slider';
	}

	public function get_title() {
		return __( 'Liel Hero Slider', 'liel-bridal' );
	}

	public function get_icon() {
		return 'eicon-slides';
	}

	public function get_categories() {
		return array( 'liel' );
	}

	public function get_keywords() {
		return array( 'liel', 'hero', 'slider', 'slides', 'bridal', 'swiper' );
	}

	public function get_script_depends() {
		return array( 'swiper-bundle', 'liel-bw' );
	}

	public function get_style_depends() {
		return array( 'swiper-bundle', 'liel-bw' );
	}

	protected function register_controls() {

		/* ============================ SLIDES ============================ */
		$this->start_controls_section(
			'section_slides',
			array( 'label' => __( 'Slides', 'liel-bridal' ) )
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			array(
				'label'   => __( 'Background Image', 'liel-bridal' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$repeater->add_control(
			'description',
			array(
				'label'       => __( 'Description', 'liel-bridal' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Desert Rose F/W 2026',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'button_text',
			array(
				'label'   => __( 'Button Text', 'liel-bridal' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'SEE MORE',
			)
		);

		$repeater->add_control(
			'button_link',
			array(
				'label'       => __( 'Button Link', 'liel-bridal' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://your-link.com',
				'default'     => array( 'url' => '#' ),
			)
		);

		$this->add_control(
			'slides',
			array(
				'label'       => __( 'Slides', 'liel-bridal' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ description }}}',
				'default'     => array(
					array( 'description' => 'Desert Rose F/W 2026', 'button_text' => 'SEE MORE', 'button_link' => array( 'url' => '#' ), 'image' => array( 'url' => Utils::get_placeholder_image_src() ) ),
					array( 'description' => 'Desert Rose F/W 2026', 'button_text' => 'SEE MORE', 'button_link' => array( 'url' => '#' ), 'image' => array( 'url' => Utils::get_placeholder_image_src() ) ),
					array( 'description' => 'Desert Rose F/W 2026', 'button_text' => 'SEE MORE', 'button_link' => array( 'url' => '#' ), 'image' => array( 'url' => Utils::get_placeholder_image_src() ) ),
				),
			)
		);

		$this->end_controls_section();

		/* =========================== SETTINGS =========================== */
		$this->start_controls_section(
			'section_settings',
			array( 'label' => __( 'Slider Settings', 'liel-bridal' ) )
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
				'selectors'  => array( '{{WRAPPER}} .liel-hero' => '--liel-hero-h:{{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'arrows',
			array(
				'label'        => __( 'Navigation Arrows', 'liel-bridal' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'dots',
			array(
				'label'        => __( 'Pagination Dots', 'liel-bridal' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'        => __( 'Loop', 'liel-bridal' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'ken_burns',
			array(
				'label'        => __( 'Ken Burns Zoom', 'liel-bridal' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => __( 'Autoplay', 'liel-bridal' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'     => __( 'Autoplay Delay (ms)', 'liel-bridal' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'transition_speed',
			array(
				'label'   => __( 'Transition Speed (ms)', 'liel-bridal' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 800,
			)
		);

		$this->add_control(
			'overlay',
			array(
				'label'        => __( 'Image Overlay', 'liel-bridal' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => __( 'Overlay Color', 'liel-bridal' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.25)',
				'selectors' => array( '{{WRAPPER}} .liel-hero__overlay' => 'background:{{VALUE}};' ),
				'condition' => array( 'overlay' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* ===================== STYLE: DESCRIPTION ====================== */
		$this->start_controls_section(
			'section_style_desc',
			array(
				'label' => __( 'Description', 'liel-bridal' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => __( 'Color', 'liel-bridal' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array( '{{WRAPPER}} .liel-hero__desc' => 'color:{{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .liel-hero__desc',
			)
		);

		$this->add_responsive_control(
			'desc_spacing',
			array(
				'label'      => __( 'Spacing Below', 'liel-bridal' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 14 ),
				'selectors'  => array( '{{WRAPPER}} .liel-hero__desc' => 'margin-bottom:{{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();

		/* ======================== STYLE: BUTTON ======================== */
		$this->start_controls_section(
			'section_style_button',
			array(
				'label' => __( 'Button', 'liel-bridal' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .liel-hero__btn',
			)
		);

		$this->start_controls_tabs( 'button_tabs' );

		$this->start_controls_tab( 'button_normal', array( 'label' => __( 'Normal', 'liel-bridal' ) ) );
		$this->add_control(
			'button_color',
			array(
				'label'     => __( 'Text Color', 'liel-bridal' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array( '{{WRAPPER}} .liel-hero__btn' => 'color:{{VALUE}};border-color:{{VALUE}};' ),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'button_hover', array( 'label' => __( 'Hover', 'liel-bridal' ) ) );
		$this->add_control(
			'button_hover_color',
			array(
				'label'     => __( 'Text Color', 'liel-bridal' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#2b2926',
				'selectors' => array( '{{WRAPPER}} .liel-hero__btn:hover' => 'color:{{VALUE}};' ),
			)
		);
		$this->add_control(
			'button_hover_bg',
			array(
				'label'     => __( 'Background', 'liel-bridal' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array( '{{WRAPPER}} .liel-hero__btn:hover' => 'background:{{VALUE}};border-color:{{VALUE}};' ),
			)
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'liel-bridal' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( '{{WRAPPER}} .liel-hero__btn' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
				'separator'  => 'before',
			)
		);

		$this->end_controls_section();

		/* ======================== STYLE: ARROWS ======================== */
		$this->start_controls_section(
			'section_style_arrows',
			array(
				'label'     => __( 'Arrows', 'liel-bridal' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'arrows' => 'yes' ),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => __( 'Color', 'liel-bridal' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array( '{{WRAPPER}} .liel-hero__arrow' => 'color:{{VALUE}};' ),
			)
		);

		$this->add_responsive_control(
			'arrow_size',
			array(
				'label'     => __( 'Size', 'liel-bridal' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 16, 'max' => 80 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 38 ),
				'selectors' => array( '{{WRAPPER}} .liel-hero__arrow' => 'font-size:{{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$slides   = ! empty( $settings['slides'] ) ? $settings['slides'] : array();

		if ( empty( $slides ) ) {
			return;
		}

		$classes = 'liel-hero swiper';
		if ( 'yes' === $settings['ken_burns'] ) {
			$classes .= ' is-kenburns';
		}

		$config = array(
			'loop'      => ( 'yes' === $settings['loop'] ),
			'arrows'    => ( 'yes' === $settings['arrows'] ),
			'dots'      => ( 'yes' === $settings['dots'] ),
			'autoplay'  => ( 'yes' === $settings['autoplay'] ),
			'delay'     => isset( $settings['autoplay_speed'] ) ? absint( $settings['autoplay_speed'] ) : 5000,
			'speed'     => isset( $settings['transition_speed'] ) ? absint( $settings['transition_speed'] ) : 800,
		);
		?>
		<div class="<?php echo esc_attr( $classes ); ?>" dir="ltr" data-liel-hero="<?php echo esc_attr( wp_json_encode( $config ) ); ?>">
			<div class="swiper-wrapper">
				<?php foreach ( $slides as $index => $slide ) : ?>
					<div class="swiper-slide">
						<div class="liel-hero__bg" role="img"
							style="background-image:url('<?php echo esc_url( $slide['image']['url'] ); ?>');"></div>
						<?php if ( 'yes' === $settings['overlay'] ) : ?>
							<div class="liel-hero__overlay"></div>
						<?php endif; ?>
						<div class="liel-hero__content">
							<?php if ( ! empty( $slide['description'] ) ) : ?>
								<div class="liel-hero__desc"><?php echo esc_html( $slide['description'] ); ?></div>
							<?php endif; ?>
							<?php if ( ! empty( $slide['button_text'] ) ) : ?>
								<?php
								$url    = ! empty( $slide['button_link']['url'] ) ? $slide['button_link']['url'] : '#';
								$target = ! empty( $slide['button_link']['is_external'] ) ? ' target="_blank"' : '';
								$rel    = ! empty( $slide['button_link']['nofollow'] ) ? ' rel="nofollow"' : '';
								?>
								<a class="liel-hero__btn" href="<?php echo esc_url( $url ); ?>"<?php echo $target . $rel; // phpcs:ignore ?>>
									<?php echo esc_html( $slide['button_text'] ); ?>
								</a>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ( 'yes' === $settings['dots'] ) : ?>
				<div class="swiper-pagination"></div>
			<?php endif; ?>

			<?php if ( 'yes' === $settings['arrows'] ) : ?>
				<div class="liel-hero__arrow liel-hero__arrow--prev" aria-label="Previous">&#8249;</div>
				<div class="liel-hero__arrow liel-hero__arrow--next" aria-label="Next">&#8250;</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
