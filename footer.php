<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Base
 */

namespace Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hook for bottom of inner wrap.
 */
do_action( 'base_after_content' );
?>
	</div><!-- #inner-wrap -->
	<?php
	do_action( 'base_before_footer' );
	/**
	 * Base footer hook.
	 *
	 * @hooked Base\footer_markup - 10
	 */
	do_action( 'base_footer' );

	do_action( 'base_after_footer' );
	?>
</div><!-- #wrapper -->
<?php do_action( 'base_after_wrapper' ); ?>

<?php wp_footer(); ?>
</body>
</html>
