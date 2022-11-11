<?php
/**
 * Template part for displaying a post's header
 *
 * @package Base
 */

namespace Base;

?>
<header class="entry-header">

	<?php
	/**
	 * Hook for entry header.
	 *
	 * @hooked Base\loop_entry_taxonomies - 10
	 * @hooked Base\loop_entry_title - 20
	 * @hooked Base\loop_entry_meta - 30
	 */
	do_action( 'base_loop_entry_header' );
	?>
</header><!-- .entry-header -->
