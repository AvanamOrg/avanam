<?php
/**
 * Header Builder Options
 *
 * @package Base
 */

namespace Base;

use Base\Theme_Customizer;
use function Base\webapp;

ob_start(); ?>
<div class="base-compontent-custom fonts-flush-button wp-clearfix">
	<span class="customize-control-title">
		<?php esc_html_e( 'Flush Local Fonts Cache', 'avanam' ); ?>
	</span>
	<span class="description customize-control-description">
		<?php esc_html_e( 'Click the button to reset the local fonts cache', 'avanam' ); ?>
	</span>
	<input type="button" class="button base-flush-local-fonts-button" name="base-flush-local-fonts-button" value="<?php esc_attr_e( 'Flush Local Font Files', 'avanam' ); ?>" />
</div>
<?php
$base_flush_button = ob_get_clean();

Theme_Customizer::add_settings(
	array(
		'enable_scroll_to_id' => array(
			'control_type' => 'base_switch_control',
			'sanitize'     => 'base_sanitize_toggle',
			'section'      => 'general_performance',
			'default'      => webapp()->default( 'enable_scroll_to_id' ),
			'label'        => esc_html__( 'Enable Scroll To ID', 'avanam' ),
		),
		'lightbox' => array(
			'control_type' => 'base_switch_control',
			'sanitize'     => 'base_sanitize_toggle',
			'section'      => 'general_performance',
			'default'      => webapp()->default( 'lightbox' ),
			'label'        => esc_html__( 'Enable Lightbox', 'avanam' ),
		),
		'load_fonts_local' => array(
			'control_type' => 'base_switch_control',
			'sanitize'     => 'base_sanitize_toggle',
			'section'      => 'general_performance',
			'default'      => webapp()->default( 'load_fonts_local' ),
			'label'        => esc_html__( 'Load Google Fonts Locally', 'avanam' ),
		),
		'preload_fonts_local' => array(
			'control_type' => 'base_switch_control',
			'sanitize'     => 'base_sanitize_toggle',
			'section'      => 'general_performance',
			'default'      => webapp()->default( 'preload_fonts_local' ),
			'label'        => esc_html__( 'Preload Local Fonts', 'avanam' ),
			'context'      => array(
				array(
					'setting'    => 'load_fonts_local',
					'operator'   => '==',
					'value'      => true,
				),
			),
		),
		'load_fonts_local_flush' => array(
			'control_type' => 'base_blank_control',
			'section'      => 'general_performance',
			'settings'     => false,
			'description'  => $base_flush_button,
			'context'      => array(
				array(
					'setting'    => 'load_fonts_local',
					'operator'   => '==',
					'value'      => true,
				),
			),
		),
		'enable_preload' => array(
			'control_type' => 'base_switch_control',
			'sanitize'     => 'base_sanitize_toggle',
			'section'      => 'general_performance',
			'default'      => webapp()->default( 'enable_preload' ),
			'label'        => esc_html__( 'Enable CSS Preload', 'avanam' ),
		),
	)
);
