<?php
/**
 * Template part for displaying the header branding/logo
 *
 * @package Base
 */

namespace Base;

?>
<div class="site-header-item site-header-focus-item" data-section="title_tagline">
	<?php
	/**
	 * Base Mobile Site Branding
	 *
	 * Hooked Base\mobile_site_branding
	 */
	do_action( 'base_mobile_site_branding' );
	?>
</div><!-- data-section="title_tagline" -->
