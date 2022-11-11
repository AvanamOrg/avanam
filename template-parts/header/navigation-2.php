<?php
/**
 * Template part for displaying the header navigation menu
 *
 * @package Base
 */

namespace Base;

?>
<div class="site-header-item site-header-focus-item site-header-item-main-navigation header-navigation-layout-stretch-<?php echo ( webapp()->option( 'secondary_navigation_stretch' ) ? 'true' : 'false' ); ?> header-navigation-layout-fill-stretch-<?php echo ( webapp()->option( 'secondary_navigation_fill_stretch' ) ? 'true' : 'false' ); ?>" data-section="base_customizer_secondary_navigation">
	<?php
	/**
	 * Base Secondary Navigation
	 *
	 * Hooked Base\secondary_navigation
	 */
	do_action( 'base_secondary_navigation' );
	?>
</div><!-- data-section="secondary_navigation" -->
