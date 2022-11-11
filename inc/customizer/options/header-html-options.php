<?php
/**
 * Header Main Row Options
 *
 * @package Base
 */

namespace Base;

use Base\Theme_Customizer;
use function Base\webapp;

$settings = array(
	'header_html_tabs' => array(
		'control_type' => 'base_tab_control',
		'section'      => 'header_html',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'avanam' ),
				'target' => 'header_html',
			),
			'design' => array(
				'label'  => __( 'Design', 'avanam' ),
				'target' => 'header_html_design',
			),
			'active' => 'general',
		),
	),
	'header_html_tabs_design' => array(
		'control_type' => 'base_tab_control',
		'section'      => 'header_html_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'avanam' ),
				'target' => 'header_html',
			),
			'design' => array(
				'label'  => __( 'Design', 'avanam' ),
				'target' => 'header_html_design',
			),
			'active' => 'design',
		),
	),
	'header_html_content' => array(
		'control_type' => 'base_editor_control',
		'section'      => 'header_html',
		'sanitize'     => 'wp_kses_post',
		'priority'     => 4,
		'default'      => webapp()->default( 'header_html_content' ),
		'partial'      => array(
			'selector'            => '.header-html',
			'container_inclusive' => true,
			'render_callback'     => 'Base\header_html',
		),
		'input_attrs'  => array(
			'id' => 'header_html',
		),
	),
	'header_html_wpautop' => array(
		'control_type' => 'base_switch_control',
		'sanitize'     => 'base_sanitize_toggle',
		'section'      => 'header_html',
		'default'      => webapp()->default( 'header_html_wpautop' ),
		'label'        => esc_html__( 'Automatically Add Paragraphs', 'avanam' ),
		'partial'      => array(
			'selector'            => '.header-html',
			'container_inclusive' => true,
			'render_callback'     => 'Base\header_html',
		),
	),
	'header_html_typography' => array(
		'control_type' => 'base_typography_control',
		'section'      => 'header_html_design',
		'label'        => esc_html__( 'Font', 'avanam' ),
		'default'      => webapp()->default( 'header_html_typography' ),
		'live_method'     => array(
			array(
				'type'     => 'css_typography',
				'selector' => '#main-header .header-html',
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
			'id' => 'header_html_typography',
		),
	),
	'header_html_link_style' => array(
		'control_type' => 'base_select_control',
		'section'      => 'header_html_design',
		'default'      => webapp()->default( 'header_html_link_style' ),
		'label'        => esc_html__( 'Link Style', 'avanam' ),
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
				'selector' => '#main-header .header-html',
				'pattern'  => 'inner-link-style-$',
				'key'      => '',
			),
		),
	),
	'header_html_link_color' => array(
		'control_type' => 'base_color_control',
		'section'      => 'header_html_design',
		'label'        => esc_html__( 'Link Colors', 'avanam' ),
		'default'      => webapp()->default( 'header_html_link_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#main-header .header-html a',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '#main-header .header-html a:hover',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
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
	'header_html_margin' => array(
		'control_type' => 'base_measure_control',
		'section'      => 'header_html_design',
		'priority'     => 10,
		'default'      => webapp()->default( 'header_html_margin' ),
		'label'        => esc_html__( 'Margin', 'avanam' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#main-header .header-html',
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

