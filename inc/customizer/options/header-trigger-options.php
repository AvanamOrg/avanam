<?php
/**
 * Header Builder Options
 *
 * @package Base
 */

namespace Base;

use Base\Theme_Customizer;
use function Base\webapp;

$settings = array(
	'mobile_trigger_tabs' => array(
		'control_type' => 'base_tab_control',
		'section'      => 'mobile_trigger',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'avanam' ),
				'target' => 'mobile_trigger',
			),
			'design' => array(
				'label'  => __( 'Design', 'avanam' ),
				'target' => 'mobile_trigger_design',
			),
			'active' => 'general',
		),
	),
	'mobile_trigger_tabs_design' => array(
		'control_type' => 'base_tab_control',
		'section'      => 'mobile_trigger_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'avanam' ),
				'target' => 'mobile_trigger',
			),
			'design' => array(
				'label'  => __( 'Design', 'avanam' ),
				'target' => 'mobile_trigger_design',
			),
			'active' => 'design',
		),
	),
	'mobile_trigger_label' => array(
		'control_type' => 'base_text_control',
		'section'      => 'mobile_trigger',
		'sanitize'     => 'sanitize_text_field',
		'priority'     => 6,
		'default'      => webapp()->default( 'mobile_trigger_label' ),
		'label'        => esc_html__( 'Menu Label', 'avanam' ),
		'live_method'     => array(
			array(
				'type'     => 'html',
				'selector' => '.menu-toggle-label',
				'pattern'  => '$',
				'key'      => '',
			),
		),
	),
	'mobile_trigger_icon' => array(
		'control_type' => 'base_radio_icon_control',
		'section'      => 'mobile_trigger',
		'priority'     => 10,
		'default'      => webapp()->default( 'mobile_trigger_icon' ),
		'label'        => esc_html__( 'Trigger Icon', 'avanam' ),
		'partial'      => array(
			'selector'            => '.menu-toggle-icon',
			'container_inclusive' => false,
			'render_callback'     => 'Base\popup_toggle',
		),
		'input_attrs'  => array(
			'layout' => array(
				'menu' => array(
					'icon' => 'menu',
				),
				'menu2' => array(
					'icon' => 'menu2',
				),
				'menu3' => array(
					'icon' => 'menu3',
				),
			),
			'responsive' => false,
		),
	),
	'mobile_trigger_style' => array(
		'control_type' => 'base_radio_icon_control',
		'section'      => 'mobile_trigger_design',
		'priority'     => 10,
		'default'      => webapp()->default( 'mobile_trigger_style' ),
		'label'        => esc_html__( 'Trigger Style', 'avanam' ),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.menu-toggle-open',
				'pattern'  => 'menu-toggle-style-$',
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'default' => array(
					'name' => __( 'Default', 'avanam' ),
				),
				'bordered' => array(
					'name' => __( 'Bordered', 'avanam' ),
				),
			),
			'responsive' => false,
		),
	),
	'mobile_trigger_border' => array(
		'control_type' => 'base_border_control',
		'section'      => 'mobile_trigger_design',
		'label'        => esc_html__( 'Trigger Border', 'avanam' ),
		'default'      => webapp()->default( 'mobile_trigger_border' ),
		'context'      => array(
			array(
				'setting'    => 'mobile_trigger_style',
				'operator'   => 'sub_object_contains',
				'sub_key'    => 'layout',
				'responsive' => false,
				'value'      => 'bordered',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css_border',
				'selector' => '.mobile-toggle-open-container .menu-toggle-style-bordered',
				'pattern'  => '$',
				'property' => 'border',
				'key'      => 'border',
			),
		),
		'input_attrs'  => array(
			'color'      => false,
			'responsive' => false,
		),
	),
	'mobile_trigger_icon_size' => array(
		'control_type' => 'base_range_control',
		'section'      => 'mobile_trigger_design',
		'label'        => esc_html__( 'Icon Size', 'avanam' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.mobile-toggle-open-container .menu-toggle-open .menu-toggle-icon',
				'property' => 'font-size',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => webapp()->default( 'mobile_trigger_icon_size' ),
		'input_attrs'  => array(
			'min'        => array(
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
			),
			'max'        => array(
				'px'  => 100,
				'em'  => 12,
				'rem' => 12,
			),
			'step'       => array(
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
			),
			'units'      => array( 'px', 'em', 'rem' ),
			'responsive' => false,
		),
	),
	'mobile_trigger_color' => array(
		'control_type' => 'base_color_control',
		'section'      => 'mobile_trigger_design',
		'label'        => esc_html__( 'Trigger Colors', 'avanam' ),
		'default'      => webapp()->default( 'mobile_trigger_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.mobile-toggle-open-container .menu-toggle-open',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '.mobile-toggle-open-container .menu-toggle-open:hover',
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
	'mobile_trigger_background' => array(
		'control_type' => 'base_color_control',
		'section'      => 'mobile_trigger_design',
		'label'        => esc_html__( 'Trigger Background', 'avanam' ),
		'default'      => webapp()->default( 'mobile_trigger_background' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.mobile-toggle-open-container .menu-toggle-open',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '.mobile-toggle-open-container .menu-toggle-open:hover',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'hover',
			),
		),
		'input_attrs'  => array(
			'colors' => array(
				'color' => array(
					'tooltip' => __( 'Initial Background', 'avanam' ),
					'palette' => true,
				),
				'hover' => array(
					'tooltip' => __( 'Hover Background', 'avanam' ),
					'palette' => true,
				),
			),
		),
	),
	'mobile_trigger_typography' => array(
		'control_type' => 'base_typography_control',
		'section'      => 'mobile_trigger_design',
		'label'        => esc_html__( 'Trigger Font', 'avanam' ),
		'context'      => array(
			array(
				'setting'  => 'mobile_trigger_label',
				'operator' => '!empty',
				'value'    => '',
			),
		),
		'default'      => webapp()->default( 'mobile_trigger_typography' ),
		'live_method'     => array(
			array(
				'type'     => 'css_typography',
				'selector' => '.mobile-toggle-open-container .menu-toggle-open',
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
			'id'      => 'mobile_trigger_typography',
			'options' => 'no-color',
		),
	),
	'mobile_trigger_padding' => array(
		'control_type' => 'base_measure_control',
		'section'      => 'mobile_trigger_design',
		'priority'     => 10,
		'default'      => webapp()->default( 'mobile_trigger_padding' ),
		'label'        => esc_html__( 'Trigger Padding', 'avanam' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.mobile-toggle-open-container .menu-toggle-open',
				'property' => 'padding',
				'pattern'  => '$',
				'key'      => 'measure',
			),
		),
		'input_attrs'  => array(
			'responsive' => false,
		),
	),
	'info_link_drawer_container' => array(
		'control_type' => 'base_title_control',
		'section'      => 'mobile_trigger',
		'priority'     => 20,
		'label'        => esc_html__( 'Drawer Container Options', 'avanam' ),
		'settings'     => false,
	),
	'mobile_trigger_drawer_link' => array(
		'control_type' => 'base_focus_button_control',
		'section'      => 'mobile_trigger',
		'settings'     => false,
		'priority'     => 20,
		'label'        => esc_html__( 'Drawer Container Options', 'avanam' ),
		'input_attrs'  => array(
			'section' => 'base_customizer_header_popup',
		),
	),
);

Theme_Customizer::add_settings( $settings );

