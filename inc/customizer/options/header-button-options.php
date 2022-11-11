<?php
/**
 * Header Main Row Options
 *
 * @package Base
 */

namespace Base;

use Base\Theme_Customizer;
use function Base\webapp;

Theme_Customizer::add_settings(
	array(
		'header_button_tabs' => array(
			'control_type' => 'base_tab_control',
			'section'      => 'header_button',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => array(
				'general' => array(
					'label'  => __( 'General', 'avanam' ),
					'target' => 'header_button',
				),
				'design' => array(
					'label'  => __( 'Design', 'avanam' ),
					'target' => 'header_button_design',
				),
				'active' => 'general',
			),
		),
		'header_button_tabs_design' => array(
			'control_type' => 'base_tab_control',
			'section'      => 'header_button_design',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => array(
				'general' => array(
					'label'  => __( 'General', 'avanam' ),
					'target' => 'header_button',
				),
				'design' => array(
					'label'  => __( 'Design', 'avanam' ),
					'target' => 'header_button_design',
				),
				'active' => 'design',
			),
		),
		'header_button_label' => array(
			'control_type' => 'base_text_control',
			'section'      => 'header_button',
			'sanitize'     => 'sanitize_text_field',
			'priority'     => 4,
			'label'        => esc_html__( 'Label', 'avanam' ),
			'default'      => webapp()->default( 'header_button_label' ),
			'live_method'     => array(
				array(
					'type'     => 'html',
					'selector' => '#main-header .header-button',
					'pattern'  => '$',
					'key'      => '',
				),
			),
		),
		'header_button_link' => array(
			'control_type' => 'base_text_control',
			'section'      => 'header_button',
			'sanitize'     => 'esc_url_raw',
			'label'        => esc_html__( 'URL', 'avanam' ),
			'priority'     => 4,
			'default'      => webapp()->default( 'header_button_link' ),
			'partial'      => array(
				'selector'            => '.header-button-wrap',
				'container_inclusive' => true,
				'render_callback'     => 'Base\header_button',
			),
		),
		'header_button_target' => array(
			'control_type' => 'base_switch_control',
			'sanitize'     => 'base_sanitize_toggle',
			'section'      => 'header_button',
			'priority'     => 6,
			'default'      => webapp()->default( 'header_button_target' ),
			'label'        => esc_html__( 'Open in New Tab?', 'avanam' ),
		),
		'header_button_nofollow' => array(
			'control_type' => 'base_switch_control',
			'sanitize'     => 'base_sanitize_toggle',
			'section'      => 'header_button',
			'priority'     => 6,
			'default'      => webapp()->default( 'header_button_nofollow' ),
			'label'        => esc_html__( 'Set link to nofollow?', 'avanam' ),
		),
		'header_button_sponsored' => array(
			'control_type' => 'base_switch_control',
			'sanitize'     => 'base_sanitize_toggle',
			'section'      => 'header_button',
			'priority'     => 6,
			'default'      => webapp()->default( 'header_button_sponsored' ),
			'label'        => esc_html__( 'Set link attribute Sponsored?', 'avanam' ),
		),
		'header_button_download' => array(
			'control_type' => 'base_switch_control',
			'sanitize'     => 'base_sanitize_toggle',
			'section'      => 'header_button',
			'priority'     => 6,
			'default'      => webapp()->default( 'header_button_download' ),
			'label'        => esc_html__( 'Set link to Download?', 'avanam' ),
		),
		'header_button_style' => array(
			'control_type' => 'base_radio_icon_control',
			'section'      => 'header_button',
			'priority'     => 6,
			'default'      => webapp()->default( 'header_button_style' ),
			'label'        => esc_html__( 'Button Style', 'avanam' ),
			'live_method'     => array(
				array(
					'type'     => 'class',
					'selector' => '#main-header .header-button',
					'pattern'  => 'button-style-$',
					'key'      => '',
				),
			),
			'input_attrs'  => array(
				'layout' => array(
					'filled' => array(
						'name'    => __( 'Filled', 'avanam' ),
					),
					'outline' => array(
						'name'    => __( 'Outline', 'avanam' ),
						'icon'    => '',
					),
				),
				'responsive' => false,
			),
		),
		'header_button_visibility' => array(
			'control_type' => 'base_radio_icon_control',
			'section'      => 'header_button',
			'priority'     => 6,
			'default'      => webapp()->default( 'header_button_visibility' ),
			'label'        => esc_html__( 'Button Visibility', 'avanam' ),
			'partial'      => array(
				'selector'            => '.header-button-wrap',
				'container_inclusive' => true,
				'render_callback'     => 'Base\header_button',
			),
			'input_attrs'  => array(
				'layout' => array(
					'all' => array(
						'name'    => __( 'Everyone', 'avanam' ),
					),
					'loggedout' => array(
						'name'    => __( 'Logged Out Only', 'avanam' ),
					),
					'loggedin' => array(
						'name'    => __( 'Logged In Only', 'avanam' ),
					),
				),
				'responsive' => false,
			),
		),
		'header_button_size' => array(
			'control_type' => 'base_radio_icon_control',
			'section'      => 'header_button_design',
			'priority'     => 4,
			'default'      => webapp()->default( 'header_button_size' ),
			'label'        => esc_html__( 'Button Size', 'avanam' ),
			'live_method'     => array(
				array(
					'type'     => 'class',
					'selector' => '#main-header .header-button',
					'pattern'  => 'button-size-$',
					'key'      => '',
				),
			),
			'input_attrs'  => array(
				'layout' => array(
					'small' => array(
						'name'    => __( 'Sm', 'avanam' ),
					),
					'medium' => array(
						'name'    => __( 'Md', 'avanam' ),
						'icon'    => '',
					),
					'large' => array(
						'name'    => __( 'Lg', 'avanam' ),
						'icon'    => '',
					),
					'custom' => array(
						'name'    => __( 'Custom', 'avanam' ),
						'icon'    => '',
					),
				),
				'responsive' => false,
			),
		),
		'header_button_padding' => array(
			'control_type' => 'base_measure_control',
			'section'      => 'header_button_design',
			'priority'     => 10,
			'default'      => webapp()->default( 'header_button_padding' ),
			'label'        => esc_html__( 'Padding', 'avanam' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '#main-header .button-size-custom.header-button',
					'property' => 'padding',
					'pattern'  => '$',
					'key'      => 'measure',
				),
			),
			'context'      => array(
				array(
					'setting'    => 'header_button_size',
					'operator'   => '=',
					'value'      => 'custom',
				),
			),
			'input_attrs'  => array(
				'responsive' => false,
			),
		),
		'header_button_color' => array(
			'control_type' => 'base_color_control',
			'section'      => 'header_button_design',
			'label'        => esc_html__( 'Colors', 'avanam' ),
			'default'      => webapp()->default( 'header_button_color' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '#main-header .header-button',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				),
				array(
					'type'     => 'css',
					'selector' => '#main-header .header-button:hover',
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
		'header_button_background' => array(
			'control_type' => 'base_color_control',
			'section'      => 'header_button_design',
			'label'        => esc_html__( 'Background Colors', 'avanam' ),
			'default'      => webapp()->default( 'header_button_background' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '#main-header .header-button',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'color',
				),
				array(
					'type'     => 'css',
					'selector' => '#main-header .header-button:hover',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'hover',
				),
			),
			'context'      => array(
				array(
					'setting'    => 'header_button_style',
					'operator'   => '=',
					'value'      => 'filled',
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
		'header_button_border_colors' => array(
			'control_type' => 'base_color_control',
			'section'      => 'header_button_design',
			'label'        => esc_html__( 'Border Colors', 'avanam' ),
			'default'      => webapp()->default( 'header_button_border' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '#main-header .header-button-wrap .header-button',
					'property' => 'border-color',
					'pattern'  => '$',
					'key'      => 'color',
				),
				array(
					'type'     => 'css',
					'selector' => '#main-header .header-button-wrap .header-button:hover',
					'property' => 'border-color',
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
		'header_button_border' => array(
			'control_type' => 'base_border_control',
			'section'      => 'header_button_design',
			'label'        => esc_html__( 'Border', 'avanam' ),
			'default'      => webapp()->default( 'header_button_border' ),
			'live_method'     => array(
				array(
					'type'     => 'css_border',
					'selector' => '#main-header .header-button',
					'property' => 'border',
					'pattern'  => '$',
					'key'      => 'border',
				),
			),
			'input_attrs'  => array(
				'responsive' => false,
				'color'      => false,
			),
		),
		'header_button_radius' => array(
			'control_type' => 'base_measure_control',
			'section'      => 'header_button_design',
			'priority'     => 10,
			'default'      => webapp()->default( 'header_button_radius' ),
			'label'        => esc_html__( 'Border Radius', 'avanam' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '#main-header .header-button',
					'property' => 'border-radius',
					'pattern'  => '$',
					'key'      => 'measure',
				),
			),
			'input_attrs'  => array(
				'responsive' => false,
			),
		),
		'header_button_typography' => array(
			'control_type' => 'base_typography_control',
			'section'      => 'header_button_design',
			'label'        => esc_html__( 'Font', 'avanam' ),
			'default'      => webapp()->default( 'header_button_typography' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => '#main-header .header-button',
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
				'id' => 'header_button_typography',
				'options' => 'no-color',
			),
		),
		'header_button_shadow' => array(
			'control_type' => 'base_shadow_control',
			'section'      => 'header_button_design',
			'label'        => esc_html__( 'Button Shadow', 'avanam' ),
			'live_method'     => array(
				array(
					'type'     => 'css_boxshadow',
					'selector' => '#main-header .header-button',
					'property' => 'box-shadow',
					'pattern'  => '$',
					'key'      => '',
				),
			),
			'default'      => webapp()->default( 'header_button_shadow' ),
		),
		'header_button_shadow_hover' => array(
			'control_type' => 'base_shadow_control',
			'section'      => 'header_button_design',
			'label'        => esc_html__( 'Button Hover State Shadow', 'avanam' ),
			'live_method'     => array(
				array(
					'type'     => 'css_boxshadow',
					'selector' => '#main-header .header-button',
					'property' => 'box-shadow',
					'pattern'  => '$',
					'key'      => '',
				),
			),
			'default'      => webapp()->default( 'header_button_shadow_hover' ),
		),
		'header_button_margin' => array(
			'control_type' => 'base_measure_control',
			'section'      => 'header_button_design',
			'priority'     => 10,
			'default'      => webapp()->default( 'header_button_margin' ),
			'label'        => esc_html__( 'Margin', 'avanam' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '#main-header .header-button',
					'property' => 'margin',
					'pattern'  => '$',
					'key'      => 'measure',
				),
			),
			'input_attrs'  => array(
				'responsive' => false,
			),
		),
	)
);
