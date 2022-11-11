<?php
/**
 * The template for displaying offline pages
 *
 * @link https://github.com/xwp/pwa-wp#offline--500-error-handling
 *
 * @package Base
 */

namespace Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Prevent showing nav menus.
add_filter( 'has_nav_menu', '__return_false' );

get_header();

webapp()->print_styles( 'base-content' );
/**
 * Hook for Hero Section
 */
do_action( 'base_hero_header' );

?>
<div id="primary" class="content-area">
	<div class="content-container site-container">
		<main id="main" class="site-main" role="main">
			<?php
			/**
			 * Hook for anything before main content
			 */
			do_action( 'base_before_main_content' );

			get_template_part( 'template-parts/content/error', 'offline' );
			/**
			 * Hook for anything after main content.
			 */
			do_action( 'base_after_main_content' );
			?>
		</main><!-- #main -->
		<?php
		get_sidebar();
		?>
	</div>
</div><!-- #primary -->
<?php
get_footer();
