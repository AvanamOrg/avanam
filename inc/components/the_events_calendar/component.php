<?php
/**
 * Base\The_Events_Calendar\Component class
 *
 * @package Base
 */

namespace Base\The_Events_Calendar;

use Base\Component_Interface;
use Base\Base_CSS;
use Base_Blocks_Frontend;
use function Base\webapp;
use function add_action;
use function have_posts;
use function the_post;
use function apply_filters;
use function get_template_part;
use function tribe_get_events_link;
use function tribe_get_event_label_plural;


/**
 * Class for adding The_Events_Calendar plugin support.
 */
class Component implements Component_Interface {

	/**
	 * Associative array of Google Fonts to load.
	 *
	 * Do not access this property directly, instead use the `get_google_fonts()` method.
	 *
	 * @var array
	 */
	protected static $google_fonts = array();

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'the_events_calendar';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'base_tribe_events_before_main_tag', array( $this, 'tribe_wapper_before' ) );
		add_action( 'base_tribe_events_after_main_tag', array( $this, 'tribe_wapper_after' ) );
		add_action( 'base_tribe_archive_events_before_template', array( $this, 'tribe_archive_wapper_before' ), 5 );
		add_action( 'base_tribe_archive_events_after_template', array( $this, 'tribe_archive_wapper_after' ) );
		add_action( 'base_tribe_events_header', array( $this, 'tribe_event_title_area' ), 10, 2 );
		add_filter( 'base_dynamic_css', array( $this, 'dynamic_css' ), 20 );
		add_action( 'wp_head', array( $this, 'frontend_gfonts' ), 80 );
		add_action( 'wp_enqueue_scripts', array( $this, 'tribe_styles' ), 60 );
		add_filter( 'base_theme_options_defaults', array( $this, 'add_option_defaults' ) );
		add_filter( 'tribe_default_events_block_single_classes', array( $this, 'events_template_classes' ) );
	}
	/**
	 * Add event template classes.
	 *
	 * @param array $classes template classes.
	 * @return array
	 */
	public function events_template_classes( $classes ) {
		$classes[] = 'entry';
		$classes[] = 'content-bg';
		return $classes;
	}
	/**
	 * Generates the dynamic css based on customizer options.
	 *
	 * @param string $css any custom css.
	 * @return string
	 */
	public function dynamic_css( $css ) {
		$generated_css = $this->generate_events_css();
		if ( ! empty( $generated_css ) ) {
			$css .= "\n/* Base Events CSS */\n" . $generated_css;
		}
		return $css;
	}
	/**
	 * Generates the dynamic css based on page options.
	 *
	 * @return string
	 */
	public function generate_events_css() {
		$css                    = new Base_CSS();
		$media_query            = array();
		$media_query['mobile']  = apply_filters( 'base_mobile_media_query', '(max-width: 767px)' );
		$media_query['tablet']  = apply_filters( 'base_tablet_media_query', '(max-width: 1024px)' );
		$media_query['desktop'] = apply_filters( 'base_desktop_media_query', '(min-width: 1025px)' );
		$css->set_selector( ':root' );
		$css->add_property( '--tec-color-background-events', 'var(--global-palette9)' );
		$css->add_property( '--tec-color-text-event-date', 'var(--global-palette3)' );
		$css->add_property( '--tec-color-text-event-title', 'var(--global-palette3)' );
		$css->add_property( '--tec-color-text-events-title', 'var(--global-palette3)' );
		$css->add_property( '--tec-color-background-view-selector-list-item-hover', 'var(--global-palette7)' );
		$css->add_property( '--tec-color-background-secondary', 'var(--global-palette7)' );
		$css->add_property( '--tec-color-link-primary', 'var(--global-palette3)' );
		$css->add_property( '--tec-color-icon-active', 'var(--global-palette3)' );
		$css->add_property( '--tec-color-day-marker-month', 'var(--global-palette4)' );
		$css->add_property( '--tec-color-border-active-month-grid-hover', 'var(--global-palette5)' );
		$css->add_property( '--tec-color-accent-primary', 'var(--global-palette1)' );
		$css->add_property( '--tec-color-border-default', 'var(--global-gray-400)' );
		// Events Hero Title Area.
		$css->set_selector( '.tribe_events-hero-section .entry-hero-container-inner' );
		$css->render_background( webapp()->sub_option( 'tribe_events_title_background', 'desktop' ), $css );
		$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'tribe_events_title_top_border', 'desktop' ) ) );
		$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'tribe_events_title_bottom_border', 'desktop' ) ) );
		$css->set_selector( '.entry-hero.tribe_events-hero-section .entry-header' );
		$css->add_property( 'min-height', $css->render_range( webapp()->option( 'tribe_events_title_height' ), 'desktop' ) );
		$css->set_selector( '.tribe_events-hero-section .hero-section-overlay' );
		$css->add_property( 'background', $css->render_color_or_gradient( webapp()->sub_option( 'tribe_events_title_overlay_color', 'color' ) ) );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '.tribe_events-hero-section .entry-hero-container-inner' );
		$css->render_background( webapp()->sub_option( 'tribe_events_title_background', 'tablet' ), $css );
		$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'tribe_events_title_top_border', 'tablet' ) ) );
		$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'tribe_events_title_bottom_border', 'tablet' ) ) );
		$css->set_selector( '.entry-hero.tribe_events-hero-section .entry-header' );
		$css->add_property( 'min-height', $css->render_range( webapp()->option( 'tribe_events_title_height' ), 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '.tribe_events-hero-section .entry-hero-container-inner' );
		$css->render_background( webapp()->sub_option( 'tribe_events_title_background', 'mobile' ), $css );
		$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'tribe_events_title_top_border', 'mobile' ) ) );
		$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'tribe_events_title_bottom_border', 'mobile' ) ) );
		$css->set_selector( '.entry-hero.tribe_events-hero-section .entry-header' );
		$css->add_property( 'min-height', $css->render_range( webapp()->option( 'tribe_events_title_height' ), 'mobile' ) );
		$css->stop_media_query();
		// Events Title Font.
		$css->set_selector( '.single-tribe_events #inner-wrap .tribe_events-title h1' );
		$css->render_font( webapp()->option( 'tribe_events_title_font' ), $css, 'heading' );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '.single-tribe_events #inner-wrap .tribe_events-title h1' );
		$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'tribe_events_title_font' ), 'tablet' ) );
		$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'tribe_events_title_font' ), 'tablet' ) );
		$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'tribe_events_title_font' ), 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '.single-tribe_events #inner-wrap .tribe_events-title h1' );
		$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'tribe_events_title_font' ), 'mobile' ) );
		$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'tribe_events_title_font' ), 'mobile' ) );
		$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'tribe_events_title_font' ), 'mobile' ) );
		$css->stop_media_query();
		// Events Title Breadcrumbs.
		$css->set_selector( '.tribe_events-title .base-breadcrumbs' );
		$css->render_font( webapp()->option( 'tribe_events_title_breadcrumb_font' ), $css );
		$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'tribe_events_title_breadcrumb_color', 'color' ) ) );
		$css->set_selector( '.tribe_events-title .base-breadcrumbs a:hover' );
		$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'tribe_events_title_breadcrumb_color', 'hover' ) ) );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '.tribe_events-title .base-breadcrumbs' );
		$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'tribe_events_title_breadcrumb_font' ), 'tablet' ) );
		$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'tribe_events_title_breadcrumb_font' ), 'tablet' ) );
		$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'tribe_events_title_breadcrumb_font' ), 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '.tribe_events-title .base-breadcrumbs' );
		$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'tribe_events_title_breadcrumb_font' ), 'mobile' ) );
		$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'tribe_events_title_breadcrumb_font' ), 'mobile' ) );
		$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'tribe_events_title_breadcrumb_font' ), 'mobile' ) );
		$css->stop_media_query();
		// Events Title Back Link.
		$css->set_selector( '.tribe_events-title .tribe-events-back a' );
		$css->render_font( webapp()->option( 'tribe_events_title_back_link_font' ), $css );
		$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'tribe_events_title_back_link_color', 'color' ) ) );
		$css->set_selector( '.tribe_events-title .tribe-events-back a:hover' );
		$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'tribe_events_title_back_link_color', 'hover' ) ) );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '.tribe_events-title .tribe-events-back a' );
		$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'tribe_events_title_back_link_font' ), 'tablet' ) );
		$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'tribe_events_title_back_link_font' ), 'tablet' ) );
		$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'tribe_events_title_back_link_font' ), 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '.tribe_events-title .tribe-events-back a' );
		$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'tribe_events_title_back_link_font' ), 'mobile' ) );
		$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'tribe_events_title_back_link_font' ), 'mobile' ) );
		$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'tribe_events_title_back_link_font' ), 'mobile' ) );
		$css->stop_media_query();
		// Single Event Backgrounds.
		$css->set_selector( 'body.single-tribe_events' );
		$css->render_background( webapp()->sub_option( 'tribe_events_background', 'desktop' ), $css );
		$css->set_selector( 'body.single-tribe_events .content-bg, body.content-style-unboxed.single-tribe_events .site' );
		$css->render_background( webapp()->sub_option( 'tribe_events_content_background', 'desktop' ), $css );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( 'body.single-tribe_events' );
		$css->render_background( webapp()->sub_option( 'tribe_events_background', 'tablet' ), $css );
		$css->set_selector( 'body.single-tribe_events .content-bg, body.content-style-unboxed.single-tribe_events .site' );
		$css->render_background( webapp()->sub_option( 'tribe_events_content_background', 'tablet' ), $css );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( 'body.single-tribe_events' );
		$css->render_background( webapp()->sub_option( 'tribe_events_background', 'mobile' ), $css );
		$css->set_selector( 'body.single-tribe_events .content-bg, body.content-style-unboxed.single-tribe_events .site' );
		$css->render_background( webapp()->sub_option( 'tribe_events_content_background', 'mobile' ), $css );
		$css->stop_media_query();
		// Events Backgrounds.
		$css->set_selector( 'body.post-type-archive-tribe_events .site, body.post-type-archive-tribe_events.content-style-unboxed .site' );
		$css->render_background( webapp()->sub_option( 'tribe_events_archive_content_background', 'desktop' ), $css );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( 'body.post-type-archive-tribe_events .site, body.post-type-archive-tribe_events.content-style-unboxed .site' );
		$css->render_background( webapp()->sub_option( 'tribe_events_archive_content_background', 'tablet' ), $css );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( 'body.post-type-archive-tribe_events .site, body.post-type-archive-tribe_events.content-style-unboxed .site' );
		$css->render_background( webapp()->sub_option( 'tribe_events_archive_content_background', 'mobile' ), $css );
		$css->stop_media_query();

		self::$google_fonts = $css->fonts_output();
		return $css->css_output();
	}
	/**
	 * Outputs the theme wrappers.
	 */
	public function tribe_event_title_area( $area = 'normal', $template_class = null ) {
		$enable = webapp()->option( 'tribe_events_title' );
		if ( ! $enable ) {
			return;
		}
		$placement = webapp()->option( 'tribe_events_title_layout' );
		if ( $area !== $placement ) {
			return;
		}
		if ( 'normal' === $area ) {
			if ( ! webapp()->show_in_content_title() ) {
				return;
			}
			$classes   = array();
			$classes[] = 'entry-header';
			$classes[] = 'tribe_events-title';
			$classes[] = 'title-align-' . ( webapp()->sub_option( 'tribe_events_title_align', 'desktop' ) ? webapp()->sub_option( 'tribe_events_title_align', 'desktop' ) : 'inherit' );
			$classes[] = 'title-tablet-align-' . ( webapp()->sub_option( 'tribe_events_title_align', 'tablet' ) ? webapp()->sub_option( 'tribe_events_title_align', 'tablet' ) : 'inherit' );
			$classes[] = 'title-mobile-align-' . ( webapp()->sub_option( 'tribe_events_title_align', 'mobile' ) ? webapp()->sub_option( 'tribe_events_title_align', 'mobile' ) : 'inherit' );
			?>
			<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php
		}
		$elements = webapp()->option( 'tribe_events_title_elements' );
		if ( isset( $elements ) && is_array( $elements ) && ! empty( $elements ) ) {
			foreach ( $elements as $item ) {
				if ( webapp()->sub_option( 'tribe_events_title_element_' . $item, 'enabled' ) ) {
					switch ( $item ) {
						case 'breadcrumb':
							$template = apply_filters( 'base_title_elements_template_path', 'template-parts/title/' . $item, $item, $area );
							get_template_part( $template );
							break;
						case 'back_link':
							if ( null !== $template_class & is_object( $template_class ) ) {
								$template_class->template( 'single-event/back-link' );
							} else {
								$template = apply_filters( 'base_title_elements_template_path', 'template-parts/title/' . $item, $item, $area );
								get_template_part( $template );
							}
							break;
						case 'title':
							do_action( 'base_single_before_entry_title' );
							if ( null !== $template_class & is_object( $template_class ) ) {
								$template_class->template( 'single-event/title' );
							} else {
								the_title( '<h1 class="entry-title tribe-events-single-event-title">', '</h1>' );
							}
							do_action( 'base_single_after_entry_title' );
							break;
						default:
							# code...
							break;
					}
				}
			}
		}
		if ( 'normal' === $area ) {
			?>
			</div>
			<?php
		}
	}
	/**
	 * Add Defaults
	 *
	 * @access public
	 * @param array $defaults registered option defaults with base theme.
	 * @return array
	 */
	public function add_option_defaults( $defaults ) {
		// event.
		$event_addons = array(
			'tribe_events_title'              => true,
			'tribe_events_title_layout'       => 'normal',
			'tribe_events_title_inner_layout' => 'standard',
			'tribe_events_title_height'       => array(
				'size' => array(
					'mobile'  => '',
					'tablet'  => '',
					'desktop' => '',
				),
				'unit' => array(
					'mobile'  => 'px',
					'tablet'  => 'px',
					'desktop' => 'px',
				),
			),
			'tribe_events_title_font' => array(
				'size' => array(
					'desktop' => '',
				),
				'lineHeight' => array(
					'desktop' => '',
				),
				'family'  => 'inherit',
				'google'  => false,
				'weight'  => '',
				'variant' => '',
				'color'   => '',
			),
			'tribe_events_layout'           => 'narrow',
			'tribe_events_content_style'    => 'boxed',
			'tribe_events_vertical_padding' => 'show',
			'tribe_events_sidebar_id'       => 'sidebar-primary',
			'tribe_events_title_elements'   => array( 'breadcrumb', 'back_link', 'title' ),
			'tribe_events_title_element_title' => array(
				'enabled' => true,
			),
			'tribe_events_title_element_breadcrumb' => array(
				'enabled' => false,
				'show_title' => true,
			),
			'tribe_events_title_element_back_link' => array(
				'enabled' => true,
			),
			'tribe_events_background'         => '',
			'tribe_events_content_background' => '',
			'tribe_events_title_background'   => array(
				'desktop' => array(
					'color' => '',
				),
			),
			'tribe_events_title_featured_image' => false,
			'tribe_events_title_overlay_color'  => array(
				'color' => '',
			),
			'tribe_events_title_top_border'    => array(),
			'tribe_events_title_bottom_border' => array(),
			'tribe_events_title_align'         => array(
				'mobile'  => '',
				'tablet'  => '',
				'desktop' => '',
			),
			'tribe_events_title_breadcrumb_color' => array(
				'color' => '',
				'hover' => '',
			),
			'tribe_events_title_breadcrumb_font'   => array(
				'size' => array(
					'desktop' => '',
				),
				'lineHeight' => array(
					'desktop' => '',
				),
				'family'  => 'inherit',
				'google'  => false,
				'weight'  => '',
				'variant' => '',
			),
			'tribe_events_title_back_link_color' => array(
				'color' => '',
				'hover' => '',
			),
			'tribe_events_title_back_link_font'   => array(
				'size' => array(
					'desktop' => '',
				),
				'lineHeight' => array(
					'desktop' => '',
				),
				'family'  => 'inherit',
				'google'  => false,
				'weight'  => '',
				'variant' => '',
			),
			'tribe_events_archive_title'            => false,
			'tribe_events_archive_title_layout'     => 'normal',
			'tribe_events_archive_layout'           => 'normal',
			'tribe_events_archive_sidebar_id'       => 'sidebar-primary',
			'tribe_events_archive_content_style'    => 'unboxed',
			'tribe_events_archive_vertical_padding' => 'show',
		);
		$defaults = array_merge(
			$defaults,
			$event_addons
		);
		return $defaults;
	}
	/**
	 * Outputs the theme wrappers.
	 */
	public function tribe_archive_wapper_before() {
		?>
		<div id="primary" class="content-area">
			<div class="content-container site-container">
		<?php
	}
	/**
	 * Outputs the theme wrappers.
	 */
	public function tribe_archive_wapper_after() {
		get_sidebar();
		?>
			</div>
		</div>
		<?php
	}
	/**
	 * Outputs the theme wrappers.
	 */
	public function tribe_wapper_before() {
		/**
		* Hook for Hero Section
		*/
		do_action( 'base_hero_header' );
		?>
		<div id="primary" class="content-area">
			<div class="content-container site-container">
		<?php
	}
	/**
	 * Outputs the theme wrappers.
	 */
	public function tribe_wapper_after() {
		get_sidebar();
		?>
			</div>
		</div>
		<?php
	}
	/**
	 * Add some css styles for Tribe Events
	 */
	public function tribe_styles() {
		wp_enqueue_style( 'base-tribe', get_theme_file_uri( '/assets/css/tribe-events.min.css' ), array(), AVANAM_VERSION );
	}
	/**
	 * Enqueue Frontend Fonts
	 */
	public function frontend_gfonts() {
		if ( empty( self::$google_fonts ) ) {
			return;
		}
		if ( class_exists( 'Base_Blocks_Frontend' ) ) {
			$ktblocks_instance = Base_Blocks_Frontend::get_instance();
			foreach ( self::$google_fonts as $key => $font ) {
				if ( ! array_key_exists( $key, $ktblocks_instance::$gfonts ) ) {
					$add_font = array(
						'fontfamily'   => $font['fontfamily'],
						'fontvariants' => ( isset( $font['fontvariants'] ) && ! empty( $font['fontvariants'] ) && is_array( $font['fontvariants'] ) ? $font['fontvariants'] : array() ),
						'fontsubsets'  => ( isset( $font['fontsubsets'] ) && ! empty( $font['fontsubsets'] ) && is_array( $font['fontsubsets'] ) ? $font['fontsubsets'] : array() ),
					);
					$ktblocks_instance::$gfonts[ $key ] = $add_font;
				} else {
					foreach ( $font['fontvariants'] as $variant ) {
						if ( ! in_array( $variant, $ktblocks_instance::$gfonts[ $key ]['fontvariants'], true ) ) {
							array_push( $ktblocks_instance::$gfonts[ $key ]['fontvariants'], $variant );
						}
					}
				}
			}
		} else {
			add_filter( 'base_theme_google_fonts_array', array( $this, 'filter_in_fonts' ) );
		}
	}
	/**
	 * Filters in pro fronts for output with free.
	 *
	 * @param array $font_array any custom css.
	 * @return array
	 */
	public function filter_in_fonts( $font_array ) {
		// Enqueue Google Fonts.
		foreach ( self::$google_fonts as $key => $font ) {
			if ( ! array_key_exists( $key, $font_array ) ) {
				$add_font = array(
					'fontfamily'   => $font['fontfamily'],
					'fontvariants' => ( isset( $font['fontvariants'] ) && ! empty( $font['fontvariants'] ) && is_array( $font['fontvariants'] ) ? $font['fontvariants'] : array() ),
					'fontsubsets'  => ( isset( $font['fontsubsets'] ) && ! empty( $font['fontsubsets'] ) && is_array( $font['fontsubsets'] ) ? $font['fontsubsets'] : array() ),
				);
				$font_array[ $key ] = $add_font;
			} else {
				foreach ( $font['fontvariants'] as $variant ) {
					if ( ! in_array( $variant, $font_array[ $key ]['fontvariants'], true ) ) {
						array_push( $font_array[ $key ]['fontvariants'], $variant );
					}
				}
			}
		}
		return $font_array;
	}
}
