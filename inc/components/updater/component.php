<?php
/**
 * Base\Updater\Component class
 *
 * @package Base
 */

namespace Base\Updater;

use Base\Component_Interface;
use function Base\webapp;
use function add_filter;
use function get_template;
use function wp_get_theme;
use function get_bloginfo;

/**
 * Class for improving updater among various core features.
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'updater';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_filter( 'site_transient_update_themes', array( $this, 'base_theme_updater' ) );
	}

	/**
	 * Theme Updater : $transient
	 *
	*/
	public function base_theme_updater( $transient ) {

		$stylesheet = get_template();
		$version = webapp()->get_version();

		if( false == $remote = get_transient( 'avanam-theme-update'.$version ) ) {

			// connect to a remote server where the update information is stored
			$remote = wp_remote_get(
				'https://avanam.org/updates/avanam.json',
				array(
					'timeout' => 10,
					'headers' => array(
						'Accept' => 'application/json'
					)
				)
			);

			if(
				is_wp_error( $remote )
				|| 200 !== wp_remote_retrieve_response_code( $remote )
				|| empty( wp_remote_retrieve_body( $remote ) )
			) {
				return $transient;
			}

			$remote = json_decode( wp_remote_retrieve_body( $remote ) );

			if( ! $remote ) {
				return $transient;
			}

			set_transient( 'avanam-theme-update'.$version, $remote, HOUR_IN_SECONDS );

		}
		
		$data = array(
			'theme' => $stylesheet,
			'url' => $remote->details_url,
			'requires' => $remote->requires,
			'requires_php' => $remote->requires_php,
			'new_version' => $remote->version,
			'package' => $remote->download_url,
		);

		if(
			$remote
			&& version_compare( $version, $remote->version, '<' )
			&& version_compare( $remote->requires, get_bloginfo( 'version' ), '<' )
		) {

			if(
				isset( $transient->response )
				&& ! empty( $transient->response )
			) {
			$transient->response[ $stylesheet ] = $data;
			}

		} else {

			if(
				isset( $transient->no_update )
				&& ! empty( $transient->no_update )
			) {
			$transient->no_update[ $stylesheet ] = $data;
			}

		}

		return $transient;

	}
}
