<?php
/**
 * Template part for displaying the page content when a 500 error has occurred
 *
 * @package Base
 */

namespace Base;

?>
<section class="error">

	<div class="page-content entry content-bg">

		<div class="entry-content-wrap">
			<header class="page-header">
				<h1 class="page-title">
					<?php esc_html_e( 'Oops! Something went wrong.', 'avanam' ); ?>
				</h1>
			</header><!-- .page-header -->

			<?php
			if ( function_exists( 'wp_service_worker_error_message_placeholder' ) ) {
				wp_service_worker_error_message_placeholder();
			}
			if ( function_exists( 'wp_service_worker_error_details_template' ) ) {
				wp_service_worker_error_details_template();
			}
			?>
		</div><!-- .entry-content-wrap -->
	</div><!-- .page-content -->
</section><!-- .error -->
