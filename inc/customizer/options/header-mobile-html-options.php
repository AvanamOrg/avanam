<?php
/**
 * Header Main Row Options
 *
 * @package Base
 */

namespace Base;

use Base\Theme_Customizer;
use function Base\webapp;

ob_start(); ?>
<div class="base-compontent-tabs nav-tab-wrapper wp-clearfix">
	<a href="#" class="nav-tab base-general-tab base-compontent-tabs-button nav-tab-active" data-tab="general">
		<span><?php esc_html_e( 'General', 'avanam' ); ?></span>
	</a>
	<a href="#" class="nav-tab base-design-tab base-compontent-tabs-button" data-tab="design">
		<span><?php esc_html_e( 'Design', 'avanam' ); ?></span>
	</a>
</div>
<?php
$compontent_tabs = ob_get_clean();
$settings = array(
	'mobile_html_tabs' => array(
		'control_type' => 'base_blank_control',
		'section'      => 'mobile_html',
		'settings'     => false,
		'priority'     => 1,
		'description'  => $compontent_tabs,
	),
	'mobile_html_content' => array(
		'control_type' => 'base_editor_control',
		'section'      => 'mobile_html',
		'sanitize'     => 'wp_kses_post',
		'priority'     => 4,
		'default'      => webapp()->default( 'mobile_html_content' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'input_attrs'  => array(
			'id' => 'mobile_html',
		),
		'partial'      => array(
			'selector'            => '.mobile-html',
			'container_inclusive' => true,
			'render_callback'     => 'Base\mobile_html',
		),
	),
	'mobile_html_wpautop' => array(
		'control_type' => 'base_switch_control',
		'sanitize'     => 'base_sanitize_toggle',
		'section'      => 'mobile_html',
		'default'      => webapp()->default( 'mobile_html_wpautop' ),
		'label'        => esc_html__( 'Automatically Add Paragraphs', 'avanam' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'partial'      => array(
			'selector'            => '.mobile-html',
			'container_inclusive' => true,
			'render_callback'     => 'Base\mobile_html',
		),
	),
	'mobile_html_typography' => array(
		'control_type' => 'base_typography_control',
		'section'      => 'mobile_html',
		'label'        => esc_html__( 'Font', 'avanam' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'default'      => webapp()->default( 'mobile_html_typography' ),
		'live_method'     => array(
			array(
				'type'     => 'css_typography',
				'selector' => '.mobile-html',
				'pattern'  => array(
					'desktop' => '$',
					'tablet'  => '$',
					'mobile'  => '$',
				),
				'property' => 'font',
				'key'      => 'typography',
			),
		),
		'input_attrs'  => array(
			'id' => 'mobile_html_typography',
		),
	),
	'mobile_html_link_color' => array(
		'control_type' => 'base_color_control',
		'section'      => 'mobile_html',
		'label'        => esc_html__( 'Link Colors', 'avanam' ),
		'default'      => webapp()->default( 'mobile_html_link_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.mobile-html a',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '.mobile-html a:hover',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
			),
		),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'input_attrs'  => array(
			'colors' => array(
				'color' => array(
					'tooltip' => __( 'Initial Color', 'avanam' ),
					'palette' => true,
				),
				'hover' => array(
					'tooltip' => __( 'Hover Color', 'avanam' ),
					'palette' => true,
				),
			),
		),
	),
	'mobile_html_link_style' => array(
		'control_type' => 'base_select_control',
		'section'      => 'mobile_html',
		'default'      => webapp()->default( 'mobile_html_link_style' ),
		'label'        => esc_html__( 'Link Style', 'avanam' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'input_attrs'  => array(
			'options' => array(
				'normal' => array(
					'name' => __( 'Underline', 'avanam' ),
				),
				'plain' => array(
					'name' => __( 'No Underline', 'avanam' ),
				),
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '#mobile-header .mobile-html',
				'pattern'  => 'inner-link-style-$',
				'key'      => '',
			),
		),
	),
	'mobile_html_margin' => array(
		'control_type' => 'base_measure_control',
		'section'      => 'mobile_html',
		'priority'     => 10,
		'default'      => webapp()->default( 'mobile_html_margin' ),
		'label'        => esc_html__( 'Margin', 'avanam' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.mobile-html',
				'property' => 'margin',
				'pattern'  => '$',
				'key'      => 'measure',
			),
		),
		'input_attrs'  => array(
			'responsive' => false,
		),
	),
);

Theme_Customizer::add_settings( $settings );

