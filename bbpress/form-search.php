<?php
/**
 * Search 
 *
 * @package bbPress
 * @subpackage Theme
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( bbp_allow_search() ) : ?>

	<div class="bbp-search-form">
		<form role="search" class="search-form" method="get">
			<label for="bbp_search">
				<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'avanam' ); ?></span>
				<input type="search" value="<?php bbp_search_terms(); ?>" placeholder="<?php esc_attr_e( 'Search ...', 'avanam' ); ?>" name="bbp_search" class="search-field" />
			</label>
			<input type="hidden" name="action" value="bbp-search-request" />
			<input class="search-submit" type="submit" value="<?php esc_attr_e( 'Search', 'avanam' ); ?>" />
			<?php do_action( 'bbpress_end_form_search' ); ?>
		</form>
	</div>

<?php endif;
