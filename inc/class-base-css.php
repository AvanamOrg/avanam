<?php
/**
 * Creates minified css via PHP.
 *
 * @author  Base
 * @package Base
 * @version  1.1
 */

namespace Base;

use function Base\webapp;
/**
 * Class to create a minified css output.
 */
class Base_CSS {

	/**
	 * The css selector that you're currently adding rules to
	 *
	 * @access protected
	 * @var string
	 */
	protected $_selector = '';

	/**
	 * Associative array of Google Fonts to load.
	 *
	 * Do not access this property directly, instead use the `get_google_fonts()` method.
	 *
	 * @var array
	 */
	protected static $google_fonts = array();

	/**
	 * Stores the final css output with all of its rules for the current selector.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_selector_output = '';

	/**
	 * Can store a list of additional selector states which can be added and removed.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_selector_states = array();

	/**
	 * Stores a list of css properties that require more formating
	 *
	 * @access private
	 * @var array
	 */
	private $_special_properties_list = array(
		'border-top-left-radius',
		'border-top-right-radius',
		'border-bottom-left-radius',
		'border-bottom-right-radius',
		'transition',
		'transition-delay',
		'transition-duration',
		'transition-property',
		'transition-timing-function',
		'background-image',
		'content',
	);

	/**
	 * Stores all of the rules that will be added to the selector
	 *
	 * @access protected
	 * @var string
	 */
	protected $_css = '';

	/**
	 * The string that holds all of the css to output
	 *
	 * @access protected
	 * @var string
	 */
	protected $_output = '';

	/**
	 * Stores media queries
	 *
	 * @var null
	 */
	protected $_media_query = null;

	/**
	 * The string that holds all of the css to output inside of the media query
	 *
	 * @access protected
	 * @var string
	 */
	protected $_media_query_output = '';

	/**
	 * Sets a selector to the object and changes the current selector to a new one
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param  string $selector - the css identifier of the html that you wish to target.
	 * @return $this
	 */
	public function set_selector( $selector = '' ) {
		// Render the css in the output string everytime the selector changes.
		if ( '' !== $this->_selector ) {
			$this->add_selector_rules_to_output();
		}
		$this->_selector = $selector;
		return $this;
	}

	/**
	 * Wrapper for the set_selector method, changes the selector to add new rules
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @see    set_selector()
	 * @param  string $selector the css selector.
	 * @return $this
	 */
	public function change_selector( $selector = '' ) {
		return $this->set_selector( $selector );
	}

	/**
	 * Adds a pseudo class to the selector ex. :hover, :active, :focus
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param  $state - the selector state
	 * @param  reset - if true the        $_selector_states variable will be reset
	 * @return $this
	 */
	public function add_selector_state( $state, $reset = true ) {
		if ( $reset ) {
			$this->reset_selector_states();
		}
		$this->_selector_states[] = $state;
		return $this;
	}

	/**
	 * Adds multiple pseudo classes to the selector
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param  array $states - the states you would like to add
	 * @return $this
	 */
	public function add_selector_states( $states = array() ) {
		$this->reset_selector_states();
		foreach ( $states as $state ) {
			$this->add_selector_state( $state, false );
		}
		return $this;
	}

	/**
	 * Removes the selector's pseudo classes
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @return $this
	 */
	public function reset_selector_states() {
		$this->add_selector_rules_to_output();
		if ( ! empty( $this->_selector_states ) ) {
			$this->_selector_states = array();
		}
		return $this;
	}

	/**
	 * Adds a new rule to the css output
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param  string $property - the css property.
	 * @param  string $value - the value to be placed with the property.
	 * @param  string $prefix - not required, but allows for the creation of a browser prefixed property.
	 * @return $this
	 */
	public function add_rule( $property, $value, $prefix = null ) {
		$format = is_null( $prefix ) ? '%1$s:%2$s;' : '%3$s%1$s:%2$s;';
		if ( $value && ! empty( $value ) ) {
			$this->_css .= sprintf( $format, $property, $value, $prefix );
		}
		return $this;
	}

	/**
	 * Adds browser prefixed rules, and other special rules to the css output
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param  string $property - the css property
	 * @param  string $value - the value to be placed with the property
	 * @return $this
	 */
	public function add_special_rules( $property, $value ) {
		// Switch through the property types and add prefixed rules.
		switch ( $property ) {
			case 'border-top-left-radius':
				$this->add_rule( $property, $value );
				$this->add_rule( $property, $value, '-webkit-' );
				$this->add_rule( 'border-radius-topleft', $value, '-moz-' );
				break;
			case 'border-top-right-radius':
				$this->add_rule( $property, $value );
				$this->add_rule( $property, $value, '-webkit-' );
				$this->add_rule( 'border-radius-topright', $value, '-moz-' );
				break;
			case 'border-bottom-left-radius':
				$this->add_rule( $property, $value );
				$this->add_rule( $property, $value, '-webkit-' );
				$this->add_rule( 'border-radius-bottomleft', $value, '-moz-' );
				break;
			case 'border-bottom-right-radius':
				$this->add_rule( $property, $value );
				$this->add_rule( $property, $value, '-webkit-' );
				$this->add_rule( 'border-radius-bottomright', $value, '-moz-' );
				break;
			case 'background-image':
				if ( substr( $value, 0, strlen( 'var(' ) ) === 'var(' ) {
					$this->add_rule( $property, $value );
				} else {
					$this->add_rule( $property, sprintf( "url('%s')", $value ) );
				}
				break;
			case 'content':
				$this->add_rule( $property, sprintf( '"%s"', $value ) );
				break;
			default:
				$this->add_rule( $property, $value );
				$this->add_rule( $property, $value, '-webkit-' );
				$this->add_rule( $property, $value, '-moz-' );
				break;
		}

		return $this;
	}

	/**
	 * Adds a css property with value to the css output
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param  string $property - the css property
	 * @param  string $value - the value to be placed with the property
	 * @return $this
	 */
	public function add_property( $property, $value ) {
		if ( in_array( $property, $this->_special_properties_list ) ) {
			$this->add_special_rules( $property, $value );
		} else {
			$this->add_rule( $property, $value );
		}
		return $this;
	}

	/**
	 * Adds multiple properties with their values to the css output
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param  array $properties - a list of properties and values
	 * @return $this
	 */
	public function add_properties( $properties ) {
		foreach ( (array) $properties as $property => $value ) {
			$this->add_property( $property, $value );
		}
		return $this;
	}

	/**
	 * Sets a media query in the class
	 *
	 * @since  1.1
	 * @param  string $value
	 * @return $this
	 */
	public function start_media_query( $value ) {
		// Add the current rules to the output
		$this->add_selector_rules_to_output();

		// Add any previous media queries to the output
		if ( $this->has_media_query() ) {
			$this->add_media_query_rules_to_output();
		}

		// Set the new media query
		$this->_media_query = $value;
		return $this;
	}

	/**
	 * Stops using a media query.
	 *
	 * @see    start_media_query()
	 *
	 * @since  1.1
	 * @return $this
	 */
	public function stop_media_query() {
		return $this->start_media_query( null );
	}

	/**
	 * Gets the media query if it exists in the class
	 *
	 * @since  1.1
	 * @return string|int|null
	 */
	public function get_media_query() {
		return $this->_media_query;
	}

	/**
	 * Checks if there is a media query present in the class
	 *
	 * @since  1.1
	 * @return boolean
	 */
	public function has_media_query() {
		if ( ! empty( $this->get_media_query() ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Adds the current media query's rules to the class' output variable
	 *
	 * @since  1.1
	 * @return $this
	 */
	private function add_media_query_rules_to_output() {
		if ( ! empty( $this->_media_query_output ) ) {
			$this->_output .= sprintf( '@media all and %1$s{%2$s}', $this->get_media_query(), $this->_media_query_output );

			// Reset the media query output string.
			$this->_media_query_output = '';
		}

		return $this;
	}

	/**
	 * Adds the current selector rules to the output variable
	 *
	 * @access private
	 * @since  1.0
	 *
	 * @return $this
	 */
	private function add_selector_rules_to_output() {
		if ( ! empty( $this->_css ) ) {
			$this->prepare_selector_output();
			$selector_output = sprintf( '%1$s{%2$s}', $this->_selector_output, $this->_css );

			if ( $this->has_media_query() ) {
				$this->_media_query_output .= $selector_output;
				$this->reset_css();
			} else {
				$this->_output .= $selector_output;
			}

			// Reset the css.
			$this->reset_css();
		}

		return $this;
	}

	/**
	 * Prepares the $_selector_output variable for rendering
	 *
	 * @access private
	 * @since  1.0
	 *
	 * @return $this
	 */
	private function prepare_selector_output() {
		if ( ! empty( $this->_selector_states ) ) {
			// Create a new variable to store all of the states.
			$new_selector = '';

			foreach ( (array) $this->_selector_states as $state ) {
				$format = end( $this->_selector_states ) === $state ? '%1$s%2$s' : '%1$s%2$s,';
				$new_selector .= sprintf( $format, $this->_selector, $state );
			}
			$this->_selector_output = $new_selector;
		} else {
			$this->_selector_output = $this->_selector;
		}
		return $this;
	}

	/**
	 * Generates the font family output.
	 *
	 * @param array $font an array of font settings.
	 * @return string
	 */
	public function render_font_family( $font, $area = 'headers' ) {
		if ( empty( $font ) ) {
			return false;
		}
		if ( ! is_array( $font ) ) {
			return false;
		}
		if ( ! isset( $font['family'] ) ) {
			return false;
		}
		if ( empty( $font['family'] ) ) {
			return false;
		}
		if ( 'inherit' === $font['family'] ) {
			$font_string = 'inherit';
		} else {
			$font_string = $font['family'];
		}
		if ( isset( $font['google'] ) && true === $font['google'] ) {
			$this->maybe_add_google_font( $font, $area );
		}
		if ( strpos( $font_string, '"') === false && strpos( $font_string, ',') === false && strpos( $font_string, ' ' ) !== false ) {
			$font_string = "'" . $font_string . "'";
		}
		if ( isset( $font['google'] ) && true === $font['google'] && 'inherit' !== $font_string ) {
			if ( isset( $font['fallback'] ) && ! empty( $font['fallback'] ) ) {
				if ( 'display' === $font['fallback'] || 'handwriting' === $font['fallback'] ) {
					$font_string = $font_string . ', cursive';
				} else {
					$font_string = $font_string . ', ' . $font['fallback'];
				}
			} else {
				$font_string = $font_string . ', var(--global-fallback-font)';
			}
		}

		return apply_filters( 'base_theme_font_family_string', $font_string );
	}
	/**
	 * Generates the font output.
	 *
	 * @param array  $font an array of font settings.
	 * @param object $css an object of css output.
	 * @param string $inherit an string to determine if the font should inherit.
	 * @return string
	 */
	public function render_font_no_color( $font, $css, $inherit = null ) {
		if ( empty( $font ) ) {
			return false;
		}
		if ( ! is_array( $font ) ) {
			return false;
		}
		if ( isset( $font['style'] ) && ! empty( $font['style'] ) ) {
			$css->add_property( 'font-style', $font['style'] );
		}
		if ( isset( $font['weight'] ) && ! empty( $font['weight'] ) ) {
			$css->add_property( 'font-weight', $font['weight'] );
		}
		$size_type = ( isset( $font['sizeType'] ) && ! empty( $font['sizeType'] ) ? $font['sizeType'] : 'px' );
		if ( isset( $font['size'] ) && isset( $font['size']['desktop'] ) && ! empty( $font['size']['desktop'] ) ) {
			$css->add_property( 'font-size', $font['size']['desktop'] . $size_type );
		}
		$line_type = ( isset( $font['lineType'] ) && ! empty( $font['lineType'] ) ? $font['lineType'] : '' );
		$line_type = ( '-' !== $line_type ? $line_type : '' );
		if ( isset( $font['lineHeight'] ) && isset( $font['lineHeight']['desktop'] ) && ! empty( $font['lineHeight']['desktop'] ) ) {
			$css->add_property( 'line-height', $font['lineHeight']['desktop'] . $line_type );
		}
		$letter_type = ( isset( $font['spacingType'] ) && ! empty( $font['spacingType'] ) ? $font['spacingType'] : 'em' );
		if ( isset( $font['letterSpacing'] ) && isset( $font['letterSpacing']['desktop'] ) && is_numeric( $font['letterSpacing']['desktop'] ) ) {
			$css->add_property( 'letter-spacing', $font['letterSpacing']['desktop'] . $letter_type );
		}
		$family = ( isset( $font['family'] ) && ! empty( $font['family'] ) && 'inherit' !== $font['family'] ? $font['family'] : '' );
		if ( ! empty( $family ) ) {
			if ( ! empty( $inherit ) && 'body' === $inherit ) {
				$family = 'var(--global-body-font-family)';
			} elseif ( ! empty( $inherit ) && 'primary_nav' === $inherit ) {
				$family = 'var(--global-primary-nav-font-family)';
			} elseif ( strpos( $family, '"') === false && strpos( $family, ',') === false && strpos( $family, ' ' ) !== false ) {
				$family = "'" . $family . "'";
			}
			if ( isset( $font['google'] ) && true === $font['google'] && 'var(' !== substr( $family, 0, 4 ) ) {
				if ( isset( $font['fallback'] ) && ! empty( $font['fallback'] ) ) {
					if ( 'display' === $font['fallback'] || 'handwriting' === $font['fallback'] ) {
						$family = $family . ', cursive';
					} else {
						$family = $family . ', ' . $font['fallback'];
					}
				} else {
					$family = $family . ', var(--global-fallback-font)';
				}
			}
			$css->add_property( 'font-family', apply_filters( 'base_theme_font_family_string', $family ) );
			if ( isset( $font['google'] ) && true === $font['google'] ) {
				if ( ! empty( $inherit ) && 'body' === $inherit ) {
					$this->maybe_add_google_font( $font, $inherit );
				} else {
					$this->maybe_add_google_font( $font );
				}
			}
		}
		if ( isset( $font['transform'] ) && ! empty( $font['transform'] ) ) {
			$css->add_property( 'text-transform', $font['transform'] );
		}
	}
	/**
	 * Generates the font output.
	 *
	 * @param array  $font an array of font settings.
	 * @param object $css an object of css output.
	 * @param string $inherit an string to determine if the font should inherit.
	 * @return string
	 */
	public function render_font( $font, $css, $inherit = null ) {
		if ( empty( $font ) ) {
			return false;
		}
		if ( ! is_array( $font ) ) {
			return false;
		}
		if ( isset( $font['style'] ) && ! empty( $font['style'] ) ) {
			$css->add_property( 'font-style', $font['style'] );
		}
		if ( isset( $font['weight'] ) && ! empty( $font['weight'] ) ) {
			$css->add_property( 'font-weight', $font['weight'] );
		}
		$size_type = ( isset( $font['sizeType'] ) && ! empty( $font['sizeType'] ) ? $font['sizeType'] : 'px' );
		if ( isset( $font['size'] ) && isset( $font['size']['desktop'] ) && ! empty( $font['size']['desktop'] ) ) {
			$css->add_property( 'font-size', $font['size']['desktop'] . $size_type );
		}
		$line_type = ( isset( $font['lineType'] ) && ! empty( $font['lineType'] ) ? $font['lineType'] : '' );
		$line_type = ( '-' !== $line_type ? $line_type : '' );
		if ( isset( $font['lineHeight'] ) && isset( $font['lineHeight']['desktop'] ) && ! empty( $font['lineHeight']['desktop'] ) ) {
			$css->add_property( 'line-height', $font['lineHeight']['desktop'] . $line_type );
		}
		$letter_type = ( isset( $font['spacingType'] ) && ! empty( $font['spacingType'] ) ? $font['spacingType'] : 'em' );
		if ( isset( $font['letterSpacing'] ) && isset( $font['letterSpacing']['desktop'] ) && is_numeric( $font['letterSpacing']['desktop'] ) ) {
			$css->add_property( 'letter-spacing', $font['letterSpacing']['desktop'] . $letter_type );
		}
		$special_inherit = ( isset( $font['family'] ) && ! empty( $font['family'] ) && 'inherit' === $font['family'] ? true : false );
		if ( $special_inherit ) {
			if ( ! empty( $inherit ) && 'heading' === $inherit ) {
				$this->maybe_add_google_variant( $font, $inherit );
			} else {
				$this->maybe_add_google_variant( $font );
			}
		}
		$family = ( isset( $font['family'] ) && ! empty( $font['family'] ) && 'inherit' !== $font['family'] ? $font['family'] : '' );
		if ( ! empty( $family ) ) {
			if ( ! empty( $inherit ) && 'body' === $inherit ) {
				$family = 'var(--global-body-font-family)';
			} elseif ( ! empty( $inherit ) && 'primary_nav' === $inherit ) {
				$family = 'var(--global-primary-nav-font-family)';
			} elseif ( strpos( $family, '"') === false && strpos( $family, ',') === false && strpos( $family, ' ' ) !== false ) {
				$family = "'" . $family . "'";
			}
			if ( isset( $font['google'] ) && true === $font['google'] && 'var(' !== substr( $family, 0, 4 ) ) {
				if ( isset( $font['fallback'] ) && ! empty( $font['fallback'] ) ) {
					if ( 'handwriting' === $font['fallback'] ) {
						$family = $family . ', cursive';
					} elseif ( 'display' === $font['fallback'] ) {
						$family = $family . ', var(--global-display-fallback-font)';
					} else {
						$family = $family . ', ' . $font['fallback'];
					}
				} else {
					$family = $family . ', var(--global-fallback-font)';
				}
			}
			$css->add_property( 'font-family', apply_filters( 'base_theme_font_family_string', $family ) );
			if ( isset( $font['google'] ) && true === $font['google'] ) {
				if ( ! empty( $inherit ) && 'body' === $inherit ) {
					$this->maybe_add_google_font( $font, $inherit );
				} else {
					$this->maybe_add_google_font( $font );
				}
			}
		}
		if ( isset( $font['transform'] ) && ! empty( $font['transform'] ) ) {
			$css->add_property( 'text-transform', $font['transform'] );
		}
		if ( isset( $font['color'] ) && ! empty( $font['color'] ) ) {
			$css->add_property( 'color', $this->render_color( $font['color'] ) );
		}
	}
	/**
	 * Generates the font height output.
	 *
	 * @param array  $font an array of font settings.
	 * @param string $device the device this is showing on.
	 * @return string
	 */
	public function render_font_height( $font, $device ) {
		if ( empty( $font ) ) {
			return false;
		}
		if ( ! is_array( $font ) ) {
			return false;
		}
		if ( ! isset( $font['lineHeight'] ) ) {
			return false;
		}
		if ( ! is_array( $font['lineHeight'] ) ) {
			return false;
		}
		if ( ! isset( $font['lineHeight'][ $device ] ) ) {
			return false;
		}
		if ( empty( $font['lineHeight'][ $device ] ) ) {
			return false;
		}
		$font_string = $font['lineHeight'][ $device ] . ( isset( $font['lineType'] ) && ! empty( $font['lineType'] ) ? $font['lineType'] : 'px' );

		return $font_string;
	}
	/**
	 * Generates the font spacing output.
	 *
	 * @param array  $font an array of font settings.
	 * @param string $device the device this is showing on.
	 * @return string
	 */
	public function render_font_spacing( $font, $device ) {
		if ( empty( $font ) ) {
			return false;
		}
		if ( ! is_array( $font ) ) {
			return false;
		}
		if ( ! isset( $font['letterSpacing'] ) ) {
			return false;
		}
		if ( ! is_array( $font['letterSpacing'] ) ) {
			return false;
		}
		if ( ! isset( $font['letterSpacing'][ $device ] ) ) {
			return false;
		}
		if ( empty( $font['letterSpacing'][ $device ] ) ) {
			return false;
		}
		$font_string = $font['letterSpacing'][ $device ] . ( isset( $font['spacingType'] ) && ! empty( $font['spacingType'] ) ? $font['spacingType'] : 'em' );

		return $font_string;
	}
	/**
	 * Generates the color output.
	 *
	 * @param string $color any color attribute.
	 * @return string
	 */
	public function render_color( $color ) {
		if ( empty( $color ) ) {
			return false;
		}
		if ( ! is_array( $color ) && strpos( $color, 'palette' ) !== false ) {
			$color = 'var(--global-' . $color . ')';
		}
		return $color;
	}
	/**
	 * Hex to RGB
	 *
	 * @param string $hex string hex code.
	 */
	public function hex2rgb( $hex ) {
		if ( empty( $hex ) ) {
			return '';
		}
		if ( 'transparent' === $hex ) {
			return '255, 255, 255';
		}
		$hex = str_replace( '#', '', $hex );
		if ( 6 === strlen( $hex ) || 3 === strlen( $hex ) ) { 
			if ( 3 === strlen( $hex ) ) {
				$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
				$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
				$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
			} else {
				$r = hexdec( substr( $hex, 0, 2 ) );
				$g = hexdec( substr( $hex, 2, 2 ) );
				$b = hexdec( substr( $hex, 4, 2 ) );
			}
		} else {
			return '255, 255, 255';
		}
		$rgb = $r . ', ' . $g . ', ' . $b;
		return $rgb;
	}
	/**
	 * Generates the size output.
	 *
	 * @param array  $size an array of size settings.
	 * @param string $device the device this is showing on.
	 * @param bool   $render_zero if 0 should be rendered or not.
	 * @return string
	 */
	public function render_range( $size, $device, $render_zero = true ) {
		if ( empty( $size ) ) {
			return false;
		}
		if ( ! is_array( $size ) ) {
			return false;
		}
		if ( ! isset( $size['size'] ) ) {
			return false;
		}
		if ( ! is_array( $size['size'] ) ) {
			return false;
		}
		if ( ! isset( $size['size'][ $device ] ) ) {
			return false;
		}
		if ( $render_zero ) {
			if ( ! is_numeric( $size['size'][ $device ] ) ) {
				return false;
			}
		} else {
			if ( empty( $size['size'][ $device ] ) ) {
				return false;
			}
		}
		$size_type   = ( isset( $size['unit'] ) && is_array( $size['unit'] ) && isset( $size['unit'][ $device ] ) && ! empty( $size['unit'][ $device ] ) ? $size['unit'][ $device ] : 'px' );
		$size_string = $size['size'][ $device ] . $size_type;

		return $size_string;
	}
	/**
	 * Generates the size output.
	 *
	 * @param array  $shadow an array of shadow settings.
	 * @param string $default the default shadow settings.
	 * @return string
	 */
	public function render_shadow( $shadow, $default = array() ) {
		if ( empty( $shadow ) ) {
			return false;
		}
		if ( ! is_array( $shadow ) ) {
			return false;
		}
		if ( ! isset( $shadow['color'] ) ) {
			return false;
		}
		if ( ! isset( $shadow['hOffset'] ) ) {
			return false;
		}
		if ( ! isset( $shadow['vOffset'] ) ) {
			return false;
		}
		if ( ! isset( $shadow['blur'] ) ) {
			return false;
		}
		if ( ! isset( $shadow['spread'] ) ) {
			return false;
		}
		if ( ! isset( $shadow['inset'] ) ) {
			return false;
		}
		if ( $shadow['inset'] ) {
			$shadow_string = 'inset ' . ( ! empty( $shadow['hOffset'] ) ? $shadow['hOffset'] : '0' ) . 'px ' . ( ! empty( $shadow['vOffset'] ) ? $shadow['vOffset'] : '0' ) . 'px ' . ( ! empty( $shadow['blur'] ) ? $shadow['blur'] : '0' ) . 'px ' . ( ! empty( $shadow['spread'] ) ? $shadow['spread'] : '0' ) . 'px ' . ( ! empty( $shadow['color'] ) ? $this->render_color( $shadow['color'] ) : 'rgba(0,0,0,0.0)' );
		} else {
			$shadow_string =  ( ! empty( $shadow['hOffset'] ) ? $shadow['hOffset'] : '0' ) . 'px ' . ( ! empty( $shadow['vOffset'] ) ? $shadow['vOffset'] : '0' ) . 'px ' . ( ! empty( $shadow['blur'] ) ? $shadow['blur'] : '0' ) . 'px ' . ( ! empty( $shadow['spread'] ) ? $shadow['spread'] : '0' ) . 'px ' . ( ! empty( $shadow['color'] ) ? $this->render_color( $shadow['color'] ) : 'rgba(0,0,0,0.0)' );
		}

		return $shadow_string;
	}
	/**
	 * Generates the measure output.
	 *
	 * @param array $measure an array of font settings.
	 * @return string
	 */
	public function render_measure( $measure ) {
		if ( empty( $measure ) ) {
			return false;
		}
		if ( ! is_array( $measure ) ) {
			return false;
		}
		if ( ! isset( $measure['size'] ) ) {
			return false;
		}
		if ( ! is_array( $measure['size'] ) ) {
			return false;
		}
		if ( ! isset( $measure['size'][0] ) ) {
			return false;
		}
		if ( ! is_numeric( $measure['size'][0] ) && ! is_numeric( $measure['size'][1] ) && ! is_numeric( $measure['size'][2] ) && ! is_numeric( $measure['size'][3] ) ) {
			return false;
		}
		$size_unit   = ( isset( $measure['unit'] ) && ! empty( $measure['unit'] ) ? $measure['unit'] : 'px' );
		$size_string = ( is_numeric( $measure['size'][0] ) ? $measure['size'][0] : '0' ) . $size_unit . ' ' . ( is_numeric( $measure['size'][1] ) ? $measure['size'][1] : '0' ) . $size_unit . ' ' . ( is_numeric( $measure['size'][2] ) ? $measure['size'][2] : '0' ) . $size_unit . ' ' . ( is_numeric( $measure['size'][3] ) ? $measure['size'][3] : '0' ) . $size_unit;
		return $size_string;
	}
	/**
	 * Generates the font size output.
	 *
	 * @param array  $font an array of font settings.
	 * @param string $device the device this is showing on.
	 * @return string
	 */
	public function render_font_size( $font, $device ) {
		if ( empty( $font ) ) {
			return false;
		}
		if ( ! is_array( $font ) ) {
			return false;
		}
		if ( ! isset( $font['size'] ) ) {
			return false;
		}
		if ( ! is_array( $font['size'] ) ) {
			return false;
		}
		if ( ! isset( $font['size'][ $device ] ) ) {
			return false;
		}
		if ( empty( $font['size'][ $device ] ) ) {
			return false;
		}
		$font_string = $font['size'][ $device ] . ( isset( $font['sizeType'] ) && ! empty( $font['sizeType'] ) ? $font['sizeType'] : 'px' );

		return $font_string;
	}
	/**
	 * Generates the background output.
	 *
	 * @param array  $background an array of background settings.
	 * @param object $css an object of css output.
	 */
	public function render_background( $background, $css ) {
		if ( empty( $background ) ) {
			return false;
		}
		if ( ! is_array( $background ) ) {
			return false;
		}
		$background_string = '';
		$type              = ( isset( $background['type'] ) && ! empty( $background['type'] ) ? $background['type'] : 'color' );
		$color_type        = '';
		if ( isset( $background['color'] ) && ! empty( $background['color'] ) ) {
			if ( strpos( $background['color'], 'palette' ) !== false ) {
				$color_type = 'var(--global-' . $background['color'] . ')';
			} else {
				$color_type = $background['color'];
			}
		}
		if ( 'image' === $type && isset( $background['image'] ) ) {
			$image_url = ( isset( $background['image']['url'] ) && ! empty( $background['image']['url'] ) ? $background['image']['url'] : '' );
			if ( ! empty( $image_url ) ) {
				$repeat            = ( isset( $background['image']['repeat'] ) && ! empty( $background['image']['repeat'] ) ? $background['image']['repeat'] : '' );
				$size              = ( isset( $background['image']['size'] ) && ! empty( $background['image']['size'] ) ? $background['image']['size'] : '' );
				$position          = ( isset( $background['image']['position'] ) && is_array( $background['image']['position'] ) && isset( $background['image']['position']['x'] ) && is_numeric( $background['image']['position']['x'] ) && isset( $background['image']['position']['y'] ) && is_numeric( $background['image']['position']['y'] ) ? ( $background['image']['position']['x'] * 100 ) . '% ' . ( $background['image']['position']['y'] * 100 ) . '%' : 'center' );
				$attachement       = ( isset( $background['image']['attachment'] ) && ! empty( $background['image']['attachment'] ) ? $background['image']['attachment'] : '' );
				$background_string = ( ! empty( $color_type ) ? $color_type . ' ' : '' ) . $image_url . ( ! empty( $repeat ) ? ' ' . $repeat : '' ) . ( ! empty( $position ) ? ' ' . $position : '' ) . ( ! empty( $size ) ? ' ' . $size : '' ) . ( ! empty( $attachement ) ? ' ' . $attachement : '' );
				$css->add_property( 'background-color', $color_type );
				$css->add_property( 'background-image', $image_url );
				$css->add_property( 'background-repeat', $repeat );
				$css->add_property( 'background-position', $position );
				$css->add_property( 'background-size', $size );
				$css->add_property( 'background-attachment', $attachement );
			} else {
				if ( ! empty( $color_type ) ) {
					$background_string = $color_type;
					$css->add_property( 'background-color', $color_type );
				}
			}
		} elseif ( 'gradient' === $type && isset( $background['gradient'] ) && ! empty( $background['gradient'] ) ) {
			$css->add_property( 'background', $background['gradient'] );
		} else {
			if ( ! empty( $color_type ) ) {
				$background_string = $color_type;
				$css->add_property( 'background', $color_type );
			}
		}
	}
	/**
	 * Generates the border output.
	 *
	 * @param array $border an array of border settings.
	 * @return string
	 */
	public function render_border( $border ) {
		if ( empty( $border ) ) {
			return false;
		}
		if ( ! is_array( $border ) ) {
			return false;
		}
		$border_string = '';
		$style         = ( isset( $border['style'] ) && ! empty( $border['style'] ) ? $border['style'] : '' );

		if ( '' === $style ) {
			return false;
		}
		$width         = ( isset( $border['width'] ) && ! empty( $border['width'] ) ? $border['width'] : '0' );
		$unit          = ( isset( $border['unit'] ) && ! empty( $border['unit'] ) ? $border['unit'] : 'px' );
		$color         = ( isset( $border['color'] ) && ! empty( $border['color'] ) ? $border['color'] : 'transparent' );
		$border_string = $width . $unit . ' ' . $style . ' ' . $this->render_color( $color );

		return $border_string;
	}
	/**
	 * Generates the border output.
	 *
	 * @param array  $border_array an array of border settings.
	 * @param string $device a string with the device.
	 * @return string
	 */
	public function render_header_responsive_border( $border_array, $device ) {
		if ( empty( $border_array ) ) {
			return false;
		}
		if ( ! is_array( $border_array ) ) {
			return false;
		}
		if ( ! isset( $border_array[ $device ] ) ) {
			return false;
		}
		if ( ! is_array( $border_array[ $device ] ) ) {
			return false;
		}
		if ( ! isset( $border_array[ $device ]['width'] ) ) {
			return false;
		}
		if ( isset( $border_array[ $device ]['width'] ) && ! is_numeric( $border_array[ $device ]['width'] ) ) {
			return false;
		}
		$border_string = '';
		$style         = ( isset( $border_array[ $device ]['style'] ) && ! empty( $border_array[ $device ]['style'] ) ? $border_array[ $device ]['style'] : '' );

		if ( '' === $style && 'desktop' === $device ) {
			return false;
		} elseif ( '' === $style && 'tablet' === $device ) {
			$style = ( isset( $border_array['desktop']['style'] ) && ! empty( $border_array['desktop']['style'] ) ? $border_array['desktop']['style'] : '' );
			if ( '' === $style ) {
				return false;
			}
		} elseif ( '' === $style && 'mobile' === $device ) {
			$style = ( isset( $border_array['tablet']['style'] ) && ! empty( $border_array['tablet']['style'] ) ? $border_array['tablet']['style'] : '' );
			if ( '' === $style ) {
				$style = ( isset( $border_array['desktop']['style'] ) && ! empty( $border_array['desktop']['style'] ) ? $border_array['desktop']['style'] : '' );
				if ( '' === $style ) {
					return false;
				}
			}
		}
		$fallback_unit = 'px';
		if ( 'tablet' === $device ) {
			$fallback_unit = ( isset( $border_array['desktop']['unit'] ) && ! empty( $border_array['desktop']['unit'] ) ? $border_array['desktop']['unit'] : $fallback_unit );
		} elseif ( 'mobile' === $device ) {
			if ( isset( $border_array['tablet']['unit'] ) && ! empty( $border_array['tablet']['unit'] ) ) {
				$fallback_unit = $border_array['tablet']['unit'];
			} else {
				$fallback_unit = ( isset( $border_array['desktop']['unit'] ) && ! empty( $border_array['desktop']['unit'] ) ? $border_array['desktop']['unit'] : $fallback_unit );
			}
		}
		$fallback_color = 'transparent';
		if ( 'tablet' === $device ) {
			$fallback_color = ( isset( $border_array['desktop']['color'] ) && ! empty( $border_array['desktop']['color'] ) ? $border_array['desktop']['color'] : $fallback_color );
		} elseif ( 'mobile' === $device ) {
			if ( isset( $border_array['tablet']['color'] ) && ! empty( $border_array['tablet']['color'] ) ) {
				$fallback_color = $border_array['tablet']['color'];
			} else {
				$fallback_color = ( isset( $border_array['desktop']['color'] ) && ! empty( $border_array['desktop']['color'] ) ? $border_array['desktop']['color'] : $fallback_color );
			}
		}
		$width         = ( isset( $border_array[ $device ]['width'] ) && ! empty( $border_array[ $device ]['width'] ) ? $border_array[ $device ]['width'] : '0' );
		$unit          = ( isset( $border_array[ $device ]['unit'] ) && ! empty( $border_array[ $device ]['unit'] ) ? $border_array[ $device ]['unit'] : $fallback_unit );
		$color         = ( isset( $border_array[ $device ]['color'] ) && ! empty( $border_array[ $device ]['color'] ) ? $border_array[ $device ]['color'] : $fallback_color );
		$border_string = $width . $unit . ' ' . $style . ' ' . $this->render_color( $color );

		return $border_string;
	}
	/**
	 * Generates the size output.
	 *
	 * @param array $size an array of size settings.
	 * @return string
	 */
	public function render_half_size( $size ) {
		if ( empty( $size ) ) {
			return false;
		}
		if ( ! is_array( $size ) ) {
			return false;
		}
		$size_number = ( isset( $size['size'] ) && ! empty( $size['size'] ) ? $size['size'] : '0' );
		$size_unit   = ( isset( $size['unit'] ) && ! empty( $size['unit'] ) ? $size['unit'] : 'em' );

		$size_string = 'calc(' . $size_number . $size_unit . ' / 2)';
		return $size_string;
	}
	/**
	 * Generates the size output.
	 *
	 * @param array $size an array of size settings.
	 * @return string
	 */
	public function render_negative_size( $size ) {
		if ( empty( $size ) ) {
			return false;
		}
		if ( ! is_array( $size ) ) {
			return false;
		}
		$size_number = ( isset( $size['size'] ) && ! empty( $size['size'] ) ? $size['size'] : '0' );
		$size_unit   = ( isset( $size['unit'] ) && ! empty( $size['unit'] ) ? $size['unit'] : 'em' );

		$size_string = '-' . $size_number . $size_unit;
		return $size_string;
	}
	/**
	 * Generates the size output.
	 *
	 * @param array $size an array of size settings.
	 * @return string
	 */
	public function render_negative_half_size( $size ) {
		if ( empty( $size ) ) {
			return false;
		}
		if ( ! is_array( $size ) ) {
			return false;
		}
		$size_number = ( isset( $size['size'] ) && ! empty( $size['size'] ) ? $size['size'] : '0' );
		$size_unit   = ( isset( $size['unit'] ) && ! empty( $size['unit'] ) ? $size['unit'] : 'em' );

		$size_string = 'calc(-' . $size_number . $size_unit . ' / 2)';
		return $size_string;
	}
	/**
	 * Generates the size output.
	 *
	 * @param array $size an array of size settings.
	 * @return string
	 */
	public function render_size( $size ) {
		if ( empty( $size ) ) {
			return false;
		}
		if ( ! is_array( $size ) ) {
			return false;
		}
		$size_number = ( isset( $size['size'] ) && ! empty( $size['size'] ) ? $size['size'] : '0' );
		$size_unit   = ( isset( $size['unit'] ) && ! empty( $size['unit'] ) ? $size['unit'] : 'em' );

		$size_string = $size_number . $size_unit;
		return $size_string;
	}
	/**
	 * Generates the border output.
	 *
	 * @param array $border an array of border settings.
	 * @return string
	 */
	public function render_responsive_border( $border, $device ) {
		if ( empty( $border ) ) {
			return false;
		}
		if ( ! is_array( $border ) ) {
			return false;
		}
		if ( ! isset( $border[ $device ] ) ) {
			return false;
		}
		if ( ! is_array( $border[ $device ] ) ) {
			return false;
		}
		if ( ! is_array( $border[ $device ] ) ) {
			return false;
		}
		$border_string = '';
		$new_style     = '';
		$style         = ( isset( $border[ $device ]['style'] ) && ! empty( $border[ $device ]['style'] ) ? $border[ $device ]['style'] : '' );

		if ( '' === $style ) {
			$continue = false;
			if ( 'desktop' === $device ) {
				$continue = false;
			} elseif ( 'tablet' === $device ) {
				if ( isset( $border['desktop'] ) && isset( $border['desktop']['style'] ) && ! empty( $border['desktop']['style'] ) && 'none' !== $border['desktop']['style'] ) {
					$new_style = $border['desktop']['style'];
					$continue  = true;
				} else {
					$continue = false;
				}
			} elseif ( 'mobile' === $device ) {
				if ( isset( $border['tablet'] ) && isset( $border['tablet']['style'] ) && ! empty( $border['tablet']['style'] ) && 'none' !== $border['tablet']['style'] ) {
					$new_style = $border['tablet']['style'];
					$continue  = true;
				} else if ( isset( $border['desktop'] ) && isset( $border['desktop']['style'] ) && ! empty( $border['desktop']['style'] ) && 'none' !== $border['desktop']['style'] ) {
					$new_style = $border['desktop']['style'];
					$continue  = true;
				} else {
					$continue = false;
				}
			}
			if ( ! $continue ) {
				return false;
			}
		}
		$width         = ( isset( $border[ $device ]['width'] ) && ! empty( $border[ $device ]['width'] ) ? $border[ $device ]['width'] : '0' );
		$unit          = ( isset( $border[ $device ]['unit'] ) && ! empty( $border[ $device ]['unit'] ) ? $border[ $device ]['unit'] : 'px' );
		$color         = ( isset( $border[ $device ]['color'] ) && ! empty( $border[ $device ]['color'] ) ? $border[ $device ]['color'] : 'transparent' );
		if ( '' === $style ) {
			$border_string = $width . $unit . ' ' . $new_style . ' ' . $this->render_color( $color );
		} else {
			$border_string = $width . $unit . ' ' . $style . ' ' . $this->render_color( $color );
		}

		return $border_string;
	}
	/**
	 * Add google font to array.
	 *
	 * @param array  $font the font settings.
	 * @param string $area the font use case.
	 */
	public function maybe_add_google_variant( $font, $area = null ) {
		if ( empty( $font['variant'] ) ) {
			return;
		}
		$maybe_add = false;
		if ( ! empty( $area ) && 'headers' === $area ) {
			$parent_font = webapp()->option( 'heading_font' );
			if ( isset( $parent_font['family'] ) && 'inherit' === $parent_font['family'] ) {
				$parent_font = webapp()->sub_option( 'base_font' );
				if ( isset( $parent_font['google'] ) && true === $parent_font['google'] ) {
					$maybe_add = true;
				}
			} elseif ( isset( $parent_font['google'] ) && true === $parent_font['google'] ) {
				$maybe_add = true;
			}
		} else {
			$parent_font = webapp()->sub_option( 'base_font' );
			if ( isset( $parent_font['google'] ) && true === $parent_font['google'] ) {
				$maybe_add = true;
			}
		}
		if ( $maybe_add ) {
			if ( ! in_array( $font['variant'], self::$google_fonts[ $parent_font['family'] ]['fontvariants'], true ) ) {
				array_push( self::$google_fonts[ $parent_font['family'] ]['fontvariants'], $font['variant'] );
			}
		}
	}
	/**
	 * Add google font to array.
	 *
	 * @param array  $font the font settings.
	 * @param string $full the font use case.
	 */
	public function maybe_add_google_font( $font, $full = null ) {
		if ( ! empty( $full ) && 'headers' === $full ) {
			$new_variant = array();
			if ( isset( $font['variant'] ) && ! empty( $font['variant'] ) && is_array( $font['variant'] ) ) {
				foreach ( array( 'h1_font', 'h2_font', 'h3_font', 'h4_font', 'h5_font', 'h6_font' ) as $option ) {
					$variant = webapp()->sub_option( $option, 'variant' );
					if ( in_array( $variant, $font['variant'], true ) && ! in_array( $variant, $new_variant, true ) ) {
						array_push( $new_variant, $variant );
					}
				}
			}
			if ( empty( $new_variant ) ) {
				$new_variant = $font['variant'];
			}
		}
		if ( ! empty( $full ) && 'body' === $full && 'inherit' === webapp()->sub_option( 'heading_font', 'family' ) ) {
			$new_variant = array( $font['variant'] );
			if ( isset( $font['variant'] ) && ! empty( $font['variant'] ) && ! is_array( $font['variant'] ) ) {
				$current_variant = array( $font['variant'] );
				foreach ( array( 'h1_font', 'h2_font', 'h3_font', 'h4_font', 'h5_font', 'h6_font' ) as $option ) {
					$variant = webapp()->sub_option( $option, 'variant' );
					if ( ! in_array( $variant, $current_variant, true ) && ! in_array( $variant, $new_variant, true ) ) {
						array_push( $new_variant, $variant );
					}
				}
			}
			if ( empty( $new_variant ) ) {
				$new_variant = array( $font['variant'] );
			}
		} elseif ( ! empty( $full ) && 'body' === $full && 'inherit' !== webapp()->sub_option( 'heading_font', 'family' ) ) {
			$new_variant = array( $font['variant'], '700' );
		}
		if ( ! empty( $full ) && 'body' === $full ) {
			if ( webapp()->option( 'load_base_italic' ) ) {
				$update_variant = array();
				foreach ( $new_variant as $variant ) {
					if ( $variant === 'italic' ) {
						$update_variant[] = $variant;
						$update_variant[] = "regular";
					} else {
						$variant = rtrim( $variant, 'italic' );
						$update_variant[] = $variant;
						$update_variant[] = "{$variant}italic";
					}
				}
				$new_variant = $update_variant;
			}
		}
		// Check if the font has been added yet.
		if ( ! array_key_exists( $font['family'], self::$google_fonts ) ) {
			if ( ! empty( $full ) && 'headers' === $full ) {
				$add_font = array(
					'fontfamily'   => $font['family'],
					'fontvariants' => ( isset( $new_variant ) && ! empty( $new_variant ) && is_array( $new_variant ) ? $new_variant : array() ),
					'fontsubsets'  => ( isset( $font['subset'] ) && ! empty( $font['subset'] ) ? array( $font['subset'] ) : array() ),
				);
			} else if ( ! empty( $full ) && 'body' === $full && 'inherit' === webapp()->sub_option( 'heading_font', 'family' ) ) {
				$add_font = array(
					'fontfamily'   => $font['family'],
					'fontvariants' => ( isset( $new_variant ) && ! empty( $new_variant ) && is_array( $new_variant ) ? $new_variant : array() ),
					'fontsubsets'  => ( isset( $font['subset'] ) && ! empty( $font['subset'] ) ? array( $font['subset'] ) : array() ),
				);
			} else if ( ! empty( $full ) && 'body' === $full && 'inherit' !== webapp()->sub_option( 'heading_font', 'family' ) ) {
				$add_font = array(
					'fontfamily'   => $font['family'],
					'fontvariants' => ( isset( $new_variant ) && ! empty( $new_variant ) && is_array( $new_variant ) ? $new_variant : array() ),
					'fontsubsets'  => ( isset( $font['subset'] ) && ! empty( $font['subset'] ) ? array( $font['subset'] ) : array() ),
				);
			} else {
				$add_font = array(
					'fontfamily'   => $font['family'],
					'fontvariants' => ( isset( $font['variant'] ) && ! empty( $font['variant'] ) ? array( $font['variant'] ) : array() ),
					'fontsubsets'  => ( isset( $font['subset'] ) && ! empty( $font['subset'] ) ? array( $font['subset'] ) : array() ),
				);
			}
			self::$google_fonts[ $font['family'] ] = $add_font;
		} else {
			if ( ! empty( $full ) ) {
				foreach ( $new_variant as $variant ) {
					if ( ! in_array( $variant, self::$google_fonts[ $font['family'] ]['fontvariants'], true ) ) {
						array_push( self::$google_fonts[ $font['family'] ]['fontvariants'], $variant );
					}
				}
			} else {
				if ( ! in_array( $font['variant'], self::$google_fonts[ $font['family'] ]['fontvariants'], true ) ) {
					array_push( self::$google_fonts[ $font['family'] ]['fontvariants'], $font['variant'] );
				}
			}
		}
	}

	/**
	 * Resets the css variable
	 *
	 * @access private
	 * @since  1.1
	 *
	 * @return void
	 */
	private function reset_css() {
		$this->_css = '';
		return;
	}

	/**
	 * Returns the google fonts array from the compiled css.
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @return string
	 */
	public function fonts_output() {
		return self::$google_fonts;
	}

	/**
	 * Returns the minified css in the $_output variable
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @return string
	 */
	public function css_output() {
		// Add current selector's rules to output
		$this->add_selector_rules_to_output();

		// Output minified css
		return $this->_output;
	}

}
