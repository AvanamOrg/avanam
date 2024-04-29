<?php
/**
 * Base\Breadcrumbs\Component class
 *
 * @package Base
 */

namespace Base\Breadcrumbs;

use Base\Component_Interface;
use Base\Templating_Component_Interface;
use function Base\webapp;
use WPSEO_Primary_Term;
use WPSEO_Taxonomy_Meta;
use WP_Post;
use function add_action;
use function add_filter;
use function wp_enqueue_script;
use function get_theme_file_uri;
use function get_theme_file_path;
use function wp_script_add_data;
use function wp_localize_script;
use function rank_math_the_breadcrumbs;
use function yoast_breadcrumb;
use function seopress_display_breadcrumbs;
use function is_bbpress;
use function bbp_breadcrumb;

/**
 * Class for adding custom header support.
 *
 * Exposes template tags:
 * * `webapp()->get_breadcrumb()`
 */
class Component implements Component_Interface, Templating_Component_Interface {
	/**
	 * Instance Control
	 *
	 * @var array
	 */
	protected static $instance = null;

	/**
	 * Breadcrumb separator.
	 *
	 * @var string/null
	 */
	private $sep = null;

	/**
	 * Breadcrumb link.
	 *
	 * @var string/null
	 */
	private $link = null;

	/**
	 * Breadcrumb settings.
	 *
	 * @var array
	 */
	private $settings = array();

	/**
	 * Local breadcrumb args.
	 *
	 * @var array
	 */
	private $args = array();

	/**
	 * Breadcrumb post types.
	 *
	 * @var array
	 */
	private $post_types = array();
	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'breadcrumbs';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
	}

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `webapp()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() : array {
		return array(
			'get_breadcrumb' => array( $this, 'get_breadcrumb' ),
			'print_breadcrumb' => array( $this, 'print_breadcrumb' ),
		);
	}
	/**
	 * Get the breadcrumbs.
	 *
	 * @param array $args Arguments.
	 */
	public function print_breadcrumb( $args = array() ) {
		if ( 'rankmath' === webapp()->option( 'breadcrumb_engine' ) ) {
			if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
				echo '<div class="base-breadcrumbs rankmath-bc-wrap">';
				rank_math_the_breadcrumbs();
				echo '</div>';
			}
		} elseif ( 'yoast' === webapp()->option( 'breadcrumb_engine' ) ) {
			if ( function_exists( 'yoast_breadcrumb' ) ) {
				yoast_breadcrumb( '<div class="base-breadcrumbs yoast-bc-wrap">','</div>' );
			}
		} elseif ( 'seopress' === webapp()->option( 'breadcrumb_engine' ) ) {
			if ( function_exists( 'seopress_display_breadcrumbs' ) ) {
				echo '<div class="base-breadcrumbs seopress-bc-wrap">';
				seopress_display_breadcrumbs();
				echo '</div>';
			}
		} else {
			if ( function_exists( 'is_bbpress' ) && is_bbpress() && function_exists( 'bbp_breadcrumb' ) ) {
				echo '<div class="base-breadcrumbs bbpress-topic-meta">';
				bbp_breadcrumb();
				echo '</div>';
			} else {
				echo webapp()->get_breadcrumb( $args );
			}
		}
	}
	/**
	 * Get the breadcrumbs.
	 *
	 * @param array $args Arguments.
	 * @return string
	 */
	public function get_breadcrumb( $args = array() ) {
		$this->args = apply_filters(
			'base_local_breadcrumb_args',
			wp_parse_args(
				$args,
				array(
					'home_title'     => __( 'Home', 'avanam' ),
					'404_title'      => __( 'Error 404', 'avanam' ),
					'search_title'   => __( 'Search results for', 'avanam' ),
					'page'           => __( 'Page', 'avanam' ),
					'show_shop'      => true,
					'show_title'     => true,
					'color_style'    => '',
					'blog_id'        => '',
					'portfolio_id'   => '',
					'staff_id'       => '',
					'testimonial_id' => '',
					'gallery_id'     => 'none',
				)
			)
		);
		$this->settings = wp_parse_args(
			apply_filters( 'base_breadcrumb_args', array() ),
			array(
				'home'             => true,
				'home_icon'        => webapp()->option( 'breadcrumb_home_icon' ),
				'before'           => '<span class="base-bread-current">',
				'after'            => '</span>',
				'home_link'        => home_url( '/' ),
				'wrap_before'      => '<nav id="base-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumbs', 'avanam' ) . '"  class="base-breadcrumbs"><div class="base-breadcrumb-container"' . ( $this->args['color_style'] ? ' style="' . esc_attr( $this->args['color_style'] ) . '"' : '' ) . '>',
				'wrap_after'       => '</div></nav>',
				'delimiter'        => apply_filters( 'base_breadcrumb_delimiter', '/' ),
				'delimiter_before' => '<span class="bc-delimiter">',
				'delimiter_after'  => '</span>',
				'link_before'      => '<span>',
				'link_after'       => '</span>',
				'link_in_before'   => '<span>',
				'link_in_after'    => '</span>',
			)
		);
		$this->post_types = wp_parse_args(
			apply_filters( 'base_breadcrumb_post_types', array() ),
			array(
				'product'      => array(
					'post_type'     => 'product',
					'taxonomy'      => 'product_cat',
					'archive_page'  => 'shop',
					'archive_label' => '',
				),
				'portfolio'    => array(
					'post_type'     => 'portfolio',
					'taxonomy'      => 'portfolio-type',
					'archive_page'  => $this->args['portfolio_id'],
					'archive_label' => '',
				),
				'post'         => array(
					'post_type'     => 'post',
					'taxonomy'      => 'category',
					'archive_page'  => $this->args['blog_id'],
					'archive_label' => '',
				),
				'staff'        => array(
					'post_type'     => 'staff',
					'taxonomy'      => 'staff-group',
					'archive_page'  => $this->args['staff_id'],
					'archive_label' => '',
				),
				'testimonial'  => array(
					'post_type'     => 'testimonial',
					'taxonomy'      => 'testimonial-group',
					'archive_page'  => $this->args['testimonial_id'],
					'archive_label' => '',
				),
				'bt_gallery'  => array(
					'post_type'     => 'bt_gallery',
					'taxonomy'      => 'bt_album',
					'archive_page'  => $this->args['gallery_id'],
					'archive_label' => '',
				),
				'tribe_events' => array(
					'post_type'     => 'tribe_events',
					'taxonomy'      => 'tribe_events_cat',
					'archive_page'  => 'tribe_events',
					'archive_label' => '',
				),
				'event'        => array(
					'post_type'     => 'event',
					'taxonomy'      => 'event-category',
					'archive_page'  => '',
					'archive_label' => '',
				),
				'podcast'      => array(
					'post_type'     => 'podcast',
					'taxonomy'      => 'series',
					'archive_page'  => '',
					'archive_label' => '',
				),
				'course'      => array(
					'post_type'     => 'course',
					'taxonomy'      => 'course_cat',
					'archive_page'  => get_option( 'lifterlms_shop_page_id' ),
					'archive_label' => '',
				),
				'lesson'      => array(
					'post_type'     => 'lesson',
					'taxonomy'      => '',
					'custom'        => 'liferlms',
					'archive_page'  => get_option( 'lifterlms_shop_page_id' ),
					'archive_label' => '',
				),
				'ht_kb'      => array(
					'post_type'     => 'ht_kb',
					'taxonomy'      => 'ht_kb_category',
					'archive_page'  => 'archive',
					'archive_label' => esc_html__( 'Knowledge Base', 'avanam' ),
				),
			)
		);
		$html = '';

		if ( ! is_front_page() ) {
			$html .= $this->settings['wrap_before'];
			if ( $this->settings['home'] ) {
				$html .= $this->get_crumbs_frontpage() . $this->get_sep();
			}
			$html = apply_filters( 'base_breadcrumbs_after_home', $html );
			if ( is_home() ) {
				$html .= $this->get_crumbs_home();
			} elseif ( is_404() ) {
				$html .= $this->get_crumbs_404();
			} elseif ( is_search() ) {
				$html .= $this->get_crumbs_search();
			} elseif ( is_attachment() ) {
				$html .= $this->get_crumbs_attachment();
			} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
				$html .= $this->get_crumbs_shop();
			} elseif ( is_single() ) {
				$html .= $this->get_crumbs_single();
			} elseif ( is_page() ) {
				$html .= $this->get_crumbs_page();
			} elseif ( is_singular() && ! is_attachment() ) {
				$html .= $this->get_crumbs_single();
			} elseif ( function_exists( 'is_product_category' ) && is_product_category() ) {
				$html .= $this->get_crumbs_product_category();
			} elseif ( function_exists( 'is_product_tag' ) && is_product_tag() ) {
				$html .= $this->get_crumbs_product_tag();
			} elseif ( $this->is_wc_attribute() ) {
				$html .= $this->get_crumbs_product_attribute();
			}  elseif ( function_exists( 'dokan_is_store_page' ) && dokan_is_store_page() ) {
				$html .= $this->get_crumbs_dokan_store();
			} elseif ( is_category() ) {
				$html .= $this->get_crumbs_category();
			} elseif ( is_tag() ) {
				$html .= $this->get_crumbs_tag();
			} elseif ( is_tax() ) {
				$html .= $this->get_crumbs_tax();
			} elseif ( is_date() ) {
				$html .= $this->get_crumbs_date();
			} elseif ( is_author() ) {
				$html .= $this->get_crumbs_author();
			} elseif ( is_archive() ) {
				$html .= $this->get_crumbs_archive();
			} else {
				$html .= $this->settings['before'] . get_the_title() . $this->settings['after'];
			}

			if ( get_query_var( 'paged' ) ) {
				$html .= ' - ' . $this->args['page'] . ' ' . esc_html( get_query_var( 'paged' ) );
			}

			$html .= $this->settings['wrap_after'];
		}
		/**
		 * Change the breadcrumbs HTML output.
		 *
		 * @param string      $html   HTML output.
		 */
		return apply_filters( 'base_breadcrumb_html', $html );
	}
	/**
	 * Check for wc_attribute_archive.
	 */
	public function is_wc_attribute() {

		/** 
		 * Attributes are proper taxonomies, therefore first thing is 
		 * to check if we are on a taxonomy page using the is_tax(). 
		 * Also, a further check if the taxonomy_is_product_attribute 
		 * function exists is necessary, in order to ensure that this 
		 * function does not produce fatal errors when the WooCommerce 
		 * is not  activated
		 */
		if ( is_tax() && function_exists( 'taxonomy_is_product_attribute') ) { 
			// now we know for sure that the queried object is a taxonomy
			$tax_obj = get_queried_object();
			return taxonomy_is_product_attribute( $tax_obj->taxonomy );
		}
		return false;
	}
	/**
	 * Get Separator
	 */
	public function get_sep() {
		if ( is_null( $this->sep ) ) {
			$this->sep = ' ' . $this->settings['delimiter_before'] . $this->settings['delimiter'] . $this->settings['delimiter_after'] . ' ';
		}
		return $this->sep;
	}
	/**
	 * Get link string
	 */
	public function get_link() {
		if ( is_null( $this->link ) ) {
			$this->link = $this->settings['link_before'] . '<a href="%1$s" itemprop="url" ' . ( $this->args['color_style'] ? 'style="' . esc_attr( $this->args['color_style'] ) . '"' : '' ) . '>' . $this->settings['link_in_before'] . '%2$s' . $this->settings['link_in_after'] . '</a>' . $this->settings['link_after'];
		}
		return $this->link;
	}
	/**
	 * Get Breadcrumb Term Title
	 *
	 * @param object $term the current term.
	 */
	private function get_breadcrumb_term_title( $term ) {
		$title = '';
		if ( class_exists( 'WPSEO_Taxonomy_Meta' ) ) {
			$title = WPSEO_Taxonomy_Meta::get_term_meta( $term, $term->taxonomy, 'bctitle' );
		} elseif ( class_exists( 'RankMath' ) ) {
			$title = get_metadata( 'term', $term->term_id, 'rank_math_breadcrumb_title', false );
			if ( is_array( $title ) && ! empty( $title ) ) {
				$title = $title[0];
			}
		}
		if ( ! is_string( $title ) || '' === $title ) {
			$title = $term->name;
		}

		return $title;
	}
	/**
	 * Get Home Breadcrumb
	 */
	private function get_crumbs_frontpage() {
		if ( $this->settings['home_icon'] ) {
			$output = $this->settings['link_before'] . '<a href="' . esc_url( $this->settings['home_link'] ) . '" title="'. esc_attr( $this->args['home_title'] ) . '" itemprop="url" class="base-bc-home base-bc-home-icon" ' . ( $this->args['color_style'] ? 'style="' . esc_attr( $this->args['color_style'] ) . '"' : '' ) . '>' . $this->settings['link_in_before'] . webapp()->get_icon( 'home' ) . $this->settings['link_in_after'] . '</a>' . $this->settings['link_after'];
		} else {
			$output = $this->settings['link_before'] . '<a href="' . esc_url( $this->settings['home_link'] ) . '" itemprop="url" class="base-bc-home" ' . ( $this->args['color_style'] ? 'style="' . esc_attr( $this->args['color_style'] ) . '"' : '' ) . '>' . $this->settings['link_in_before'] . esc_html( $this->args['home_title'] ) . $this->settings['link_in_after'] . '</a>' . $this->settings['link_after'];
		}
		return $output;
	}

	/**
	 * Get Home Breadcrumb
	 */
	private function get_crumbs_home() {
		return $this->settings['before'] . get_the_title( get_option( 'page_for_posts' ) ) . $this->settings['after'];
	}

	/**
	 * Get 404 Breadcrumb
	 */
	private function get_crumbs_404() {
		return $this->settings['before'] . $this->args['404_title'] . $this->settings['after'];
	}

	/**
	 * Get search Breadcrumb
	 */
	private function get_crumbs_search() {
		$html = '';
		if ( array_key_exists( 'ht-kb-search', $_REQUEST ) ) {
			if ( $this->post_types['ht_kb']['archive_page'] ) {
				$html .= $this->get_archive_crumb( $this->post_types['ht_kb']['archive_page'], $this->post_types['ht_kb']['archive_label'] );
			}
		}
		return $html . $this->settings['before'] . $this->args['search_title'] . ' "' . get_search_query() . '"' . $this->settings['after'];
	}

	/**
	 * Get attachment Breadcrumb
	 */
	private function get_crumbs_attachment() {
		global $post;
		$parent_id    = $post->post_parent;
		$html         = '';
		$parentcrumbs = array();

		if ( $parent_id ) {
			while ( $parent_id ) {
				$page           = get_page( $parent_id );
				$parentcrumbs[] = sprintf( $this->get_link(), get_permalink( $page->ID ), get_the_title( $page->ID ) ) . $this->get_sep();
				$parent_id      = $page->post_parent;
			}
		}
		$parentcrumbs = array_reverse( $parentcrumbs );
		foreach ( $parentcrumbs as $parentcrumb ) {
			$html .= $parentcrumb;
		}
		$html .= $this->settings['before'] . get_the_title() . $this->settings['after'];
		return $html;
	}

	/**
	 * Get shop page Breadcrumb
	 */
	private function get_crumbs_shop() {
		$shop_page_id = wc_get_page_id( 'shop' );
		$page_title   = get_the_title( $shop_page_id );
		return $this->settings['before'] . $page_title . $this->settings['after'];
	}

	/**
	 * Get shop Breadcrumb
	 */
	private function get_shop_crumb() {
		$shop_page_id = wc_get_page_id( 'shop' );
		$shop_page    = get_post( $shop_page_id );
		$shop_bread   = '';
		if ( get_option( 'page_on_front' ) !== $shop_page_id ) {
			$shop_bread = sprintf( $this->get_link(), get_permalink( $shop_page ), get_the_title( $shop_page_id ) ) . $this->get_sep();
		}
		return $shop_bread;
	}

	/**
	 * Get product category
	 */
	private function get_crumbs_product_category() {
		$html = '';
		if ( $this->args['show_shop'] ) {
			$html .= $this->get_shop_crumb();
		}
		$ancestors = get_ancestors( get_queried_object()->term_id, 'product_cat' );
		$ancestors = array_reverse( $ancestors );
		foreach ( $ancestors as $ancestor ) {
			$ancestor = get_term( $ancestor, 'product_cat' );
			$html    .= sprintf( $this->get_link(), get_term_link( $ancestor->slug, 'product_cat' ), $this->get_breadcrumb_term_title( $ancestor ) ) . $this->get_sep();
		}
		return $html . $this->settings['before'] . $this->get_breadcrumb_term_title( get_queried_object() ) . $this->settings['after'];
	}
	/**
	 * Get product tag
	 */
	private function get_crumbs_product_tag() {
		$html = '';
		if ( $this->args['show_shop'] ) {
			$html .= $this->get_shop_crumb();
		}
		return $html . $this->settings['before'] . $this->get_breadcrumb_term_title( get_queried_object() ) . $this->settings['after'];
	}
	/**
	 * Get product attributes.
	 */
	private function get_crumbs_product_attribute() {
		$html = '';
		if ( $this->args['show_shop'] ) {
			$html .= $this->get_shop_crumb();
		}
		return $html . $this->settings['before'] . $this->get_breadcrumb_term_title( get_queried_object() ) . $this->settings['after'];
	}
	/**
	 * Get dokan store.
	 */
	private function get_crumbs_dokan_store() {
		$html = '';
		$store_user = dokan()->vendor->get( get_query_var( 'author' ) );
		return $html . $this->settings['before'] . $store_user->get_shop_name() . $this->settings['after'];
	}
	/**
	 * Check if has post archive.
	 *
	 * @param string $post_type the post type.
	 */
	private function check_has_archive( $post_type ) {
		if ( ! isset( $post_type ) ) {
			return false;
		}
		// find custom post types with archives.
		$args = array(
			'has_archive' => true,
			'_builtin'    => false,
		);
		$output = 'names';
		$archived_custom_post_types = get_post_types( $args, $output );

		// if there are no custom post types, then the current post can't be one.
		if ( empty( $archived_custom_post_types ) ) {
			return false;
		}
		// check if post type is a supports archives.
		if ( in_array( $post_type, $archived_custom_post_types ) ) {
			return true;
		} else {
			return false;
		}
		// if all else fails, return false.
		return false;
	}
	/**
	 * Get archive Breadcrumb
	 *
	 * @param mixed  $archive_page the archive page for breadcrumbs.
	 * @param string $archive_label the archive page label for breadcrumbs.
	 */
	private function get_archive_crumb( $archive_page, $archive_label = '' ) {
		$html = '';
		if ( is_numeric( $archive_page ) ) {
			// Check if page ID.
			$parent_page_link = get_page_link( $archive_page );
			if ( $parent_page_link ) {
				$html .= sprintf( $this->get_link(), $parent_page_link, get_the_title( $archive_page ) ) . $this->get_sep();
			}
		} elseif ( 'shop' === $archive_page ) {
			if ( ! is_archive() && ! $this->args['show_shop'] ) {
				$html .= '';
			} else {
				$html .= $this->get_shop_crumb();
			}
		} elseif ( 'tribe_events' === $archive_page ) {
			// Check for tribe.
			$html .= sprintf( $this->get_link(), tribe_get_events_link(), tribe_get_event_label_plural() ) . $this->get_sep();
		} elseif ( 'none' === $archive_page ) {
			// Check for none.
			$html .= '';
		} elseif ( 'archive' === $archive_page ) {
			$parent_title = ( ! empty( $archive_label ) ? $archive_label : 'Archive' );
			$html        .= sprintf( $this->get_link(), get_post_type_archive_link( get_post_type() ), $parent_title ) . $this->get_sep();
		} elseif ( filter_var( $archive_page, FILTER_VALIDATE_URL ) ) {
			// Check if url.
			$parent_title = ( ! empty( $archive_label ) ? $archive_label : 'Archive' );
			$html        .= sprintf( $this->get_link(), $archive_page, $parent_title ) . $this->get_sep();
		} elseif ( $this->check_has_archive( get_post_type() ) ) {
			$post_type_obj = get_post_type_object( get_post_type() );
			$parent_title  = apply_filters( 'post_type_archive_title', $post_type_obj->labels->name, get_post_type() );
			$html        .= sprintf( $this->get_link(), get_post_type_archive_link( get_post_type() ), $parent_title ) . $this->get_sep();
		}
		return $html;
	}
	/**
	 * Get main tax.
	 *
	 * @param string $taxonomy the taxonomy name.
	 * @param number $post_id the post id.
	 */
	private function get_taxonomy_main( $taxonomy, $post_id ) {
		$main_term = '';
		$terms     = wp_get_post_terms(
			$post_id,
			$taxonomy,
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
		return $main_term;
	}

	/**
	 * Get tax Breadcrumb
	 *
	 * @param string $taxonomy the taxonomy type for breadcrumbs.
	 */
	private function get_taxonomy_crumb( $taxonomy ) {
		global $post;
		$html      = '';
		$main_term = '';
		if ( class_exists( 'WPSEO_Primary_Term' ) ) {
			$wpseo_term = new WPSEO_Primary_Term( $taxonomy, $post->ID );
			$wpseo_term = $wpseo_term->get_primary_term();
			$wpseo_term = get_term( $wpseo_term );
			if ( is_wp_error( $wpseo_term ) ) {
				$main_term = $this->get_taxonomy_main( $taxonomy, $post->ID );
			} else {
				$main_term = $wpseo_term;
			}
		} elseif ( class_exists( 'RankMath' ) ) {
			$wpseo_term = get_post_meta( $post->ID, 'rank_math_primary_' . $taxonomy, true );
			if ( $wpseo_term ) {
				$wpseo_term = get_term( $wpseo_term );
				if ( is_wp_error( $wpseo_term ) ) {
					$main_term = $this->get_taxonomy_main( $taxonomy, $post->ID );
				} else {
					$main_term = $wpseo_term;
				}
			} else {
				$main_term = $this->get_taxonomy_main( $taxonomy, $post->ID );
			}
		} else {
			$main_term = $this->get_taxonomy_main( $taxonomy, $post->ID );
		}
		if ( $main_term && ! is_wp_error( $main_term ) ) {
			$ancestors = get_ancestors( $main_term->term_id, $taxonomy );
			$ancestors = array_reverse( $ancestors );
			foreach ( $ancestors as $ancestor ) {
				$ancestor = get_term( $ancestor, $taxonomy );
				$html    .= sprintf( $this->get_link(), get_term_link( $ancestor->slug, $taxonomy ), $this->get_breadcrumb_term_title( $ancestor ) ) . $this->get_sep();
			}
			$term_link = get_term_link( $main_term->slug, $taxonomy );
			if ( $term_link && ! is_wp_error( $term_link ) ) {
				$html .= sprintf( $this->get_link(), $term_link, $this->get_breadcrumb_term_title( $main_term ) ) . $this->get_sep();
			}
		}
		return $html;
	}


	/**
	 * Get Custom Breadcrumb
	 *
	 * @param string $custom the custom string for breadcrumbs.
	 */
	private function get_custom_crumb( $custom ) {
		global $post;
		$html      = '';
		if ( 'liferlms' === $custom ) {
			$parent_course = get_post_meta( $post->ID, '_llms_parent_course', true );
			if ( $parent_course ) {
				$html .= sprintf( $this->get_link(), get_permalink( $parent_course ), get_the_title( $parent_course ) ) . $this->get_sep();
			}
		}
		return $html;
	}

	/**
	 * Get category
	 */
	private function get_crumbs_category() {
		$html = '';
		if ( $this->post_types['post']['archive_page'] ) {
			$html .= $this->get_archive_crumb( $this->post_types['post']['archive_page'], $this->post_types['post']['archive_label'] );
		}
		$ancestors = get_ancestors( get_queried_object()->term_id, 'category' );
		$ancestors = array_reverse( $ancestors );
		foreach ( $ancestors as $ancestor ) {
			$ancestor = get_term( $ancestor, 'category' );
			$html    .= sprintf( $this->get_link(), get_term_link( $ancestor->slug, 'category' ), $this->get_breadcrumb_term_title( $ancestor ) ) . $this->get_sep();
		}
		$title_output = ( $this->args['show_title'] ? $this->settings['before'] . $this->get_breadcrumb_term_title( get_queried_object() ) . $this->settings['after'] : '' );
		return $html . $title_output;
	}

	/**
	 * Get tag
	 */
	private function get_crumbs_tag() {
		$html = '';
		if ( $this->post_types['post']['archive_page'] ) {
			$html .= $this->get_archive_crumb( $this->post_types['post']['archive_page'], $this->post_types['post']['archive_label'] );
		}
		$title_output = ( $this->args['show_title'] ? $this->settings['before'] . $this->get_breadcrumb_term_title( get_queried_object() ) . $this->settings['after'] : '' );
		return $html . $title_output;
	}

	/**
	 * Get author
	 */
	private function get_crumbs_author() {
		global $author;
		$html = '';
		if ( $this->post_types['post']['archive_page'] ) {
			$html .= $this->get_archive_crumb( $this->post_types['post']['archive_page'], $this->post_types['post']['archive_label'] );
		}
		$userdata = get_userdata( $author );
		$title_output = ( $this->args['show_title'] ? $this->settings['before'] . $userdata->display_name . $this->settings['after'] : '' );
		return $html . $title_output;
	}
	/**
	 * Get author
	 */
	private function get_crumbs_archive() {
		$html = '';
		$title_output = ( $this->args['show_title'] ? $this->settings['before'] . get_the_archive_title() . $this->settings['after'] : '' );
		return $html . $title_output;
	}
	/**
	 * Get date
	 */
	private function get_crumbs_date() {
		$html = '';
		if ( $this->post_types['post']['archive_page'] ) {
			$html .= $this->get_archive_crumb( $this->post_types['post']['archive_page'], $this->post_types['post']['archive_label'] );
		}
		if ( is_day() ) {
			$html .= sprintf( $this->get_link(), get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) ) . $this->get_sep();
			$html .= sprintf( $this->get_link(), get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ), get_the_time( 'F' ) ) . $this->get_sep();
			$title = get_the_time( 'd' );
		} elseif ( is_month() ) {
			$html .= sprintf( $this->get_link(), get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) ) . $this->get_sep();
			$title = get_the_time( 'F' );
		} else {
			$title = get_the_time( 'Y' );
		}
		$title_output = ( $this->args['show_title'] ? $this->settings['before'] . $title . $this->settings['after'] : '' );
		return $html . $title_output;
	}
	/**
	 * Get tax
	 */
	private function get_crumbs_tax() {
		$html = '';
		if ( is_tax( 'portfolio-type' ) || is_tax( 'portfolio-tag' ) ) {
			if ( $this->post_types['portfolio']['archive_page'] ) {
				$html .= $this->get_archive_crumb( $this->post_types['portfolio']['archive_page'], $this->post_types['portfolio']['archive_label'] );
			}
		} elseif ( is_tax( 'staff-group' ) ) {
			if ( $this->post_types['staff']['archive_page'] ) {
				$html .= $this->get_archive_crumb( $this->post_types['staff']['archive_page'], $this->post_types['staff']['archive_label'] );
			}
		} elseif ( is_tax( 'testimonial-group' ) ) {
			if ( $this->post_types['testimonial']['archive_page'] ) {
				$html .= $this->get_archive_crumb( $this->post_types['testimonial']['archive_page'], $this->post_types['testimonial']['archive_label'] );
			}
		} elseif ( is_tax( 'event-category' ) ) {
			if ( $this->post_types['event']['archive_page'] ) {
				$html .= $this->get_archive_crumb( $this->post_types['event']['archive_page'], $this->post_types['event']['archive_label'] );
			}
		} elseif ( is_tax( 'series' ) ) {
			if ( $this->post_types['podcast']['archive_page'] ) {
				$html .= $this->get_archive_crumb( $this->post_types['podcast']['archive_page'], $this->post_types['podcast']['archive_label'] );
			}
		} elseif ( is_tax( 'ht_kb_category' ) ) {
			if ( $this->post_types['ht_kb']['archive_page'] ) {
				$html .= $this->get_archive_crumb( $this->post_types['ht_kb']['archive_page'], $this->post_types['ht_kb']['archive_label'] );
			}
		} elseif ( is_tax( 'course_cat' ) || is_tax( 'course_tag' ) || is_tax( 'course_track' ) ) {
			if ( $this->post_types['course']['archive_page'] ) {
				$html .= $this->get_archive_crumb( $this->post_types['course']['archive_page'], $this->post_types['course']['archive_label'] );
			}
		}
		$title = ( $this->args['show_title'] ? $this->settings['before'] . $this->get_breadcrumb_term_title( get_queried_object() ) . $this->settings['after'] : '' );
		return $html . $title;
	}
	/**
	 * Get page
	 */
	private function get_crumbs_page() {
		global $post;
		$html         = '';
		$parent_id    = $post->post_parent;
		$parentcrumbs = array();
		if ( $parent_id ) {
			while ( $parent_id ) {
				$page           = get_page( $parent_id );
				$parentcrumbs[] = sprintf( $this->get_link(), get_permalink( $page->ID ), get_the_title( $page->ID ) ) . $this->get_sep();
				$parent_id      = $page->post_parent;
			}
		}
		$parentcrumbs = array_reverse( $parentcrumbs );
		foreach ( $parentcrumbs as $parentcrumb ) {
			$html .= $parentcrumb;
		}
		$title = ( $this->args['show_title'] ? $this->settings['before'] . get_the_title() . $this->settings['after'] : '' );
		return $html . $title;
	}
	/**
	 * Get single
	 */
	private function get_crumbs_single() {
		$html      = '';
		$post_type = get_post_type();
		if ( isset( $this->post_types[ $post_type ] ) ) {
			// Archive Page.
			$html .= $this->get_archive_crumb( $this->post_types[ $post_type ]['archive_page'], $this->post_types[ $post_type ]['archive_label'] );
			// Tax Page.
			$html .= $this->get_taxonomy_crumb( $this->post_types[ $post_type ]['taxonomy'] );
			// Custom Parent.
			if ( isset( $this->post_types[ $post_type ]['custom'] ) ) {
				$html .= $this->get_custom_crumb( $this->post_types[ $post_type ]['custom'] );
			}
		} else {
			// Archive Page.
			$html .= $this->get_archive_crumb( '', '' );
		}
		$title = ( $this->args['show_title'] ? $this->settings['before'] . get_the_title() . $this->settings['after'] : '' );
		return $html . $title;
	}
}
