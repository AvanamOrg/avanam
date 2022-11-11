<?php
/**
 * Single Event Template
 *
 * A single event complete template, divided in smaller template parts.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/single-event-blocks.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 4.7
 *
 */

$event_id = $this->get( 'post_id' );

$is_recurring = '';

if ( ! empty( $event_id ) && function_exists( 'tribe_is_recurring_event' ) ) {
	$is_recurring = tribe_is_recurring_event( $event_id );
}
$classes = apply_filters( 'tribe_default_events_block_single_classes', array( 'tribe-events-single', 'tribe-blocks-editor' ) );
?>

<div id="tribe-events-content" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="entry-content-wrap">
		<?php $this->template( 'single-event/notices' ); ?>
		<?php do_action( 'base_tribe_events_header', 'normal', $this ); ?>
		<?php // $this->template( 'single-event/back-link' ); ?>
		<?php // $this->template( 'single-event/title' ); ?>
		<?php if ( $is_recurring ) { ?>
			<?php $this->template( 'single-event/recurring-description' ); ?>
		<?php } ?>
		<?php $this->template( 'single-event/content' ); ?>
		<?php $this->template( 'single-event/comments' ); ?>
		<?php $this->template( 'single-event/footer' ); ?>
	</div>
</div>
