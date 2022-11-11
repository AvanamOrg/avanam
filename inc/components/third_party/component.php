<?php
/**
 * Base\Third_Party\Component class
 *
 * @package Base
 */

namespace Base\Third_Party;

use Base\Component_Interface;
use function Base\webapp;
use function add_action;
use function add_filter;
use function get_template_part;
use function locate_template;

/**
 * Class for integrating with the block Third_Party.
 *
 * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'third_party';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		// WeDocs.
		remove_action( 'wedocs_before_main_content', 'wedocs_template_wrapper_start' );
		remove_action( 'wedocs_after_main_content', 'wedocs_template_wrapper_end' );
		add_action( 'wedocs_before_main_content', array( $this, 'output_content_wrapper' ) );
		add_action( 'wedocs_after_main_content', array( $this, 'output_content_wrapper_end' ) );
		add_action( 'base_gallery_post_before', array( $this, 'output_content_wrapper' ) );
		add_action( 'base_gallery_post_after', array( $this, 'output_content_wrapper_end' ) );
		add_action( 'base_gallery_post_before_content', array( $this, 'output_content_inner' ) );
		add_action( 'base_gallery_post_after_content', array( $this, 'output_content_inner_end' ) );
		add_filter( 'base_gallery_single_show_title', '__return_false' );
		add_action( 'base_gallery_album_before', array( $this, 'output_content_wrapper' ) );
		add_action( 'base_gallery_album_after', array( $this, 'output_content_wrapper_end' ) );
		add_action( 'base_gallery_album_before_content', array( $this, 'output_archive_content_inner' ) );
		add_action( 'base_gallery_album_after_content', array( $this, 'output_content_inner_end' ) );
	}
	/**
	 * Adds theme output Wrapper.
	 */
	public function output_content_inner() {
		if ( webapp()->show_feature_above() ) {
			get_template_part( 'template-parts/content/entry_thumbnail', get_post_type() );
		}
		?>
		<div class="entry-content-wrap">
		<?php
		if ( webapp()->show_in_content_title() ) {
			get_template_part( 'template-parts/content/entry_header', get_post_type() );
		}
		if ( webapp()->show_feature_below() ) {
			get_template_part( 'template-parts/content/entry_thumbnail', get_post_type() );
		}
	}
	/**
	 * Adds theme output Wrapper.
	 */
	public function output_content_inner_end() {
		?>
		</div>
		<?php
	}
	/**
	 * Adds theme output Wrapper.
	 */
	public function output_archive_content_inner() {
		/**
		 * Hook for anything before main content
		 */
		do_action( 'base_before_archive_content' );
		if ( webapp()->show_in_content_title() ) {
			get_template_part( 'template-parts/content/archive_header' );
		}
		?>
		<div id="archive-container" class="content-wrap">
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
}
