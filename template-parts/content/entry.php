<?php
/**
 * Template part for displaying a post
 *
 * @package Base
 */

namespace Base;

?>

<article <?php post_class( 'entry content-bg loop-entry' ); ?>>
	<?php
		/**
		 * Hook for entry thumbnail.
		 *
		 * @hooked Base\loop_entry_thumbnail
		 */
		do_action( 'base_loop_entry_thumbnail' );
	?>
	<div class="entry-content-wrap">
		<?php
		/**
		 * Hook for entry content.
		 *
		 * @hooked Base\loop_entry_header - 10
		 * @hooked Base\loop_entry_summary - 20
		 * @hooked Base\loop_entry_footer - 30
		 */
		do_action( 'base_loop_entry_content' );
		?>
	</div>
</article>
