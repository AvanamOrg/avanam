<?php
/**
 * Class for the Customizer
 *
 * @package Base
 */

namespace Base;

use WP_Customize_Control;
use function sanitize_text_field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for Customizer Sanitize
 *
 * @category class
 */
class Customizer_Sanitize {

	/**
	 * Sanitize string, remove spaces and special characters.
	 *
	 * @param string $value the string to be sanitized.
	 *
	 * @return string
	 */
	public static function base_sanitize_key_string( $value ) {
		// To allow for strings with capitals, dashes, underscores, remove other special characters and spaces.
		return preg_replace( '/[^A-Za-z0-9_\-]/', '', $value );
	}
	/**
	 * Sanitize customizer setting. If a string use sanitize_key, for an array, loop through each item.
	 * For the key use sanitize_key and the value strip out most special characters.
	 * 
	 * @param mixed $start_value the value before sanitized.
	 *
	 * @return mixed
	 */
	public static function base_sanitize_option( $start_value ) {
		if ( empty( $start_value ) ) {
			return '';
		}
		if ( ! is_array( $start_value ) ) {
			return self::base_sanitize_key_string( $start_value );
		}
		$new_value = array();
		foreach ( $start_value as $key => $value ) {
			// Sanitize key, remove spaces and special characters.
			$key = self::base_sanitize_key_string( $key );
			if ( is_array( $value ) ) {
				$new_value[ $key ] = self::base_sanitize_option( $value );
			} else {
				switch ( $key ) {
					case 'family':
					case 'label':
						// Font family names can have special characters and can be custom so treat like an text field, label is a text field.
						$new_value[ $key ] = sanitize_text_field( $value );
						break;
					case 'color':
					case 'hover':
					case 'active':
					case 'link':
					case 'background':
					case 'backgroundHover':
					case 'border':
					case 'borderHover':
						// To allow hex, rgba(), and var() values or custom values like transparent run through custom preg_replace to remove most special characters and spaces.
						if ( strpos( $value, '#' ) === 0 ) {
							$new_value[ $key ] = sanitize_hex_color( $value );
						} else {
							$new_value[ $key ] = preg_replace( '/[^A-Za-z0-9_)(\-,.]/', '', $value );
						}
						break;
					case 'gradient':
						// gradient has some extra symbols that need to be allowed.
						$new_value[ $key ] = sanitize_text_field( $value );
						break;
					case 'enabled':
					case 'locked':
						// return a boolean.
						$new_value[ $key ] = ( ( isset( $value ) && true == $value ) ? true : false );
						break;
					case 'url':
						// URL is used for custom social images and background images so save as a url.
						$new_value[ $key ] = esc_url_raw( $value );
						break;
					default:
						// To allow for strings with capitals, negative numbers and decimals. Remove other special characters and spaces.
						$new_value[ $key ] = preg_replace( '/[^A-Za-z0-9_\-.]/', '', $value );
						break;
				}
			}
		}
		return $new_value;
	}
	/**
	 * Sanitize customizer toggle setting.
	 *
	 * @param mixed $value the value before sanitized.
	 *
	 * @return bool
	 */
	public static function base_sanitize_toggle( $value ) {
		return ( ( isset( $value ) && true == $value ) ? true : false );
	}
	/**
	 * Sanitize customizer google subset setting.
	 *
	 * @param mixed $start_value the value before sanitized.
	 *
	 * @return mixed
	 */
	public static function base_sanitize_google_subsets( $start_value ) {
		if ( empty( $start_value ) ) {
			return '';
		}
		if ( ! is_array( $start_value ) ) {
			return '';
		}
		$new_value = array();
		foreach ( $start_value as $key => $value ) {
			// Sanitize key, remove spaces and special characters.
			$key               = self::base_sanitize_key_string( $key );
			$new_value[ $key ] = ( ( isset( $value ) && true == $value ) ? true : false );
		}
		return $new_value;
	}
}
