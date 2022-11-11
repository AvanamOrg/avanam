<?php
/**
 * Base\Custom_Header\Component class
 *
 * @package Base
 */

namespace Base\Custom_Header;

use Base\Component_Interface;
use Base\Templating_Component_Interface;
use function add_action;
use function apply_filters;
use function Base\webapp;
use function get_template_part;

/**
 * Class for adding custom header support.
 *
 * Exposes template tags:
 * * `webapp()->render_header()`
 * * `webapp()->display_header_row()`
 * * `webapp()->has_center_column()`
 * * `webapp()->has_side_columns()`
 * * `webapp()->display_mobile_header_row()`
 * * `webapp()->has_mobile_center_column()`
 * * `webapp()->has_mobile_side_columns()`
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Holds center column display.
	 *
	 * @var value for center column;
	 */
	protected static $center = array();

	/**
	 * Holds sides column display.
	 *
	 * @var value for center column;
	 */
	protected static $sides = array();

	/**
	 * Holds sides column display.
	 *
	 * @var value for center column;
	 */
	protected static $mobile_sides = array();

	/**
	 * Holds center column display.
	 *
	 * @var value for center column;
	 */
	protected static $mobile_center = array();

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'custom_header';
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
			'render_header'             => array( $this, 'render_header' ),
			'display_header_row'        => array( $this, 'display_header_row' ),
			'has_center_column'         => array( $this, 'has_center_column' ),
			'has_side_columns'          => array( $this, 'has_side_columns' ),
			'display_mobile_header_row' => array( $this, 'display_mobile_header_row' ),
			'has_mobile_center_column'  => array( $this, 'has_mobile_center_column' ),
			'has_mobile_side_columns'   => array( $this, 'has_mobile_side_columns' ),
		);
	}
	/**
	 * Adds support to render header columns.
	 *
	 * @param string $row the name of the row.
	 */
	public function display_header_row( $row = 'main' ) {
		$display = false;
		foreach ( array( 'left', 'center', 'right' ) as $column ) {
			$elements = webapp()->option( 'header_desktop_items' );
			if ( isset( $elements ) && isset( $elements[ $row ] ) && isset( $elements[ $row ][ $row . '_' . $column ] ) && is_array( $elements[ $row ][ $row . '_' . $column ] ) && ! empty( $elements[ $row ][ $row . '_' . $column ] ) ) {
				$display = true;
				break;
			}
		}
		return $display;
	}

	/**
	 * Adds support to render header columns.
	 *
	 * @param string $row the name of the row.
	 */
	public function display_mobile_header_row( $row = 'main' ) {
		$display = false;
		foreach ( array( 'left', 'center', 'right' ) as $column ) {
			$elements = webapp()->option( 'header_mobile_items' );
			if ( isset( $elements ) && isset( $elements[ $row ] ) && isset( $elements[ $row ][ $row . '_' . $column ] ) && is_array( $elements[ $row ][ $row . '_' . $column ] ) && ! empty( $elements[ $row ][ $row . '_' . $column ] ) ) {
				$display = true;
				break;
			}
		}
		return $display;
	}

	/**
	 * Adds a check to see if the side columns should run.
	 *
	 * @param string $row the name of the row.
	 */
	public function has_side_columns( $row = 'main' ) {
		if ( isset( self::$sides[ $row ] ) ) {
			return self::$sides[ $row ];
		}
		$sides    = false;
		$elements = webapp()->option( 'header_desktop_items' );
		if ( isset( $elements ) && isset( $elements[ $row ] ) ) {
			if ( ( isset( $elements[ $row ][ $row . '_left' ] ) && is_array( $elements[ $row ][ $row . '_left' ] ) && ! empty( $elements[ $row ][ $row . '_left' ] ) ) || ( isset( $elements[ $row ][ $row . '_left_center' ] ) && is_array( $elements[ $row ][ $row . '_left_center' ] ) && ! empty( $elements[ $row ][ $row . '_left_center' ] ) ) || ( isset( $elements[ $row ][ $row . '_right_center' ] ) && is_array( $elements[ $row ][ $row . '_right_center' ] ) && ! empty( $elements[ $row ][ $row . '_right_center' ] ) ) || ( isset( $elements[ $row ][ $row . '_right' ] ) && is_array( $elements[ $row ][ $row . '_right' ] ) && ! empty( $elements[ $row ][ $row . '_right' ] ) ) ) {
				$sides = true;
			}
		}
		self::$sides[ $row ] = $sides;
		return $sides;
	}

	/**
	 * Adds a check to see if the side columns should run.
	 *
	 * @param string $row the name of the row.
	 */
	public function has_mobile_side_columns( $row = 'main' ) {
		if ( isset( self::$mobile_sides[ $row ] ) ) {
			return self::$mobile_sides[ $row ];
		}
		$mobile_sides = false;
		$elements     = webapp()->option( 'header_mobile_items' );
		if ( isset( $elements ) && isset( $elements[ $row ] ) ) {
			if ( ( isset( $elements[ $row ][ $row . '_left' ] ) && is_array( $elements[ $row ][ $row . '_left' ] ) && ! empty( $elements[ $row ][ $row . '_left' ] ) ) || ( isset( $elements[ $row ][ $row . '_left_center' ] ) && is_array( $elements[ $row ][ $row . '_left_center' ] ) && ! empty( $elements[ $row ][ $row . '_left_center' ] ) ) || ( isset( $elements[ $row ][ $row . '_right_center' ] ) && is_array( $elements[ $row ][ $row . '_right_center' ] ) && ! empty( $elements[ $row ][ $row . '_right_center' ] ) ) || ( isset( $elements[ $row ][ $row . '_right' ] ) && is_array( $elements[ $row ][ $row . '_right' ] ) && ! empty( $elements[ $row ][ $row . '_right' ] ) ) ) {
				$mobile_sides = true;
			}
		}
		self::$mobile_sides[ $row ] = $mobile_sides;
		return $mobile_sides;
	}

	/**
	 * Adds a check to see if the center column should run.
	 *
	 * @param string $row the name of the row.
	 */
	public function has_center_column( $row = 'main' ) {
		if ( isset( self::$center[ $row ] ) ) {
			return self::$center[ $row ];
		}
		$center   = false;
		$elements = webapp()->option( 'header_desktop_items' );
		if ( isset( $elements ) && isset( $elements[ $row ] ) && isset( $elements[ $row ][ $row . '_center' ] ) && is_array( $elements[ $row ][ $row . '_center' ] ) && ! empty( $elements[ $row ][ $row . '_center' ] ) ) {
			$center = true;
		}
		self::$center[ $row ] = $center;
		return $center;
	}

	/**
	 * Adds a check to see if the center column should run.
	 *
	 * @param string $row the name of the row.
	 */
	public function has_mobile_center_column( $row = 'main' ) {
		if ( isset( self::$mobile_center[ $row ] ) ) {
			return self::$mobile_center[ $row ];
		}
		$mobile_center = false;
		$elements      = webapp()->option( 'header_mobile_items' );
		if ( isset( $elements ) && isset( $elements[ $row ] ) && isset( $elements[ $row ][ $row . '_center' ] ) && is_array( $elements[ $row ][ $row . '_center' ] ) && ! empty( $elements[ $row ][ $row . '_center' ] ) ) {
			$mobile_center = true;
		}
		self::$mobile_center[ $row ] = $mobile_center;
		return $mobile_center;
	}
	/**
	 * Adds support to render header columns.
	 *
	 * @param string $row the name of the row.
	 * @param string $column the name of the column.
	 * @param string $header the name of the header.
	 */
	public function render_header( $row = 'main', $column = 'left', $header = 'desktop' ) {
		$elements = webapp()->option( 'header_' . $header . '_items' );
		if ( isset( $elements ) && isset( $elements[ $row ] ) && isset( $elements[ $row ][ $row . '_' . $column ] ) && is_array( $elements[ $row ][ $row . '_' . $column ] ) && ! empty( $elements[ $row ][ $row . '_' . $column ] ) ) {
			foreach ( $elements[ $row ][ $row . '_' . $column ] as $key => $item ) {
				$template = apply_filters( 'base_header_elements_template_path', 'template-parts/header/' . $item, $item, $row, $column );
				get_template_part( $template );
			}
		}
	}
}
