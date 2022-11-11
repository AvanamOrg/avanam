<?php
/**
 * Template part for displaying a post's footer
 *
 * @package Base
 */

namespace Base;

$tags = get_the_tags();
if ( ! is_array( $tags ) ) {
	return;
}
?>
<div class="entry-tags">
	<span class="tags-links">
		<span class="tags-label screen-reader-text">
			<?php echo esc_html__( 'Post Tags:', 'avanam' ); ?>
		</span>
		<?php
		foreach ( $tags as $tag_item ) {
			$tag_link = get_tag_link( $tag_item->term_id );
			echo '<a href=' . esc_url( $tag_link ) . ' title="' . esc_attr( $tag_item->name ) . '" class="tag-link tag-item-' . esc_attr( $tag_item->slug ) . '" rel="tag"><span class="tag-hash">#</span>' . esc_html( $tag_item->name ) . '</a>';
		}
		?>
	</span>
</div><!-- .entry-tags -->
