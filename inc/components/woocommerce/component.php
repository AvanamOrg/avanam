<?php
/**
 * Base\Woocommerce\Component class
 *
 * @package Base
 */

namespace Base\Woocommerce;

use Base\Component_Interface;
use ElementorPro;
use function Base\webapp;
use function add_action;
use function add_theme_support;
use function have_posts;
use function the_post;
use function is_search;
use function get_template_part;
use function get_post_type;
use function woocommerce_catalog_ordering;
use function woocommerce_result_count;
use WPSEO_Primary_Term;

/**
 * Class for adding Woocommerce plugin support.
 */
class Component implements Component_Interface {

	/**
	 * Holds the bool for cart in header.
	 *
	 * @var bool based on the theme settings.
	 */
	public static $cart_in_header = null;

	/**
	 * Holds the bool for mini cart in header.
	 *
	 * @var bool based on the theme settings.
	 */
	public static $show_mini_cart = false;

	/**
	 * Holds the bool for showing the cart total.
	 *
	 * @var bool based on the theme settings.
	 */
	public static $show_cart_total = false;

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'woocommerce';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {

		add_action( 'wp_enqueue_scripts', array( $this, 'action_enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'action_enqueue_product_scripts' ), 1 );

		add_action( 'after_setup_theme', array( $this, 'action_add_woocommerce_support' ) );
		// Remove default wrappers.
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper' );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end' );
		// Remove Default Woo Sidebar.
		remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar' );
		// Remove default Woo archive title meta.
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
		// Remove default description output.
		add_action( 'woocommerce_before_main_content', array( $this, 'action_remove_normal_archive_description' ) );
		// Remove Breadcrumbs.
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
		// Add Product Above Area Breadcrumb.
		add_action( 'woocommerce_before_single_product', array( $this, 'output_product_above' ), 20 );
		// Add Product Above Area title.
		add_action( 'woocommerce_before_main_content', array( $this, 'output_product_above_title' ), 5 );
		// Perhaps remove product tab headings.
		add_filter( 'woocommerce_product_description_heading', array( $this, 'remove_product_tab_heading' ) );
		add_filter( 'woocommerce_product_additional_information_heading', array( $this, 'remove_product_tab_heading' ) );
		// Add custom wrappers.
		add_action( 'woocommerce_before_main_content', array( $this, 'output_content_wrapper' ) );
		add_action( 'woocommerce_after_main_content', array( $this, 'output_content_wrapper_end' ) );

		add_filter( 'woocommerce_single_product_image_gallery_classes', array( $this, 'single_product_image_initial_ratio' ), 20 );
		// Remove Default Title.
		add_filter( 'woocommerce_show_page_title', '__return_false', 20 );
		// Add Woo archive title meta.
		add_action( 'woocommerce_before_shop_loop', array( $this, 'archive_loop_top' ), 20 );
		// Add Single product controls.
		add_action( 'woocommerce_before_single_product', array( $this, 'single_product_layout' ), 20 );
		// Add Single product reviews css.
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'single_product_comment_css' ), 5 );
		// Loop Start.
		add_filter( 'woocommerce_product_loop_start', array( $this, 'product_loop_start' ), 5 );
		add_filter( 'base_blocks_carousel_woocommerce_product_loop_start', array( $this, 'product_loop_start' ), 5 );
		// Add Post grid class.
		add_filter( 'post_class', array( $this, 'add_woo_entry_classes' ), 20, 3 );
		// Add category grid class.
		add_filter( 'product_cat_class', array( $this, 'add_woo_cat_entry_classes' ), 20, 3 );
		// Remove standard link open for products.
		remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open' );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
		// Image Link.
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'archive_loop_image_wrap_open' ), 1 );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'archive_loop_image_link_open' ), 5 );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'archive_loop_second_image' ), 30 );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'archive_loop_image_link_close' ), 50 );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'archive_loop_product_action' ), 90 );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'archive_loop_image_wrap_close' ), 91 );
		// Content Wrap.
		add_action( 'woocommerce_shop_loop_item_title', array( $this, 'archive_content_wrap_start' ), 5 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'archive_content_wrap_end' ), 50 );
		add_action( 'woocommerce_shop_loop_subcategory_title', array( $this, 'archive_content_wrap_start' ), 5 );
		add_action( 'woocommerce_after_subcategory_title', array( $this, 'archive_content_wrap_end' ), 50 );
		// Title Link.
		remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title' );
		add_action( 'woocommerce_shop_loop_item_title', array( $this, 'archive_title_with_link' ) );
		// Excerpt.
		add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'archive_excerpt' ), 20 );
		add_filter( 'archive_woocommerce_short_description', 'wptexturize' );
		add_filter( 'archive_woocommerce_short_description', 'wpautop' );
		add_filter( 'archive_woocommerce_short_description', 'shortcode_unautop' );
		add_filter( 'archive_woocommerce_short_description', 'do_shortcode', 11 );
		// Add to cart wrap.
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'archive_action_wrap_start' ), 5 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'archive_action_wrap_end' ), 20 );
		// Add to cart.
		add_filter( 'woocommerce_product_loop_start', array( $this, 'add_filter_for_add_to_cart_link' ) );
		add_filter( 'woocommerce_product_loop_end', array( $this, 'remove_filter_for_add_to_cart_link' ) );
		// Custom templates for blocks.
		add_action( 'base_woocommerce_template_before_block_loop', array( $this, 'add_filter_for_add_to_cart_link' ) );
		add_action( 'base_woocommerce_template_after_block_loop', array( $this, 'add_filter_for_add_to_cart_link' ) );
		// My Account.
		add_action( 'woocommerce_before_account_navigation', array( $this, 'myaccount_nav_wrap_start' ), 2 );
		add_action( 'woocommerce_before_account_navigation', array( $this, 'myaccount_nav_avatar' ), 20 );
		add_action( 'woocommerce_after_account_navigation', array( $this, 'myaccount_nav_wrap_end' ), 50 );

		// Cart.
		add_action( 'woocommerce_before_cart', array( $this, 'cart_form_wrap_before' ) );
		add_action( 'woocommerce_after_cart', array( $this, 'cart_form_wrap_after' ) );
		add_action( 'woocommerce_before_cart_table', array( $this, 'cart_summary_title' ) );
		// Move Cross sell below.
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
		add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display' );
		// Change cross sells columns and limit.
		add_filter( 'woocommerce_cross_sells_columns', array( $this, 'cross_sell_columns' ), 20 );
		add_filter( 'woocommerce_cross_sells_total', array( $this, 'cross_sell_limit' ), 20 );

		add_action( 'base_before_main_content', array( $this, 'wc_print_notices_none_woo' ) );
		// Add Fragment Support.
		add_action( 'init', array( $this, 'check_for_fragment_support' ) );
		// Check again for Fragment Support in conditional header.
		add_action( 'wp', array( $this, 'check_conditional_for_fragment_support' ), 2 );

		// Add my Account Navigation Classes.
		add_filter( 'body_class', array( $this, 'my_account_body_classes' ) );

		// Add single product Button Classes.
		add_filter( 'body_class', array( $this, 'single_product_body_classes' ) );

		// Add Store Notice Body Class.
		add_filter( 'body_class', array( $this, 'woo_extra_body_classes' ) );
		// Filter product blocks grid html.
		add_filter( 'woocommerce_blocks_product_grid_item_html', array( $this, 'custom_block_html' ), 2, 3 );
		// Change related products columns.
		add_filter( 'woocommerce_output_related_products_args', array( $this, 'related_products_columns' ), 20 );
		// Add js for category toggling.
		add_filter( 'woocommerce_product_categories_widget_args', array( $this, 'category_widget_toggle_script' ) );
		// Add menu-item class to submenu of woo account nav.
		add_filter( 'woocommerce_account_menu_item_classes', array( $this, 'add_account_navigation_class' ) );

		// Replace woocommerce archive description.
		remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );
		add_action( 'woocommerce_archive_description', array( $this, 'add_product_archive_description' ), 10 );
		add_action( 'init', array( $this, 'setup_shop_content_filter' ), 9 );
		// Shopkit Add filter for block classes.
		add_filter( 'base_shop_kit_product_loop_classes', array( $this, 'add_product_archive_loop_classes_shopkit' ), 10 );

	}
	/**
	 * Add filters for element content output.
	 */
	public function setup_shop_content_filter() {
		global $wp_embed;
		add_filter( 'base_theme_shop_content', array( $wp_embed, 'run_shortcode' ), 8 );
		add_filter( 'base_theme_shop_content', array( $wp_embed, 'autoembed'     ), 8 );
		add_filter( 'base_theme_shop_content', 'do_blocks' );
		add_filter( 'base_theme_shop_content', 'wptexturize' );
		add_filter( 'base_theme_shop_content', 'convert_chars' );
		add_filter( 'base_theme_shop_content', 'shortcode_unautop' );
		add_filter( 'base_theme_shop_content', 'do_shortcode', 11 );
		add_filter( 'base_theme_shop_content', 'convert_smilies', 20 );
	}
	/**
	 * Show a shop page description on product archives.
	 */
	public function add_product_archive_description() {
		// Don't display the description on search results page.
		if ( is_search() ) {
			return;
		}

		if ( is_post_type_archive( 'product' ) && in_array( absint( get_query_var( 'paged' ) ), array( 0, 1 ), true ) ) {
			$shop_page = get_post( wc_get_page_id( 'shop' ) );
			if ( $shop_page ) {
				if ( has_blocks( $shop_page->post_content ) ) {
					$description = apply_filters( 'base_theme_shop_content', $shop_page->post_content );
				} else {
					$allowed_html = wp_kses_allowed_html( 'post' );

					// This is needed for the search product block to work.
					$allowed_html = array_merge(
						$allowed_html,
						array(
							'form'   => array(
								'action'         => true,
								'accept'         => true,
								'accept-charset' => true,
								'enctype'        => true,
								'method'         => true,
								'name'           => true,
								'target'         => true,
							),

							'input'  => array(
								'type'        => true,
								'id'          => true,
								'class'       => true,
								'placeholder' => true,
								'name'        => true,
								'value'       => true,
							),

							'button' => array(
								'type'  => true,
								'class' => true,
								'label' => true,
							),

							'svg'    => array(
								'hidden'    => true,
								'role'      => true,
								'focusable' => true,
								'xmlns'     => true,
								'width'     => true,
								'height'    => true,
								'viewbox'   => true,
							),
							'path'   => array(
								'd' => true,
							),
						)
					);
					$description = wc_format_content( wp_kses( $shop_page->post_content, $allowed_html ) );
				}
				if ( $description ) {
					echo '<div class="page-description">' . $description . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}
		}
	}
	/**
	 * Add a class for the account navigation.
	 *
	 * @param array $classes for the gallery ratio.
	 * @return array updated classes array.
	 */
	public function add_account_navigation_class( $classes ) {
		$classes[] = 'menu-item';
		return $classes;
	}
	/**
	 * Add a class for the initial gallery ratio.
	 *
	 * @param array $classes for the gallery ratio.
	 * @return array updated classes array.
	 */
	public function single_product_image_initial_ratio( $classes ) {
		global $product;
		if ( is_object( $product ) ) {
			$attachment_ids = $product->get_gallery_image_ids();
			if ( $attachment_ids && $product->get_image_id() ) {
				$classes[] = 'gallery-has-thumbnails';
			}
		}
		return $classes;
	}
	/**
	 * Maybe remove product tab heading.
	 *
	 * @param string $heading for the tab.
	 * @return string/bool string or false if disabled.
	 */
	public function remove_product_tab_heading( $heading ) {
		if ( ! webapp()->option( 'product_tab_title' ) ) {
			return false;
		}
		return $heading;
	}
	/**
	 * Just make sure the toggle script is added.
	 *
	 * @param array $args the query args.
	 * @return array the query args.
	 */
	public function category_widget_toggle_script( $args ) {
		wp_enqueue_script( 'base-shop-toggle' );
		return $args;
	}
	/**
	 * Remove the normal archive description.
	 */
	public function action_remove_normal_archive_description() {
		remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description' );
	}
	/**
	 * Sets columns for related columns.
	 *
	 * @param array $args for the related columns.
	 * @return array updated args array.
	 */
	public function related_products_columns( $args ) {
		$columns = absint( webapp()->option( 'product_related_columns' ) );
		$args['posts_per_page'] = $columns;
		$args['columns'] = $columns;
		return $args;
	}
	/**
	 * Sets classes for the product loop.
	 *
	 * @param string/array $classes for the product loop.
	 * @return array updated classes string.
	 */
	public function add_product_archive_loop_classes_shopkit( $classes ) {
		$product_image_hover_style = webapp()->option( 'product_archive_image_hover_switch' );
		$product_btn_style = webapp()->option( 'product_archive_button_style' );
		$hover_style       = 'woo-archive-image-hover-' . esc_attr( $product_image_hover_style );
		$button_style      = 'woo-archive-btn-' . esc_attr( $product_btn_style );
		if ( ! empty( $classes ) && is_array( $classes ) ) {
			$classes[] = $hover_style;
			$classes[] = $button_style;
		} else if ( ! empty( $classes ) ) {
			$classes = array( $hover_style, $button_style, $classes );
		} else {
			$classes = array( $hover_style, $button_style );
		}
		return $classes;
	}
	/**
	 * Adds arrow icon to product action buttons.
	 *
	 * @param string $html the html for the product block.
	 * @param object $data block product object.
	 * @param object $product block product object.
	 * @return string updated html.
	 */
	public function custom_block_html( $html, $data, $product ) {
		$attributes = array(
			'aria-label'       => $product->add_to_cart_description(),
			'data-quantity'    => '1',
			'data-product_id'  => $product->get_id(),
			'data-product_sku' => $product->get_sku(),
			'rel'              => 'nofollow',
			'class'            => 'wp-block-button__link add_to_cart_button',
		);

		if ( $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && ( $product->is_in_stock() || $product->backorders_allowed() ) ) {
			$attributes['class'] .= ' ajax_add_to_cart';
		}
		$product_btn_style = webapp()->option( 'product_archive_button_style' );
		$product_image_hover_style = webapp()->option( 'product_archive_image_hover_switch' );
		if ( 'button' === $product_btn_style ) {
			$cart_text = sprintf(
				'<a href="%s" %s>%s</a>',
				esc_url( $product->add_to_cart_url() ),
				wc_implode_html_attributes( $attributes ),
				esc_html( $product->add_to_cart_text() ) . '' . webapp()->get_icon( 'spinner' ) . '' . webapp()->get_icon( 'check' )
			);
		} else {
			$cart_text = sprintf(
				'<a href="%s" %s>%s</a>',
				esc_url( $product->add_to_cart_url() ),
				wc_implode_html_attributes( $attributes ),
				esc_html( $product->add_to_cart_text() ) . '' . webapp()->get_icon( 'arrow-right-alt' ) . '' . webapp()->get_icon( 'spinner' ) . '' . webapp()->get_icon( 'check' )
			);
		}
		$style_class = '';
		if ( webapp()->option( 'product_archive_button_style' ) ) {
			$style_class = 'style-'.webapp()->option( 'product_archive_button_style' );
		}
		$action_button = '<div class="wp-block-button wc-block-grid__product-add-to-cart '.$style_class.'">' . $cart_text . '</div>';
		$secondary_image_output = '';
		if ( 'none' !== $product_image_hover_style ) {
			if ( is_a( $product, 'WC_Product' ) ) {
				$attachment_ids = $product->get_gallery_image_ids();
				if ( $attachment_ids ) {
					$attachment_ids     = array_values( $attachment_ids );
					$secondary_image_id    = $attachment_ids['0'];
					$secondary_image_alt   = get_post_meta( $secondary_image_id, '_wp_attachment_image_alt', true );
					$secondary_image_title = get_the_title( $secondary_image_id );
					$secondary_image_output = wp_get_attachment_image(
						$secondary_image_id,
						apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' ),
						false,
						array(
							'class' => 'secondary-product-image attachment-woocommerce_thumbnail attachment-shop-catalog wp-post-image wp-post-image--secondary',
							'alt'   => $secondary_image_alt,
							'title' => $secondary_image_title,
						)
					);
				}
			}
		}
		$product_image = $data->image;
		if ( is_a( $product, 'WC_Product' ) && ! empty( $product_image ) ) {
			$image_size = apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' );
			$product_image = $product->get_image( $image_size );
		}
		$new_data = (object) array(
			'permalink'    => $data->permalink,
			'image'        => $product_image,
			'second_image' => $secondary_image_output,
			'title'        => $data->title,
			'rating'       => $data->rating,
			'price'        => $data->price,
			'badge'        => $data->badge,
			'button'       => ( ! empty ( $data->button ) ? $action_button : '' ),
		);
		$align_archive_button = 'block-align-button-normal';
		if ( webapp()->option( 'product_archive_button_align' ) ) {
			$align_archive_button = 'block-align-buttons-bottom';
		}
		$product_style     = webapp()->option( 'product_archive_style' );
		$action_style      = 'woo-archive-' . esc_attr( $product_style );
		$button_style      = 'woo-archive-btn-' . esc_attr( $product_btn_style );
		$hover_style       = 'woo-archive-image-hover-' . esc_attr( $product_image_hover_style );
		if ( $product_image_hover_style !== 'none' && ! empty( $secondary_image_output ) ) {
			$product_image_link = 'wc-block-grid__product-link woocommerce-loop-image-link woocommerce-LoopProduct-link woocommerce-loop-product__link product-has-hover-image';
		} else {
			$product_image_link = 'wc-block-grid__product-link woocommerce-loop-image-link woocommerce-LoopProduct-link woocommerce-loop-product__link';
		}
		$boxed = webapp()->option( 'product_archive_content_style' );
		if ( 'unboxed' === $boxed || 'boxed' === $boxed ) {
			$boxed_class = 'product-loop-' . $boxed;
		} else {
			$boxed_class = 'product-loop-unboxed';
		}
		$output = "<li class=\"wc-block-grid__product entry loop-entry content-bg {$action_style} {$button_style} {$boxed_class} {$hover_style}\">";
		if ( $product_image ) {
			$output .= "<a href=\"{$new_data->permalink}\" class=\"{$product_image_link}\">
					{$new_data->image}
					{$new_data->second_image}
				</a>";
		}
		$output .= "{$new_data->badge}
				<div class=\"product-details content-bg entry-content-wrap\">
					<a href=\"{$new_data->permalink}\" class=\"wc-block-grid__product-title-link\">
						{$new_data->title}
					</a>
					{$new_data->rating}
					{$new_data->price}
					{$new_data->button}
				</div>
			</li>";
		return $output;
	}
	/**
	 * Adds comment css for reviews.
	 */
	public function single_product_comment_css() {
		webapp()->print_styles( 'base-comments' );
	}
	/**
	 * Removes hooks and triggers other hooks realted to the single product page.
	 */
	public function single_product_layout() {
		// Product Single Cat.
		$category_element = webapp()->option( 'product_content_element_category' );
		if ( isset( $category_element ) && is_array( $category_element ) && true === $category_element['enabled'] ) {
			add_action( 'woocommerce_single_product_summary', array( $this, 'woocommerce_product_single_category' ), 3 );
		}
		// Product Title.
		$title_element = webapp()->option( 'product_content_element_title' );
		if ( isset( $title_element ) && is_array( $title_element ) && false === $title_element['enabled'] ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
		}
		// Product Rating.
		$rating_element = webapp()->option( 'product_content_element_rating' );
		if ( isset( $rating_element ) && is_array( $rating_element ) && false === $rating_element['enabled'] ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating' );
		}
		// Product Price.
		$price_element = webapp()->option( 'product_content_element_price' );
		if ( isset( $price_element ) && is_array( $price_element ) && false === $price_element['enabled'] ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price' );
		} else {
			if ( isset( $price_element ) && is_array( $price_element ) && true === $price_element['show_shipping'] ) {
				add_filter( 'woocommerce_get_price_html', array( $this, 'add_shipping_statement_price' ), 10, 2 );
			}
		}
		// Product Excerpt.
		$excerpt_element = webapp()->option( 'product_content_element_excerpt' );
		if ( isset( $excerpt_element ) && is_array( $excerpt_element ) && false === $excerpt_element['enabled'] ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
		}
		// Product Cart.
		$add_to_cart_element = webapp()->option( 'product_content_element_add_to_cart' );
		if ( isset( $add_to_cart_element ) && is_array( $add_to_cart_element ) && false === $add_to_cart_element['enabled'] ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		}
		// Product Extras.
		$extras_element = webapp()->option( 'product_content_element_extras' );
		if ( isset( $extras_element ) && is_array( $extras_element ) && true === $extras_element['enabled'] ) {
			add_action( 'woocommerce_single_product_summary', array( $this, 'woocommerce_product_single_extras' ), 35 );
		}
		// Product Payments.
		$payments_element = webapp()->option( 'product_content_element_payments' );
		if ( isset( $payments_element ) && is_array( $payments_element ) && true === $payments_element['enabled'] ) {
			add_action( 'woocommerce_single_product_summary', array( $this, 'woocommerce_product_single_payments' ), 38 );
		}
		// Product Meta.
		$meta_element = webapp()->option( 'product_content_element_product_meta' );
		if ( isset( $meta_element ) && is_array( $meta_element ) && false === $meta_element['enabled'] ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
		}
		// Product Sharing.
		$sharing_element = webapp()->option( 'product_content_element_sharing' );
		if ( isset( $sharing_element ) && is_array( $sharing_element ) && false === $sharing_element['enabled'] ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
		}
		// Remove Weight and Dimensions.
		if ( false === webapp()->option( 'product_additional_weight_dimensions' ) ) {
			add_filter( 'wc_product_enable_dimensions_display', '__return_false' );
		}
		// Related Products.
		if ( false === webapp()->option( 'product_related' ) ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		}
	}
	/**
	 * Adds a shipping price after the price on single product pages.
	 *
	 * @param string $price the price for the product.
	 * @param object $object product object.
	 * @return string Filtered body classes.
	 */
	public function add_shipping_statement_price( $price, $object ) {
		if ( is_product() && get_queried_object_id() === $object->get_id() ) {
			$price_element = webapp()->option( 'product_content_element_price' );
			if ( isset( $price_element ) && is_array( $price_element ) && isset( $price_element['shipping_statement'] ) && ! empty( $price_element['shipping_statement'] ) ) {
				$price = $price . ' <span class="brief-shipping-details">' . $price_element['shipping_statement'] . '</span>';
			}
		}
		return $price;
	}
	/**
	 * Adds single product category.
	 */
	public function woocommerce_product_single_category() {
		global $post;
		$main_term = false;
		if ( class_exists( 'WPSEO_Primary_Term' ) ) {
			$wpseo_term = new WPSEO_Primary_Term( 'product_cat', $post->ID );
			$wpseo_term = $wpseo_term->get_primary_term();
			$wpseo_term = get_term( $wpseo_term );
			if ( is_wp_error( $wpseo_term ) ) {
				$main_term = false;
			} else {
				$main_term = $wpseo_term;
			}
		} elseif ( class_exists( 'RankMath' ) ) {
			$wpseo_term = get_post_meta( $post->ID, 'rank_math_primary_product_cat', true );
			if ( $wpseo_term ) {
				$wpseo_term = get_term( $wpseo_term );
				if ( is_wp_error( $wpseo_term ) ) {
					$main_term = false;
				} else {
					$main_term = $wpseo_term;
				}
			} else {
				$main_term = false;
			}
		}
		if ( false === $main_term ) {
			$main_term = '';
			$terms     = wp_get_post_terms(
				$post->ID,
				'product_cat',
				array(
					'orderby' => 'parent',
					'order'   => 'DESC',
				)
			);
			if ( $terms && ! is_wp_error( $terms ) ) {
				if ( is_array( $terms ) ) {
					$main_term = $terms[0];
				}
			}
		}
		if ( $main_term ) {
			$term_title = $main_term->name;
			echo '<div class="single-product-category">';
			echo '<a href="' . esc_attr( get_term_link( $main_term->slug, 'product_cat' ) ) . '" class="product-single-category single-category">';
			echo esc_html( $term_title );
			echo '</a>';
			echo '</div>';
		}
	}
	/**
	 * Adds Product Extras just below the button.
	 */
	public function woocommerce_product_single_extras() {
		$extras_element = webapp()->option( 'product_content_element_extras' );
		echo '<div class="single-product-extras">';
		if ( isset( $extras_element ) && is_array( $extras_element ) && isset( $extras_element['title'] ) && ! empty( $extras_element['title'] ) ) {
			echo '<p><strong>' . wp_kses_post( $extras_element['title'] ) . '</strong></p>';
		}
		echo '<ul>';
		if ( isset( $extras_element ) && is_array( $extras_element ) && isset( $extras_element['feature_1'] ) && ! empty( $extras_element['feature_1'] ) ) {
			echo '<li>' . webapp()->get_icon( isset( $extras_element['feature_1_icon'] ) && ! empty( $extras_element['feature_1_icon'] ) ? $extras_element['feature_1_icon'] : 'shield_check' ) . ' ' . wp_kses_post( $extras_element['feature_1'] ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( isset( $extras_element ) && is_array( $extras_element ) && isset( $extras_element['feature_2'] ) && ! empty( $extras_element['feature_2'] ) ) {
			echo '<li>' . webapp()->get_icon( isset( $extras_element['feature_2_icon'] ) && ! empty( $extras_element['feature_2_icon'] ) ? $extras_element['feature_2_icon'] : 'shield_check' ) . ' ' . wp_kses_post( $extras_element['feature_2'] ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( isset( $extras_element ) && is_array( $extras_element ) && isset( $extras_element['feature_3'] ) && ! empty( $extras_element['feature_3'] ) ) {
			echo '<li>' . webapp()->get_icon( isset( $extras_element['feature_3_icon'] ) && ! empty( $extras_element['feature_3_icon'] ) ? $extras_element['feature_3_icon'] : 'shield_check' ) . ' ' . wp_kses_post( $extras_element['feature_3'] ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( isset( $extras_element ) && is_array( $extras_element ) && isset( $extras_element['feature_4'] ) && ! empty( $extras_element['feature_4'] ) ) {
			echo '<li>' . webapp()->get_icon( isset( $extras_element['feature_4_icon'] ) && ! empty( $extras_element['feature_4_icon'] ) ? $extras_element['feature_4_icon'] : 'shield_check' ) . ' ' . wp_kses_post( $extras_element['feature_4'] ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( isset( $extras_element ) && is_array( $extras_element ) && isset( $extras_element['feature_5'] ) && ! empty( $extras_element['feature_5'] ) ) {
			echo '<li>' . webapp()->get_icon( isset( $extras_element['feature_5_icon'] ) && ! empty( $extras_element['feature_5_icon'] ) ? $extras_element['feature_5_icon'] : 'shield_check' ) . ' ' . wp_kses_post( $extras_element['feature_5'] ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</ul>';
		echo '</div>';
	}
	/**
	 * Adds Product Payments just below the button.
	 */
	public function woocommerce_product_single_payments() {
		$payments_element = webapp()->option( 'product_content_element_payments' );
		$colors           = ( isset( $payments_element ) && is_array( $payments_element ) && isset( $payments_element['card_color'] ) && ! empty( $payments_element['card_color'] ) ? $payments_element['card_color'] : 'inherit' );
		echo '<fieldset class="single-product-payments payments-color-scheme-' . esc_attr( $colors ) . '">';
		if ( isset( $payments_element ) && is_array( $payments_element ) && isset( $payments_element['title'] ) && ! empty( $payments_element['title'] ) ) {
			echo '<legend>' . wp_kses_post( $payments_element['title'] ) . '</legend>';
		}
		echo '<ul>';
		if ( isset( $payments_element ) && is_array( $payments_element ) && isset( $payments_element['stripe'] ) && true === $payments_element['stripe'] ) {
			echo '<li class="single-product-payments-stripe">' . webapp()->get_icon( 'stripe' ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( isset( $payments_element ) && is_array( $payments_element ) && isset( $payments_element['visa'] ) && true === $payments_element['visa'] ) {
			echo '<li class="single-product-payments-visa">' . webapp()->get_icon( ( 'inherit' !== $colors ? 'visa_gray' : 'visa' ) ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( isset( $payments_element ) && is_array( $payments_element ) && isset( $payments_element['mastercard'] ) && true === $payments_element['mastercard'] ) {
			echo '<li class="single-product-payments-mastercard">' . webapp()->get_icon( ( 'inherit' !== $colors ? 'mastercard_gray' : 'mastercard' ) ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( isset( $payments_element ) && is_array( $payments_element ) && isset( $payments_element['amex'] ) && true === $payments_element['amex'] ) {
			echo '<li class="single-product-payments-amex">' . webapp()->get_icon( ( 'inherit' !== $colors ? 'amex_gray' : 'amex' ) ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( isset( $payments_element ) && is_array( $payments_element ) && isset( $payments_element['discover'] ) && true === $payments_element['discover'] ) {
			echo '<li class="single-product-payments-discover">' . webapp()->get_icon( ( 'inherit' !== $colors ? 'discover_gray' : 'discover' ) ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( isset( $payments_element ) && is_array( $payments_element ) && isset( $payments_element['paypal'] ) && true === $payments_element['paypal'] ) {
			echo '<li class="single-product-payments-paypal">' . webapp()->get_icon( ( 'inherit' !== $colors ? 'paypal_gray' : 'paypal' ) ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( isset( $payments_element ) && is_array( $payments_element ) && isset( $payments_element['applepay'] ) && true === $payments_element['applepay'] ) {
			echo '<li class="single-product-payments-applepay">' . webapp()->get_icon( ( 'inherit' !== $colors ? 'applepay_gray' : 'applepay' ) ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( isset( $payments_element ) && is_array( $payments_element ) && isset( $payments_element['custom_enable_01'] ) && true === $payments_element['custom_enable_01'] && isset( $payments_element['custom_img_01'] ) && ! empty( $payments_element['custom_img_01'] ) ) {
			echo '<li class="single-product-payments-custom-01"><img src="' . esc_attr( $payments_element['custom_img_01'] ) . '" class="payment-custom-img' . ( 'inherit' !== $colors ? ' payment-custom-img-gray' : '' ) . '" alt="' . ( isset( $payments_element['custom_id_01'] ) && ! empty( $payments_element['custom_id_01'] ) ? get_post_meta( $payments_element['custom_id_01'], '_wp_attachment_image_alt', true ) : '' ) . '"/></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( isset( $payments_element ) && is_array( $payments_element ) && isset( $payments_element['custom_enable_02'] ) && true === $payments_element['custom_enable_02'] && isset( $payments_element['custom_img_02'] ) && ! empty( $payments_element['custom_img_02'] ) ) {
			echo '<li class="single-product-payments-custom-02"><img src="' . esc_attr( $payments_element['custom_img_02'] ) . '" class="payment-custom-img' . ( 'inherit' !== $colors ? ' payment-custom-img-gray' : '' ) . '" alt="' . ( isset( $payments_element['custom_id_02'] ) && ! empty( $payments_element['custom_id_02'] ) ? get_post_meta( $payments_element['custom_id_02'], '_wp_attachment_image_alt', true ) : '' ) . '"/></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( isset( $payments_element ) && is_array( $payments_element ) && isset( $payments_element['custom_enable_03'] ) && true === $payments_element['custom_enable_03'] && isset( $payments_element['custom_img_03'] ) && ! empty( $payments_element['custom_img_03'] ) ) {
			echo '<li class="single-product-payments-custom-03"><img src="' . esc_attr( $payments_element['custom_img_03'] ) . '" class="payment-custom-img' . ( 'inherit' !== $colors ? ' payment-custom-img-gray' : '' ) . '" alt="' . ( isset( $payments_element['custom_id_03'] ) && ! empty( $payments_element['custom_id_03'] ) ? get_post_meta( $payments_element['custom_id_03'], '_wp_attachment_image_alt', true ) : '' ) . '"/></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( isset( $payments_element ) && is_array( $payments_element ) && isset( $payments_element['custom_enable_04'] ) && true === $payments_element['custom_enable_04'] && isset( $payments_element['custom_img_04'] ) && ! empty( $payments_element['custom_img_04'] ) ) {
			echo '<li class="single-product-payments-custom-04"><img src="' . esc_attr( $payments_element['custom_img_04'] ) . '" class="payment-custom-img' . ( 'inherit' !== $colors ? ' payment-custom-img-gray' : '' ) . '" alt="' . ( isset( $payments_element['custom_id_04'] ) && ! empty( $payments_element['custom_id_04'] ) ? get_post_meta( $payments_element['custom_id_04'], '_wp_attachment_image_alt', true ) : '' ) . '"/></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( isset( $payments_element ) && is_array( $payments_element ) && isset( $payments_element['custom_enable_05'] ) && true === $payments_element['custom_enable_05'] && isset( $payments_element['custom_img_05'] ) && ! empty( $payments_element['custom_img_05'] ) ) {
			echo '<li class="single-product-payments-custom-05"><img src="' . esc_attr( $payments_element['custom_img_05'] ) . '" class="payment-custom-img' . ( 'inherit' !== $colors ? ' payment-custom-img-gray' : '' ) . '" alt="' . ( isset( $payments_element['custom_id_05'] ) && ! empty( $payments_element['custom_id_05'] ) ? get_post_meta( $payments_element['custom_id_05'], '_wp_attachment_image_alt', true ) : '' ) . '"/></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</ul>';
		echo '</fieldset>';
	}
	/**
	 * Removes filter to add svgs to add to cart link for product archives.
	 *
	 * @param string $html the html to end a loop.
	 * @return string $html the html to end a loop.
	 */
	public function remove_filter_for_add_to_cart_link( $html ) {
		remove_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'filter_add_to_cart_link_link' ), 10, 3 );
		return $html;
	}
	/**
	 * Adds filter to add svgs to add to cart link for product archives.
	 *
	 * @param string $html the html to start a loop.
	 * @return string $html the html to start a loop.
	 */
	public function add_filter_for_add_to_cart_link( $html ) {
		add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'filter_add_to_cart_link_link' ), 9, 3 );
		return $html;
	}

	/**
	 * Adds custom classes to body tab related to woocommerce.
	 *
	 * @param array $classes Classes for the body element.
	 * @return array Filtered body classes.
	 */
	public function woo_extra_body_classes( $classes ) {
		// Store Notice Body class.
		if ( is_store_notice_showing() ) {
			$placement = webapp()->option( 'woo_store_notice_placement' );
			$classes[] = esc_attr( 'base-store-notice-placement-' . $placement );
			if ( 'above' === $placement ) {
				if ( webapp()->option( 'woo_store_notice_hide_dismiss' ) ) {
					add_filter( 'woocommerce_demo_store', array( $this, 'woocommerce_demo_store_remove_dismiss' ), 15, 2 );
				}
				remove_action( 'wp_footer', 'woocommerce_demo_store' );
				add_action( 'base_before_header', 'woocommerce_demo_store' );
			}
		}
		if ( is_archive() && is_tax() ) {
			$slug = ( is_search() && ! is_post_type_archive( 'product' ) ? 'search' : get_post_type() );
			if ( empty( $slug ) ) {
				$queried_object = get_queried_object();
				if ( property_exists( $queried_object, 'taxonomy' ) ) {
					$current_tax = get_taxonomy( $queried_object->taxonomy );
					if ( property_exists( $current_tax, 'object_type' ) ) {
						$post_types = $current_tax->object_type;
						$slug = $post_types[0];
						if ( 'product' === $slug ) {
							$classes[] = 'tax-woo-product';
						}
					}
				}
			} else if ( 'product' === $slug ) {
				$classes[] = 'tax-woo-product';
			}
		}
		return $classes;
	}
	/**
	 * Filters woocommerce demo store to remove dismiss option.
	 *
	 * @param string $notice_html html for the notice.
	 * @param string $notice text for the notice.
	 * @return string new html for the notice.
	 */
	public function woocommerce_demo_store_remove_dismiss( $notice_html, $notice ) {
		$notice_id = md5( $notice );
		return '<p class="woocommerce-store-notice woo-static-store-notice demo_store" data-notice-id="' . esc_attr( $notice_id ) . '" style="display:block;">' . wp_kses_post( $notice ) . '</p>';
	}
	/**
	 * Adds custom classes to indicate whether a sidebar is present to the array of body classes.
	 *
	 * @param array $classes Classes for the body element.
	 * @return array Filtered body classes.
	 */
	public function my_account_body_classes( array $classes ) : array {
		if ( is_account_page() ) {
			$classes[] = 'base-account-nav-' . esc_attr( webapp()->option( 'woo_account_navigation_layout' ) );
		}
		return $classes;
	}
	/**
	 * Adds custom classes to indicate the button size for the single products.
	 *
	 * @param array $classes Classes for the body element.
	 * @return array Filtered body classes.
	 */
	public function single_product_body_classes( array $classes ) : array {
		if ( is_product() ) {
			$cart_element = webapp()->option( 'product_content_element_add_to_cart' );
			if ( isset( $cart_element ) && is_array( $cart_element ) && isset( $cart_element['button_size'] ) && ! empty( $cart_element['button_size'] ) ) {
				$size = $cart_element['button_size'];
			} else if ( webapp()->option( 'product_large_cart_button' ) ) {
				$size = 'large';
			} else {
				$size = 'normal';
			}
			$classes[] = 'product-tab-style-' . esc_attr( webapp()->option( 'product_tab_style' ) );
			$classes[] = 'product-variation-style-' . esc_attr( webapp()->option( 'variation_direction' ) );
			$classes[] = 'base-cart-button-' . esc_attr( $size );
		}
		return $classes;
	}
	/**
	 * Refresh the cart for ajax adds.
	 *
	 * @param object $fragments the cart object.
	 */
	public function get_refreshed_fragments_class( $fragments ) {
		// Get cart items.
		ob_start();

		?><span class="header-cart-empty-check header-cart-is-empty-<?php echo esc_attr( WC()->cart->get_cart_contents_count() > 0 ? 'false' : 'true' ); ?>"></span> 
		<?php

		$fragments['span.header-cart-empty-check'] = ob_get_clean();

		return $fragments;

	}
	/**
	 * Refresh the cart for ajax adds.
	 *
	 * @param object $fragments the cart object.
	 */
	public function get_refreshed_fragments_number( $fragments ) {
		// Get cart items.
		ob_start();

		?><span class="header-cart-total header-cart-is-empty-<?php echo esc_attr( WC()->cart->get_cart_contents_count() > 0 ? 'false' : 'true' ); ?>"><?php echo wp_kses_post( WC()->cart->get_cart_contents_count() ); ?></span> 
		<?php

		$fragments['span.header-cart-total'] = ob_get_clean();

		return $fragments;

	}
	/**
	 * Refresh the cart for ajax adds.
	 *
	 * @param object $fragments the cart object.
	 */
	public function get_refreshed_fragments_mini( $fragments ) {
		// Get mini cart.
		ob_start();

		echo '<div class="base-mini-cart-refresh">';
		woocommerce_mini_cart();
		echo '</div>';
		$fragments['div.base-mini-cart-refresh'] = ob_get_clean();

		return $fragments;

	}
	/**
	 * Checks to see if theme needs to hook into cart fragments.
	 */
	public function check_for_fragment_support() {
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'get_refreshed_fragments_class' ) );
		if ( webapp()->option( 'header_cart_show_total' ) ) {
			self::$show_cart_total = true;
			add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'get_refreshed_fragments_number' ) );
		}
		if ( 'slide' === webapp()->option( 'header_cart_style' ) || 'slide' === webapp()->option( 'header_mobile_cart_style' ) || 'dropdown' === webapp()->option( 'header_cart_style' ) ) {
			self::$show_mini_cart = true;
			add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'get_refreshed_fragments_mini' ) );
		}
	}
	/**
	 * Checks to see if themes conditional header needs to hook into cart fragments.
	 */
	public function check_conditional_for_fragment_support() {
		if ( webapp()->option( 'header_cart_show_total' ) && ! self::$show_cart_total ) {
			self::$show_cart_total = true;
			add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'get_refreshed_fragments_number' ) );
		}
		if ( ( 'slide' === webapp()->option( 'header_cart_style' ) || 'slide' === webapp()->option( 'header_mobile_cart_style' ) || 'dropdown' === webapp()->option( 'header_cart_style' ) ) && ! self::$show_mini_cart ) {
			self::$show_mini_cart = true;
			add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'get_refreshed_fragments_mini' ) );
		}
	}
	/**
	 * Checks to see if theme needs to hook into cart fragments.
	 */
	public function cart_in_header() {
		$in_header = false;
		$elements  = webapp()->option( 'header_desktop_items' );
		if ( isset( $elements ) && is_array( $elements ) ) {
			foreach ( array( 'top', 'main', 'bottom' ) as $row ) {
				if ( isset( $elements[ $row ] ) && is_array( $elements[ $row ] ) ) {
					foreach ( array( 'left', 'left_center', 'center', 'right_center', 'right' ) as $column ) {
						if ( isset( $elements[ $row ][ $row . '_' . $column ] ) && is_array( $elements[ $row ][  $row . '_' . $column ] ) ) {
							if ( in_array( 'cart', $elements[ $row ][  $row . '_' . $column ], true ) ) {
								$in_header = true;
								break;
							}
						}
					}
				}
			}
		}
		return $in_header;
	}
	/**
	 * Enqueues a script for product CLS.
	 */
	public function action_enqueue_product_scripts() {
		if ( class_exists( 'woocommerce' ) && is_product() ) {
			wp_enqueue_script(
				'base-product-cls',
				get_theme_file_uri( '/assets/js/product-cls.min.js' ),
				array( 'jquery' ),
				AVANAM_VERSION,
				true
			);
			// wp_script_add_data( 'base-product-cls', 'async', true );
			// wp_script_add_data( 'base-product-cls', 'precache', true );
		}
	}
	/**
	 * Enqueues a script for shop toggle.
	 */
	public function action_enqueue_scripts() {

		// If the AMP plugin is active, return early.
		if ( webapp()->is_amp() ) {
			return;
		}
		if ( webapp()->option( 'custom_quantity' ) ) {
			// Enqueue the quantity script.
			wp_enqueue_script(
				'base-shop-spinner',
				get_theme_file_uri( '/assets/js/shop-spinner.min.js' ),
				array( 'jquery' ),
				AVANAM_VERSION,
				true
			);
			wp_script_add_data( 'base-shop-spinner', 'async', true );
			wp_script_add_data( 'base-shop-spinner', 'precache', true );
		}
		if ( class_exists( 'woocommerce' ) && is_cart() ) {
			// Enqueue the quantity script.
			wp_enqueue_script(
				'base-cart-update',
				get_theme_file_uri( '/assets/js/cart-update.min.js' ),
				array( 'jquery' ),
				AVANAM_VERSION,
				true
			);
			wp_script_add_data( 'base-cart-update', 'async', true );
			wp_script_add_data( 'base-cart-update', 'precache', true );
		}
		// Enqueue the toggle script.
		wp_register_script(
			'base-shop-toggle',
			get_theme_file_uri( '/assets/js/shop-toggle.min.js' ),
			array(),
			AVANAM_VERSION,
			true
		);
		wp_script_add_data( 'base-shop-toggle', 'async', true );
		wp_script_add_data( 'base-shop-toggle', 'precache', true );
		wp_localize_script(
			'base-shop-toggle',
			'baseShopConfig',
			array(
				'siteSlug' => sanitize_title( get_bloginfo( 'name' ) ),
			)
		);
	}

	/**
	 * Print the notices on none woocommerce pages.
	 */
	public function wc_print_notices_none_woo() {
		if ( ! is_shop() && ! is_woocommerce() && ! is_cart() && ! is_checkout() && ! is_account_page() ) {
			if ( function_exists( 'wc_print_notices' ) ) {
				echo '<div class="woocommerce base-woo-messages-none-woo-pages woocommerce-notices-wrapper">';
				echo wc_print_notices( true );
				echo '</div>';
			}
		}
	}
	/**
	 * Set Cross sells limit.
	 *
	 * @param string $limit the current product limit.
	 */
	public function cross_sell_limit( $limit ) {
		return 4;
	}
	/**
	 * Set Cross sells columns.
	 *
	 * @param string $columns the current column count.
	 */
	public function cross_sell_columns( $columns ) {
		return 4;
	}
	/**
	 * Insert the Cart summary title
	 */
	public function cart_summary_title() {
		echo '<div class="cart-summary"><h2>' . esc_html__( 'Cart Summary', 'avanam' ) . '</h2></div>';
	}
	/**
	 * Insert the Cart Form wrap.
	 */
	public function cart_form_wrap_before() {
		echo '<div class="base-woo-cart-form-wrap">';
	}
	/**
	 * Close the Cart Form wrap.
	 */
	public function cart_form_wrap_after() {
		echo '</div>';
	}
	/**
	 * Insert the myaccount navigation wrap.
	 */
	public function myaccount_nav_wrap_start() {
		echo '<div class="account-navigation-wrap">';
	}
	/**
	 * Close the myaccount navigation wrap.
	 */
	public function myaccount_nav_wrap_end() {
		echo '</div>';
	}
	/**
	 * Avatar for myaccount page.
	 */
	public function myaccount_nav_avatar() {
		$current_user = wp_get_current_user();
		if ( webapp()->option( 'woo_account_navigation_avatar' ) && 0 !== $current_user->ID ) {
			?>
			<div class="base-account-avatar">
				<div class="base-customer-image">
				<a class="bst-link-to-gravatar" href="https://gravatar.com/" target="_blank" rel="no" title="<?php echo esc_attr__( 'Update Profile Photo', 'avanam' ); ?>">
					<?php echo get_avatar( $current_user->ID, 60 ); ?>
				</a>
				</div>
				<div class="base-customer-name">
					<?php echo esc_html( $current_user->display_name ); ?>
				</div> 
			</div>
			<?php
		}
	}
	/**
	 * Output product loop wrapper.
	 *
	 * @param string $output the loop start.
	 */
	public function product_loop_start( $output ) {
		$columns = absint( wc_get_loop_prop( 'columns' ) );
		$columns_class = 'content-wrap product-archive grid-cols grid-ss-col-2 grid-sm-col-3 grid-lg-col-4';
		if ( 1 === $columns ) {
			if ( is_main_query() && is_archive() && ! wc_get_loop_prop( 'is_shortcode' ) ) {
				$columns_class = 'content-wrap product-archive grid-cols grid-sm-col-1 grid-lg-col-1 products-list-view';
			} else {
				$columns_class = 'content-wrap product-archive grid-cols grid-sm-col-1 grid-lg-col-1';
			}
		} elseif ( 2 === $columns ) {
			$columns_class = 'content-wrap product-archive grid-cols grid-sm-col-2 grid-lg-col-2';
		} elseif ( 3 === $columns ) {
			$columns_class = 'content-wrap product-archive grid-cols grid-sm-col-2 grid-lg-col-3';
		} elseif ( 4 === $columns ) {
			$columns_class = 'content-wrap product-archive grid-cols grid-ss-col-2 grid-sm-col-3 grid-lg-col-4';
		} elseif ( 5 === $columns ) {
			$columns_class = 'content-wrap product-archive grid-cols grid-ss-col-2 grid-sm-col-3 grid-md-col-4 grid-lg-col-5';
		} elseif ( 6 === $columns ) {
			$columns_class = 'content-wrap product-archive grid-cols grid-ss-col-2 grid-sm-col-3 grid-md-col-4 grid-lg-col-6';
		}
		$main_archive_loop = '';
		if ( is_main_query() && is_archive() && ! wc_get_loop_prop( 'is_shortcode' ) ) {
			$main_archive_loop = 'woo-archive-loop';
		}
		$align_archive_button = '';
		if ( webapp()->option( 'product_archive_button_align' ) ) {
			$align_archive_button = 'align-buttons-bottom';
		}
		$product_style = webapp()->option( 'product_archive_style' );
		$product_btn_style = webapp()->option( 'product_archive_button_style' );
		$product_image_hover_style = webapp()->option( 'product_archive_image_hover_switch' );
		if ( is_main_query() && is_archive() && wc_get_loop_prop( 'is_paginated' ) && apply_filters( 'base_enabled_product_archive_attributes', true, $GLOBALS['woocommerce_loop'] ) ) {
			$attributes = $this->get_archive_infinite_attributes();
		} else {
			$attributes = '';
		}
		return '<ul class="products ' . esc_attr( $columns_class ) . ' woo-archive-' . esc_attr( $product_style ) . ' woo-archive-btn-' . esc_attr( $product_btn_style ) . ( $main_archive_loop ? ' ' . $main_archive_loop : '' ) . ( $align_archive_button ? ' ' . $align_archive_button : '' ) . '  woo-archive-image-hover-' . esc_attr( $product_image_hover_style ) . '"' . ( $attributes ? " data-infinite-scroll='" . esc_attr( $attributes ) . "'" : '' ) . '>';

	}
	/**
	 * Get Archive infinite attributes
	 *
	 * @return string $attributes for the archive container.
	 */
	public function get_archive_infinite_attributes() {
		$attributes = '';
		return apply_filters( 'base_product_archive_infinite_attributes', $attributes );
	}
	/**
	 * Insert the opening wrap for products image in the loop.
	 */
	public function archive_loop_image_wrap_open() {
		echo '<div class="product-thumbnail">';
	}
	/**
	 * Insert the closing wrap for products image in the loop.
	 */
	public function archive_loop_image_wrap_close() {
		echo '</div>';
	}
	/**
	 * Insert the closing anchor tag for products image in the loop.
	 */
	public function archive_loop_product_action() {
		echo '<div class="product-actions">';
			do_action( 'base_archive_loop_product_action_over_image' );
		echo '</div>';
	}
	/**
	 * Insert the opening anchor tag for products image in the loop.
	 */
	public function archive_loop_image_link_open() {
		global $product;

		$link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound
		$has_hover_image = '';
		if ( 'none' !== webapp()->option( 'product_archive_image_hover_switch' ) ) {
			if ( is_a( $product, 'WC_Product' ) ) {
				$attachment_ids = $product->get_gallery_image_ids();
				if ( $attachment_ids ) {
					$has_hover_image = ' product-has-hover-image';
				}
			}
		}
		echo '<a href="' . esc_url( $link ) . '" class="woocommerce-loop-image-link woocommerce-LoopProduct-link woocommerce-loop-product__link' . esc_attr( $has_hover_image ) . '">';
	}
	/**
	 * Insert the closing anchor tag for products image in the loop.
	 */
	public function archive_loop_image_link_close() {
		echo '</a>';
	}
	/**
	 * Insert a second product image if enabled and if image exists.
	 */
	public function archive_loop_second_image() {
		if ( 'none' !== webapp()->option( 'product_archive_image_hover_switch' ) ) {
			global $product;
			if ( is_a( $product, 'WC_Product' ) ) {
				$attachment_ids = $product->get_gallery_image_ids();
				if ( $attachment_ids ) {
					$attachment_ids     = array_values( $attachment_ids );
					$secondary_image_id    = $attachment_ids['0'];
					$secondary_image_alt   = get_post_meta( $secondary_image_id, '_wp_attachment_image_alt', true );
					$secondary_image_title = get_the_title( $secondary_image_id );
					echo wp_get_attachment_image(
						$secondary_image_id,
						apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' ),
						false,
						array(
							'class' => 'secondary-product-image attachment-woocommerce_thumbnail attachment-shop-catalog wp-post-image wp-post-image--secondary',
							'alt'   => $secondary_image_alt,
							'title' => $secondary_image_title,
						)
					);
				}
			}
		}
	}
	/**
	 * Insert the content wrap.
	 */
	public function archive_content_wrap_start() {
		echo apply_filters( 'base_archive_content_wrap_start', '<div class="product-details content-bg entry-content-wrap">' );
	}
	/**
	 * Close the content wrap.
	 */
	public function archive_content_wrap_end() {
		echo apply_filters( 'base_archive_content_wrap_end', '</div>' );
	}
	/**
	 * Show the product title in the product loop. By default this is an H2.
	 */
	public function archive_title_with_link() {
		global $product;

		$link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound
		echo '<h2 class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ) . '"><a href="' . esc_url( $link ) . '" class="woocommerce-LoopProduct-link-title woocommerce-loop-product__title_ink">' . get_the_title() . '</a></h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		// phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound
	}

	/**
	 * Show the product excerpt if single or if toggle is on, only for archives.
	 */
	public function archive_excerpt() {
		if ( is_main_query() && is_archive() ) {
			$columns = wc_get_loop_prop( 'columns' );
			if ( 1 === $columns || webapp()->option( 'product_archive_toggle' ) ) {
				global $post;
				echo '<div class="product-excerpt">';
				if ( $post->post_excerpt ) {
					echo wp_kses_post( apply_filters( 'archive_woocommerce_short_description', $post->post_excerpt ) ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound
				} else {
					the_excerpt();
				}
				echo '</div>';
			}
		}
	}
	/**
	 * Wrap Action buttons.
	 */
	public function archive_action_wrap_start() {
		$class = '';
		if ( webapp()->option( 'product_archive_button_style' ) ) {
			$class = 'style-'.webapp()->option( 'product_archive_button_style' );
		}
		echo '<div class="product-action-wrap '.$class.'">';
		
		do_action( 'base_archive_loop_product_action_inner' );
	}
	/**
	 * Close Action buttons wrap.
	 */
	public function archive_action_wrap_end() {
		echo '</div>';
	}
	/**
	 * Adds Arrow to add to cart button.
	 *
	 * @param string $button Current classes.
	 * @param object $product Product object.
	 * @param array  $args The Product args.
	 */
	public function filter_add_to_cart_link_link( $button, $product, $args = array() ) {
		$product_btn_style = webapp()->option( 'product_archive_button_style' );
		if ( 'button' === $product_btn_style ) {
			$button = sprintf(
				'<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
				esc_url( $product->add_to_cart_url() ),
				esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
				esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
				isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
				esc_html( $product->add_to_cart_text() ) . '' . webapp()->get_icon( 'spinner' ) . '' . webapp()->get_icon( 'check' )
			);
		} else {
			$button = sprintf(
				'<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
				esc_url( $product->add_to_cart_url() ),
				esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
				esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
				isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
				esc_html( $product->add_to_cart_text() ) . '' . webapp()->get_icon( 'arrow-right-alt' ) . '' . webapp()->get_icon( 'spinner' ) . '' . webapp()->get_icon( 'check' )
			);
		}
		return $button;
	}
	/**
	 * Adds results count and catalog ordering.
	 *
	 * @param array        $classes Current classes.
	 * @param string|array $class Additional class.
	 * @param int          $post_id Post ID.
	 */
	public function add_woo_entry_classes( $classes, $class = '', $post_id = 0 ) {
		if ( ! $post_id || ! in_array( get_post_type( $post_id ), array( 'product', 'product_variation' ), true ) ) {
			return $classes;
		}
		$product = wc_get_product( $post_id );
		if ( ! $product ) {
			return $classes;
		}
		$classes[] = 'entry';
		$classes[] = 'content-bg';
		if ( is_singular() && is_main_query() && get_queried_object_id() === $post_id ) {
			$classes[] = 'entry-content-wrap';
		} else {
			$classes[] = 'loop-entry';
		}
		return $classes;
	}
	/**
	 * Adds results count and catalog ordering.
	 *
	 * @param array        $classes Current classes.
	 * @param string|array $class Additional class.
	 * @param int          $post_id Post ID.
	 */
	public function add_woo_cat_entry_classes( $classes, $class = '', $post_id = 0 ) {
		$classes[] = 'entry';
		$classes[] = 'content-bg';
		$classes[] = 'loop-entry';
		return $classes;
	}

	/**
	 * Adds results count and catalog ordering.
	 */
	public function archive_loop_top() {
		global $wp_query;
		if ( 0 === $wp_query->found_posts || ! woocommerce_products_will_display() ) {
			return;
		}
		if ( webapp()->option( 'product_archive_show_results_count' ) || webapp()->option( 'product_archive_show_order' ) || webapp()->option( 'product_archive_toggle' ) || apply_filters( 'base_product_archive_show_top_row', false ) ) {
			echo '<div class="base-shop-top-row">';
			do_action( 'base_woocommerce_before_shop_loop_top_row' );
			if ( webapp()->option( 'product_archive_show_results_count' ) ) {
				echo '<div class="base-shop-top-item base-woo-results-count">';
					woocommerce_result_count();
				echo '</div>';
			}
			if ( webapp()->option( 'product_archive_show_order' ) ) {
				echo '<div class="base-shop-top-item base-woo-ordering">';
					woocommerce_catalog_ordering();
				echo '</div>';
			}
			if ( webapp()->option( 'product_archive_toggle' ) ) {
				echo '<div class="base-shop-top-item base-woo-toggle">';
					$this->toggle_list();
				echo '</div>';
			}
			do_action( 'base_woocommerce_after_shop_loop_top_row' );
			echo '</div>';
		}
	}
	/**
	 * Adds toggle list option.
	 */
	public function toggle_list() {
		wp_enqueue_script( 'base-shop-toggle' );
		if ( 1 === wc_get_loop_prop( 'columns' ) ) {
			echo '<div class="base-product-toggle-container base-product-toggle-outer base-single-to-grid">';
				echo '<button title="' . esc_attr__( 'List View', 'avanam' ) . '" class="base-toggle-shop-layout base-toggle-list toggle-active" data-archive-toggle="list">';
					webapp()->print_icon( 'list', '', false );
				echo '</button>';
				echo '<button title="' . esc_attr__( 'Grid View', 'avanam' ) . '" class="base-toggle-shop-layout base-toggle-grid" data-archive-toggle="grid">';
					webapp()->print_icon( 'grid', '', false );
				echo '</button>';
			echo '</div>';
		} else {
			echo '<div class="base-product-toggle-container base-product-toggle-outer">';
				echo '<button title="' . esc_attr__( 'Grid View', 'avanam' ) . '" class="base-toggle-shop-layout base-toggle-grid toggle-active" data-archive-toggle="grid">';
					webapp()->print_icon( 'grid', '', false );
				echo '</button>';
				echo '<button title="' . esc_attr__( 'List View', 'avanam' ) . '" class="base-toggle-shop-layout base-toggle-list" data-archive-toggle="list">';
					webapp()->print_icon( 'list', '', false );
				echo '</button>';
			echo '</div>';
		}
	}
	/**
	 * Adds theme support for the Woocommerce plugin.
	 */
	public function action_add_woocommerce_support() {
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-slider' );
		add_theme_support( 'wc-product-gallery-lightbox' );

		add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
	}
	/**
	 * Adds Breadcrumb for single products.
	 */
	public function output_product_above() {
		if ( is_product() && 'breadcrumbs' === webapp()->option( 'product_above_layout' ) ) {
			// Make sure not using elementor template.
			if ( class_exists( '\ElementorPro\Plugin' ) ) {
				$conditions_manager = \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'theme-builder' )->get_conditions_manager();
				$documents = $conditions_manager->get_documents_for_location( 'single' );
				if ( empty( $documents ) ) {
					echo '<div class="product-title product-above">';
					get_template_part( 'template-parts/title/breadcrumb' );
					echo '</div>';
				}
			} else {
				echo '<div class="product-title product-above">';
				get_template_part( 'template-parts/title/breadcrumb' );
				echo '</div>';
			}
		}
	}
	/**
	 * Adds Title Area for single products.
	 */
	public function output_product_above_title() {
		if ( is_product() && 'title' === webapp()->option( 'product_above_layout' ) ) {
			get_template_part( 'template-parts/content/entry_hero' );
		}
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
		echo '<div id="primary" class="content-area"><div class="content-container site-container"><main id="main" class="site-main" role="main">';
		if ( ! is_product() && webapp()->show_in_content_title() ) {
			get_template_part( 'template-parts/content/archive_header' );
		}
	}

	/**
	 * Adds theme end output Wrapper.
	 */
	public function output_content_wrapper_end() {
		echo '</main>';
		get_sidebar();
		echo '</div></div>';
	}
}
