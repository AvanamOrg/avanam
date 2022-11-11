<?php
/**
 * Base\Microdata\Component class
 *
 * @package Base
 */

namespace Base\Microdata;

use Base\Component_Interface;
use Base\Templating_Component_Interface;
use function apply_filters;

/**
 * Class for managing Microdata support.
 *
 * Exposes template tags:
 * * `webapp()->print_microdata()`
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'microdata';
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
			'print_microdata' => array( $this, 'print_microdata' ),
		);
	}

	/**
	 * Prints microdata directly into html elements.
	 *
	 * @param string $context html context for microdata.
	 */
	public function print_microdata( string $context ) {

		// If not using, return early.
		if ( ! apply_filters( 'base_microdata', true, $context ) ) {
			return;
		}

		echo $this->get_microdata( $context ); // phpcs:ignore
	}

	/**
	 * Get any necessary microdata.
	 *
	 * @param string $context The element to target.
	 * @return string Our final attribute to add to the element.
	 */
	public function get_microdata( $context ) {
		$data = false;

		if ( 'html' === $context ) {
			$type = 'WebPage';

			if ( is_home() || is_archive() || is_attachment() || is_tax() || is_single() ) {
				$type = 'Blog';
			} elseif ( is_author() ) {
				$type = 'ProfilePage';
			}

			if ( is_search() ) {
				$type = 'SearchResultsPage';
			}

			$type = apply_filters( 'base_html_itemtype', $type );

			$data = sprintf(
				'itemtype="https://schema.org/%s" itemscope',
				esc_html( $type )
			);
		}

		if ( 'header' === $context ) {
			$data = 'itemtype="https://schema.org/WPHeader" itemscope';
		}

		if ( 'navigation' === $context ) {
			$data = 'itemtype="https://schema.org/SiteNavigationElement" itemscope';
		}

		if ( 'article' === $context ) {
			$type = apply_filters( 'base_article_itemtype', 'CreativeWork' );

			$data = sprintf(
				'itemtype="https://schema.org/%s" itemscope',
				esc_html( $type )
			);
		}

		if ( 'post-author' === $context ) {
			$data = 'itemprop="author" itemtype="https://schema.org/Person" itemscope';
		}

		if ( 'comment-body' === $context ) {
			$data = 'itemtype="https://schema.org/Comment" itemscope';
		}

		if ( 'comment-author' === $context ) {
			$data = 'itemprop="author" itemtype="https://schema.org/Person" itemscope';
		}

		if ( 'sidebar' === $context ) {
			$data = 'itemtype="https://schema.org/WPSideBar" itemscope';
		}

		if ( 'footer' === $context ) {
			$data = 'itemtype="https://schema.org/WPFooter" itemscope';
		}
		if ( 'video' === $context ) {
			$data = 'itemprop="video" itemtype="http://schema.org/VideoObject" itemscope';
		}

		if ( $data ) {
			return apply_filters( "base_{$context}_schema", $data );
		}
	}
}
