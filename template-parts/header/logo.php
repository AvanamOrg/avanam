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
	 * Base Site Branding
	 *
	 * Hooked Base\site_branding
	 */
	do_action( 'base_site_branding' );
	?>
</div><!-- data-section="title_tagline" -->
