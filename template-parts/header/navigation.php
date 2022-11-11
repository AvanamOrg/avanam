<?php
/**
 * Template part for displaying the header navigation menu
 *
 * @package Base
 */

namespace Base;

?>
<div class="site-header-item site-header-focus-item site-header-item-main-navigation header-navigation-layout-stretch-<?php echo ( webapp()->option( 'primary_navigation_stretch' ) ? 'true' : 'false' ); ?> header-navigation-layout-fill-stretch-<?php echo ( webapp()->option( 'primary_navigation_fill_stretch' ) ? 'true' : 'false' ); ?>" data-section="base_customizer_primary_navigation">
	<?php
	/**
	 * Base Primary Navigation
	 *
	 * Hooked Base\primary_navigation
	 */
	do_action( 'base_primary_navigation' );
	?>
</div><!-- data-section="primary_navigation" -->
