<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Base
 */

namespace Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js" <?php webapp()->print_microdata( 'html' ); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php
/**
 * Base before wrapper hook.
 */
do_action( 'base_before_wrapper' );
?>
<div id="wrapper" class="site wp-site-blocks">
	<?php
	/**
	 * Base before header hook.
	 *
	 * @hooked base_do_skip_to_content_link - 2
	 */
	do_action( 'base_before_header' );

	/**
	 * Base header hook.
	 *
	 * @hooked Base\header_markup - 10
	 */
	do_action( 'base_header' );

	do_action( 'base_after_header' );
	?>

	<div id="inner-wrap" class="wrap hfeed bst-clear">
		<?php
		/**
		 * Hook for top of inner wrap.
		 */
		do_action( 'base_before_content' );
		?>
