<?php
/**
 * View: Default Template for Events
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/default-template.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 5.0.0
 */

use Tribe\Events\Views\V2\Template_Bootstrap;

get_header();
/**
 * Hook for anything before main tag
 */
do_action( 'base_tribe_events_before_main_tag' );
echo tribe( Template_Bootstrap::class )->get_view_html();
do_action( 'base_tribe_events_after_main_tag' );
get_footer();
