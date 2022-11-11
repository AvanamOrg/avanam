<?php
/**
 * Base\GiveWP\Component class
 *
 * @package Base
 */

namespace Base\Give;

use Base\Component_Interface;
use Base\Base_CSS;
use function Base\webapp;
use function Base\print_webfont_preload;
use function Base\get_webfont_url;
use function add_action;
use function have_posts;
use function the_post;
use function is_search;
use function get_template_part;
use function get_post_type;

/**
 * Class for adding Woocommerce plugin support.
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
		return 'give';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_print_styles', array( $this, 'override_iframe_template_styles' ), 10 );
		add_action( 'wp_print_styles', array( $this, 'add_iframe_fonts' ), 20 );
		add_action( 'give_default_wrapper_start', array( $this, 'output_content_wrapper' ) );
		add_action( 'give_default_wrapper_end', array( $this, 'output_content_wrapper_end' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'give_styles' ), 60 );
		add_filter( 'post_class', array( $this, 'set_give_entry_class' ), 10, 3 );
		add_action( 'give_before_single_form_summary', array( $this, 'output_inner_content_wrapper' ), 2 );
		add_action( 'give_after_single_form_summary', array( $this, 'output_inner_content_wrapper_end' ) );
		add_action( 'give_single_form_summary', array( $this, 'maybe_add_title' ), 1 );
		remove_action( 'give_single_form_summary', 'give_template_single_title', 5 );
	}
	/**
	 * Adds theme output Wrapper.
	 */
	public function maybe_add_title() {
		if ( function_exists( 'give_get_meta' ) ) {
			$form_template = give_get_meta( get_the_ID(), '_give_form_template', true );
			if ( ( ! $form_template || 'legacy' === $form_template ) && webapp()->show_in_content_title() ) {
				get_template_part( 'template-parts/content/entry_header', get_post_type() );
			}
		} elseif ( webapp()->show_in_content_title() ) {
			get_template_part( 'template-parts/content/entry_header', get_post_type() );
		}
	}
	/**
	 * Adds entry class to loop items.
	 *
	 * @param array  $classes the classes.
	 * @param string $class the class.
	 * @param int    $post_id the post id.
	 */
	public function set_give_entry_class( $classes, $class, $post_id ) {
		if ( is_singular() && in_array( 'type-give_forms', $classes, true ) ) {
			$classes[] = 'entry';
			$classes[] = 'content-bg';
			$classes[] = 'single-entry';
		}
		return $classes;
	}
	/**
	 * Add some css styles for zoom_recipe_card
	 */
	public function give_styles() {
		wp_enqueue_style( 'base-givewp', get_theme_file_uri( '/assets/css/givewp.min.css' ), array(), AVANAM_VERSION );
	}
	/**
	 * Adds theme output Wrapper.
	 */
	public function output_inner_content_wrapper() {
		?>
		<div class="entry-content-wrap">
		<?php
	}
	/**
	 * Adds theme output Wrapper.
	 */
	public function output_inner_content_wrapper_end() {
		?>
		</div>
		<?php
	}
	/**
	 * Adds theme output Wrapper.
	 */
	public function output_content_wrapper() {
		webapp()->print_styles( 'base-content' );
		/**
		 * Hook for Hero Section
		 */
		do_action( 'base_hero_header' );
		?>
		<div id="primary" class="content-area">
			<div class="content-container site-container">
				<main id="main" class="site-main" role="main">
					<?php
					/**
					 * Hook for anything before main content
					 */
					do_action( 'base_before_main_content' );
					?>
					<div class="content-wrap">
					<?php
	}
	/**
	 * Adds theme end output Wrapper.
	 */
	public function output_content_wrapper_end() {
					?>
					</div>
					<?php
					/**
					 * Hook for anything after main content
					 */
					do_action( 'base_after_main_content' );
					?>
				</main><!-- #main -->
				<?php
				get_sidebar();
				?>
			</div>
		</div><!-- #primary -->
		<?php
	}
	/**
	 * Registers or enqueues google fonts.
	 */
	public function add_iframe_fonts() {
		// Enqueue Google Fonts.
		$google_fonts = apply_filters( 'base_theme_givewp_google_fonts_array', self::$google_fonts );
		if ( empty( $google_fonts ) ) {
			return '';
		}
		$link    = '';
		$sub_add = array();
		$subsets = webapp()->option( 'google_subsets' );
		foreach ( $google_fonts as $key => $gfont_values ) {
			if ( ! empty( $link ) ) {
				$link .= '%7C'; // Append a new font to the string.
			}
			$link .= $gfont_values['fontfamily'];
			if ( ! empty( $gfont_values['fontvariants'] ) ) {
				$link .= ':';
				$link .= implode( ',', $gfont_values['fontvariants'] );
			}
			if ( ! empty( $gfont_values['fontsubsets'] ) && is_array( $gfont_values['fontsubsets'] ) ) {
				foreach ( $gfont_values['fontsubsets'] as $subkey ) {
					if ( ! empty( $subkey ) && ! isset( $sub_add[ $subkey ] ) ) {
						$sub_add[] = $subkey;
					}
				}
			}
		}
		$args = array(
			'family' => $link,
		);
		if ( ! empty( $subsets ) ) {
			$available = array( 'latin-ext', 'cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'vietnamese', 'arabic', 'khmer', 'chinese', 'chinese-simplified', 'tamil', 'bengali', 'devanagari', 'hebrew', 'korean', 'thai', 'telugu' );
			foreach ( $subsets as $key => $enabled ) {
				if ( $enabled && in_array( $key, $available, true ) ) {
					if ( 'chinese' === $key ) {
						$key = 'chinese-traditional';
					}
					if ( ! isset( $sub_add[ $key ] ) ) {
						$sub_add[] = $key;
					}
				}
			}
			if ( $sub_add ) {
				$args['subset'] = implode( ',', $sub_add );
			}
		}
		if ( apply_filters( 'base_givewp_display_swap_google_fonts', true ) ) {
			$args['display'] = 'swap';
		}
		$google_fonts_url = add_query_arg( apply_filters( 'base_theme_givewp_google_fonts_query_args', $args ), 'https://fonts.googleapis.com/css' );
		if ( ! empty( $google_fonts_url ) ) {
			if ( webapp()->option( 'load_fonts_local' ) ) {
				if ( webapp()->option( 'preload_fonts_local' ) && apply_filters( 'base_local_fonts_preload', true ) ) {
					print_webfont_preload( $google_fonts_url );
				}
				wp_enqueue_style(
					'base-givewp-iframe-fonts',
					get_webfont_url( $google_fonts_url ),
					'give-sequoia-template-css',
					AVANAM_VERSION
				);
			} else {
				wp_enqueue_style( 'base-givewp-iframe-fonts', $google_fonts_url, 'give-sequoia-template-css', AVANAM_VERSION ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
			}
		}
	}
	/**
	 * Add basic theme styling to iframe.
	 */
	public function override_iframe_template_styles() {
		$css                    = new Base_CSS();
		$media_query            = array();
		$media_query['mobile']  = apply_filters( 'base_mobile_media_query', '(max-width: 767px)' );
		$media_query['tablet']  = apply_filters( 'base_tablet_media_query', '(max-width: 1024px)' );
		$media_query['desktop'] = apply_filters( 'base_desktop_media_query', '(min-width: 1025px)' );
		// Globals.
		$css->set_selector( ':root' );
		$css->add_property( '--global-palette1', webapp()->palette_option( 'palette1' ) );
		$css->add_property( '--global-palette2', webapp()->palette_option( 'palette2' ) );
		$css->add_property( '--global-palette3', webapp()->palette_option( 'palette3' ) );
		$css->add_property( '--global-palette4', webapp()->palette_option( 'palette4' ) );
		$css->add_property( '--global-palette5', webapp()->palette_option( 'palette5' ) );
		$css->add_property( '--global-palette6', webapp()->palette_option( 'palette6' ) );
		$css->add_property( '--global-palette7', webapp()->palette_option( 'palette7' ) );
		$css->add_property( '--global-palette8', webapp()->palette_option( 'palette8' ) );
		$css->add_property( '--global-palette9', webapp()->palette_option( 'palette9' ) );
		$css->add_property( '--global-palette-highlight', $css->render_color( webapp()->sub_option( 'link_color', 'highlight' ) ) );
		$css->add_property( '--global-palette-highlight-alt', $css->render_color( webapp()->sub_option( 'link_color', 'highlight-alt' ) ) );
		$css->add_property( '--global-palette-highlight-alt2', $css->render_color( webapp()->sub_option( 'link_color', 'highlight-alt2' ) ) );
		$css->add_property( '--global-palette-btn-bg', $css->render_color( webapp()->sub_option( 'buttons_background', 'color' ) ) );
		$css->add_property( '--global-palette-btn-bg-hover', $css->render_color( webapp()->sub_option( 'buttons_background', 'hover' ) ) );
		$css->add_property( '--global-palette-btn', $css->render_color( webapp()->sub_option( 'buttons_color', 'color' ) ) );
		$css->add_property( '--global-palette-btn-hover', $css->render_color( webapp()->sub_option( 'buttons_color', 'hover' ) ) );
		$css->add_property( '--global-body-font-family', $css->render_font_family( webapp()->option( 'base_font' ), '' ) );
		$css->add_property( '--global-heading-font-family', $css->render_font_family( webapp()->option( 'heading_font' ) ) );
		$css->add_property( '--global-fallback-font', apply_filters( 'base_theme_global_typography_fallback', 'sans-serif' ) );
		$css->add_property( '--global-display-fallback-font', apply_filters( 'base_theme_global_display_typography_fallback', 'sans-serif' ) );

		$css->set_selector( 'body' );
		$css->add_property( 'margin', '10px' );
		$css->add_property( 'font-family', 'var( --global-body-font-family )' );
		$css->add_property( 'color', 'var(--global-palette4 )' );

		$css->set_selector( '.give-embed-form, .give-embed-receipt' );
		$css->add_property( 'color', 'var(--global-palette5 )' );
		$css->add_property( 'background-color', 'var(--global-palette9 )' );
		$css->add_property( 'box-shadow', '0px 15px 25px -10px rgb(0 0 0 / 5%)' );
		$css->add_property( 'border-radius', '.25rem' );
		$css->add_property( 'border', '1px solid var( --global-palette7 )' );

		$css->set_selector( 'body.give-form-templates .give-form-navigator' );
		$css->add_property( 'border', '1px solid transparent' );
		$css->add_property( 'background', 'var(--global-palette8 )' );
		$css->set_selector( '.form-footer .secure-notice' );
		$css->add_property( 'background', 'var(--global-palette8 )' );
		$css->add_property( 'border-top', '1px solid var( --global-palette7 )' );
		$css->add_property( 'color', 'var(--global-palette6 )' );
		$css->set_selector( 'body.give-form-templates .give-form-navigator.nav-visible' );
		$css->add_property( 'border', '1px solid var( --global-palette7 )' );
		$css->set_selector( '.form-footer .navigator-tracker .step-tracker' );
		$css->add_property( 'background', 'var(--global-palette7 )' );
		$css->set_selector( '.form-footer .navigator-tracker .step-tracker.current' );
		$css->add_property( 'background', 'var(--global-palette6 )' );
		$css->set_selector( '.payment #give_purchase_form_wrap');
		$css->add_property( 'background', 'var(--global-palette8 )' );
		$css->set_selector(
			'body.give-form-templates,
			body.give-form-templates .give-btn,
			body.give-form-templates .choose-amount .give-donation-amount .give-amount-top,
			body.give-form-templates #give-recurring-form .form-row input[type=email],
			body.give-form-templates #give-recurring-form .form-row input[type=password],
			body.give-form-templates #give-recurring-form .form-row input[type=tel],
			body.give-form-templates #give-recurring-form .form-row input[type=text],
			body.give-form-templates #give-recurring-form .form-row input[type=url],
			body.give-form-templates #give-recurring-form .form-row textarea, .give-input-field-wrapper,
			body.give-form-templates .give-square-cc-fields,
			body.give-form-templates .give-stripe-cc-field,
			body.give-form-templates .give-stripe-single-cc-field-wrap,
			body.give-form-templates form.give-form .form-row input[type=email],
			body.give-form-templates form.give-form .form-row input[type=password],
			body.give-form-templates form.give-form .form-row input[type=tel],
			body.give-form-templates form.give-form .form-row input[type=text],
			body.give-form-templates form.give-form .form-row input[type=url],
			body.give-form-templates form.give-form .form-row textarea,
			body.give-form-templates form[id*=give-form] .form-row input[type=email],
			body.give-form-templates form[id*=give-form] .form-row input[type=email].required,
			body.give-form-templates form[id*=give-form] .form-row input[type=password],
			body.give-form-templates form[id*=give-form] .form-row input[type=password].required,
			body.give-form-templates form[id*=give-form] .form-row input[type=tel],
			body.give-form-templates form[id*=give-form] .form-row input[type=tel].required,
			body.give-form-templates form[id*=give-form] .form-row input[type=text],
			body.give-form-templates form[id*=give-form] .form-row input[type=text].required,
			body.give-form-templates form[id*=give-form] .form-row input[type=url],
			body.give-form-templates form[id*=give-form] .form-row input[type=url].required,
			body.give-form-templates form[id*=give-form] .form-row textarea,
			body.give-form-templates form[id*=give-form] .form-row textarea.required'
		);
		$css->add_property( 'font-family', 'var( --global-body-font-family )' );
		$css->set_selector( '.give-stripe-becs-mandate-acceptance-text, .give-stripe-sepa-mandate-acceptance-text, p, .give-form-navigator>.title, .give-form-navigator>.back-btn,.payment .subheading' );
		$css->add_property( 'color', 'var(--global-palette5 )' );
		$css->set_selector( 'h1,h2,h3,h4,h5,h6, .payment .heading' );
		$css->add_property( 'color', 'var(--global-palette3 )' );
		$css->set_selector( '.advance-btn, .download-btn, .give-submit' );
		$css->add_property( 'text-transform', 'uppercase' );
		$css->add_property( 'font-weight', '600' );
		$css->add_property( 'font-size', '16px' );
		$css->add_property( 'padding', '14px 30px !important' );
		self::$google_fonts = $css->fonts_output();
		wp_add_inline_style( 'give-sequoia-template-css', $css->css_output() );
	}
}
