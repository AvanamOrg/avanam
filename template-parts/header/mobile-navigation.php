<?php
/**
 * Template part for displaying the header navigation menu
 *
 * @package Base
 */

namespace Base;

?>
<div class="site-header-item site-header-focus-item site-header-item-mobile-navigation mobile-navigation-layout-stretch-<?php echo ( webapp()->option( 'mobile_navigation_stretch' ) ? 'true' : 'false' ); ?>" data-section="base_customizer_mobile_navigation">
	<?php
	/**
	 * Base Mobile Navigation
	 *
	 * Hooked Base\mobile_navigation
	 */
	do_action( 'base_mobile_navigation' );
	?>
</div><!-- data-section="mobile_navigation" -->
