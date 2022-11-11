<?php
/**
 * Template part for displaying an events back link
 *
 * @package Base
 */

namespace Base;

use function tribe_get_event_label_plural;
use function tribe_get_events_link;

$label = esc_html_x( 'All %s', '%s Events plural label', 'avanam' );
$events_label_plural = tribe_get_event_label_plural();
?>
<p class="tribe-events-back">
	<a href="<?php echo esc_url( tribe_get_events_link() ); ?>">
		&laquo; <?php printf( $label, $events_label_plural ); ?>
	</a>
</p>
