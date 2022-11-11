<?php
/**
 * Template part for displaying the header navigation menu
 *
 * @package Base
 */

namespace Base;

?>
<div class="site-header-item site-header-focus-item site-header-item-navgation-popup-toggle" data-section="base_customizer_mobile_trigger">
	<?php
	/**
	 * Base Mobile Navigation Popup Toggle
	 *
	 * Hooked Base\navigation_popup_toggle
	 */
	do_action( 'base_navigation_popup_toggle' );
	?>
</div><!-- data-section="mobile_trigger" -->
