<?php
/**
 * Template part for displaying a post's featured image
 *
 * @package Base
 */

namespace Base;

if ( post_password_required() || ! post_type_supports( get_post_type(), 'thumbnail' ) || ! has_post_thumbnail() ) {
	return;
}
$defaults = array(
	'enabled'   => true,
	'ratio'     => '2-3',
	'size'      => 'medium_large',
	'imageLink' => true,
);
$slug            = ( is_search() ? 'search' : get_post_type() );
$feature_element = webapp()->option( $slug . '_archive_element_feature', $defaults );
if ( isset( $feature_element ) && is_array( $feature_element ) && true === $feature_element['enabled'] ) {
	$feature_element = wp_parse_args( $feature_element, $defaults );
	$ratio = ( isset( $feature_element['ratio'] ) && ! empty( $feature_element['ratio'] ) ? $feature_element['ratio'] : '2-3' );
	$size  = ( isset( $feature_element['size'] ) && ! empty( $feature_element['size'] ) ? $feature_element['size'] : 'medium_large' );
	if ( isset( $feature_element['imageLink'] ) && ! $feature_element['imageLink'] ) {
		?>
		<div class="post-thumbnail base-thumbnail-ratio-<?php echo esc_attr( $ratio ); ?>">
			<div class="post-thumbnail-inner">
				<?php
				the_post_thumbnail(
					$size,
					array(
						'alt' => the_title_attribute(
							array(
								'echo' => false,
							)
						),
					)
				);
				?>
			</div>
		</div><!-- .post-thumbnail -->
		<?php
	} else {
		?>
		<a class="post-thumbnail base-thumbnail-ratio-<?php echo esc_attr( $ratio ); ?>" href="<?php the_permalink(); ?>">
			<div class="post-thumbnail-inner">
				<?php
				the_post_thumbnail(
					$size,
					array(
						'alt' => the_title_attribute(
							array(
								'echo' => false,
							)
						),
					)
				);
				?>
			</div>
		</a><!-- .post-thumbnail -->
		<?php
	}
}
