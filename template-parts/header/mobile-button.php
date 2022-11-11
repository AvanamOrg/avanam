<?php
/**
 * Template part for displaying the a button in the mobile header.
 *
 * @package Base
 */

namespace Base;

?>
<div class="site-header-item site-header-focus-item" data-section="base_customizer_mobile_button">
	<?php
	/**
	 * Base Mobile Header Button
	 *
	 * Hooked Base\mobile_button
	 */
	do_action( 'base_mobile_button' );
	?>
</div><!-- data-section="mobile_button" -->
