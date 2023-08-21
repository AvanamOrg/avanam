<?php
/**
 * Template part for displaying a post's footer
 *
 * @package Base
 */

namespace Base;

use function Base\webapp;
use function get_the_author;
use function get_avatar;
use function get_the_author_meta;
use function the_author_meta;
use function get_the_author_posts_link;

webapp()->print_styles( 'base-author-box' );

?>
<div class="entry-author entry-author-style-<?php echo esc_attr( webapp()->option( 'post_author_box_style' ) ); ?><?php echo ( webapp()->option( 'post_footer_area_boxed' ) ? ' content-bg entry-content-wrap entry' : '' ); ?>">
	<div class="entry-author-profile author-profile vcard">
		<div class="entry-author-avatar">
			<?php echo get_avatar( get_the_author_meta( 'ID' ), 80 ); ?>
		</div>
		<b class="entry-author-name author-name fn"><?php echo wp_kses_post( webapp()->option( 'post_author_box_link' ) ? get_the_author_posts_link() : get_the_author() ); ?></b>
		<?php if ( get_the_author_meta( 'occupation' ) ) { ?>
			<p class="entry-author-occupation author-occupation"><?php the_author_meta( 'occupation' ); ?></p>
		<?php } ?>
		<div class="entry-author-description author-bio">
			<?php the_author_meta( 'description' ); ?>
		</div>
		<div class="entry-author-follow author-follow">
			<?php
			foreach ( array( 'facebook', 'twitter', 'instagram', 'youtube', 'flickr', 'vimeo', 'linkedin', 'pinterest', 'dribbble', 'amazon', 'medium', 'goodreads', 'bookbub' ) as $social ) {
				if ( get_the_author_meta( $social ) ) {
					$url = get_the_author_meta( $social );

					// Might need some special parsing if rank math is active.
					if ( defined( 'RANK_MATH_VERSION' ) && ! str_contains( $url, '.com' ) ) {
						if ( 'twitter' == $social ) {
							$url = 'https://twitter.com/' . $url;
						}
					}
					?>
					<a href="<?php echo esc_url( $url ); ?>" class="<?php echo esc_attr( $social ); ?>-link social-button" target="_blank" rel="noopener" title="<?php /* translators: 1: Author Name, 2: Social Media Name */ echo sprintf( esc_attr__( 'Follow %1$s on %2$s', 'avanam' ), esc_attr( get_the_author_meta( 'display_name' ) ), esc_attr( ucfirst( $social ) ) ); ?>">
						<?php webapp()->print_icon( $social, '', false ); ?>
					</a>
					<?php
				}
			}
			?>
		</div><!--.author-follow-->
	</div>
</div><!-- .entry-author -->
