<?php
/**
 * Header Builder Options
 *
 * @package Base
 */

namespace Base;

use Base\Theme_Customizer;
use function Base\webapp;

Theme_Customizer::add_settings(
	array(
		'dropdown_navigation_tabs' => array(
			'control_type' => 'base_tab_control',
			'section'      => 'dropdown_navigation',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => array(
				'general' => array(
					'label'  => __( 'General', 'avanam' ),
					'target' => 'dropdown_navigation',
				),
				'design' => array(
					'label'  => __( 'Design', 'avanam' ),
					'target' => 'dropdown_navigation_design',
				),
				'active' => 'general',
			),
		),
		'dropdown_navigation_tabs_design' => array(
			'control_type' => 'base_tab_control',
			'section'      => 'dropdown_navigation_design',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => array(
				'general' => array(
					'label'  => __( 'General', 'avanam' ),
					'target' => 'dropdown_navigation',
				),
				'design' => array(
					'label'  => __( 'Design', 'avanam' ),
					'target' => 'dropdown_navigation_design',
				),
				'active' => 'design',
			),
		),
		'dropdown_navigation_color' => array(
			'control_type' => 'base_color_control',
			'section'      => 'dropdown_navigation_design',
			'label'        => esc_html__( 'Dropdown Colors', 'avanam' ),
			'default'      => webapp()->default( 'dropdown_navigation_color' ),
			'live_method'  => array(
				array(
					'type'     => 'css',
					'selector' => '.header-navigation .header-menu-container ul ul li.menu-item > a',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				),
				array(
					'type'     => 'css',
					'selector' => '.header-navigation .header-menu-container ul ul li.menu-item > a:hover',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'hover',
				),
				array(
					'type'     => 'css',
					'selector' => '.header-navigation .header-menu-container ul ul > li.menu-item.current-menu-item > a, .header-navigation .header-menu-container ul ul > li.menu-item.current_page_item > a',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'active',
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
					'active' => array(
						'tooltip' => __( 'Active Color', 'avanam' ),
						'palette' => true,
					),
				),
			),
		),
		'dropdown_navigation_background' => array(
			'control_type' => 'base_color_control',
			'section'      => 'dropdown_navigation_design',
			'priority'     => 20,
			'label'        => esc_html__( 'Dropdown Background', 'avanam' ),
			'default'      => webapp()->default( 'dropdown_navigation_background' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.header-navigation .header-menu-container ul ul li.menu-item > a',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'color',
				),
				array(
					'type'     => 'css',
					'selector' => '.header-navigation .header-menu-container ul ul li.menu-item > a:hover',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'hover',
				),
				array(
					'type'     => 'css',
					'selector' => '.header-navigation .header-menu-container ul ul li.menu-item.current-menu-item > a, .header-navigation .header-menu-container ul ul li.menu-item.current_page_item > a',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'active',
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
					'active' => array(
						'tooltip' => __( 'Active Background', 'avanam' ),
						'palette' => true,
					),
				),
			),
		),
		'dropdown_navigation_divider' => array(
			'control_type' => 'base_border_control',
			'section'      => 'dropdown_navigation_design',
			'priority'     => 20,
			'label'        => esc_html__( 'Item Divider', 'avanam' ),
			'default'      => webapp()->default( 'dropdown_navigation_divider' ),
			'live_method'     => array(
				array(
					'type'     => 'css_border',
					'selector' => '.header-navigation ul ul li.menu-item',
					'pattern'  => '$',
					'property' => 'border-bottom',
					'pattern'  => '$',
					'key'      => 'border',
				),
			),
			'input_attrs'  => array(
				'responsive' => false,
			),
		),
		'dropdown_navigation_typography' => array(
			'control_type' => 'base_typography_control',
			'section'      => 'dropdown_navigation_design',
			'priority'     => 20,
			'label'        => esc_html__( 'Dropdown Font', 'avanam' ),
			'default'      => webapp()->default( 'dropdown_navigation_typography' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => '.header-navigation .header-menu-container ul ul li.menu-item > a',
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
				'id'      => 'dropdown_navigation_typography',
				'options' => 'no-color',
			),
		),
		'dropdown_navigation_shadow' => array(
			'control_type' => 'base_shadow_control',
			'section'      => 'dropdown_navigation_design',
			'priority'     => 20,
			'label'        => esc_html__( 'Dropdown Shadow', 'avanam' ),
			'live_method'     => array(
				array(
					'type'     => 'css_boxshadow',
					'selector' => '.header-navigation .header-menu-container ul ul.submenu',
					'property' => 'box-shadow',
					'pattern'  => '$',
					'key'      => '',
				),
			),
			'default'      => webapp()->default( 'dropdown_navigation_shadow' ),
		),
		'dropdown_navigation_reveal' => array(
			'control_type' => 'base_radio_icon_control',
			'section'      => 'dropdown_navigation',
			'priority'     => 20,
			'default'      => webapp()->default( 'dropdown_navigation_reveal' ),
			'label'        => esc_html__( 'Dropdown Reveal', 'avanam' ),
			'live_method'     => array(
				array(
					'type'     => 'class',
					'selector' => '.header-navigation',
					'pattern'  => 'header-navigation-dropdown-animation-$',
					'key'      => '',
				),
			),
			'input_attrs'  => array(
				'layout' => array(
					'none' => array(
						'name'    => __( 'None', 'avanam' ),
					),
					'fade' => array(
						'name'    => __( 'Fade', 'avanam' ),
					),
					'fade-up' => array(
						'name'    => __( 'Fade Up', 'avanam' ),
					),
					'fade-down' => array(
						'name'    => __( 'Fade Down', 'avanam' ),
					),
				),
				'responsive' => false,
			),
		),
		'dropdown_navigation_width' => array(
			'control_type' => 'base_range_control',
			'section'      => 'dropdown_navigation',
			'priority'     => 20,
			'label'        => esc_html__( 'Dropdown Width', 'avanam' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.wp-site-blocks .header-navigation .header-menu-container ul ul li.menu-item > a',
					'property' => 'width',
					'pattern'  => '$',
					'key'      => 'size',
				),
			),
			'default'      => webapp()->default( 'dropdown_navigation_width' ),
			'input_attrs'  => array(
				'min'        => array(
					'px'  => 0,
					'em'  => 0,
					'rem' => 0,
					'vw'  => 0,
				),
				'max'        => array(
					'px'  => 600,
					'em'  => 50,
					'rem' => 50,
					'vw'  => 50,
				),
				'step'       => array(
					'px'  => 1,
					'em'  => 0.01,
					'rem' => 0.01,
					'vw'  => 1,
				),
				'units'      => array( 'px', 'em', 'rem', 'vw' ),
				'responsive' => false,
			),
		),
		'dropdown_navigation_vertical_spacing' => array(
			'control_type' => 'base_range_control',
			'section'      => 'dropdown_navigation',
			'priority'     => 20,
			'label'        => esc_html__( 'Dropdown Items Vertical Spacing', 'avanam' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.header-navigation .header-menu-container ul ul li.menu-item > a',
					'property' => 'padding-top',
					'pattern'  => '$',
					'key'      => 'size',
				),
				array(
					'type'     => 'css',
					'selector' => '.header-navigation .header-menu-container ul ul li.menu-item > a',
					'property' => 'padding-bottom',
					'pattern'  => '$',
					'key'      => 'size',
				),
			),
			'default'      => webapp()->default( 'dropdown_navigation_vertical_spacing' ),
			'input_attrs'  => array(
				'min'        => array(
					'px'  => 0,
					'em'  => 0,
					'rem' => 0,
					'vh'  => 0,
				),
				'max'        => array(
					'px'  => 100,
					'em'  => 12,
					'rem' => 12,
					'vh'  => 12,
				),
				'step'       => array(
					'px'  => 1,
					'em'  => 0.01,
					'rem' => 0.01,
					'vh'  => 0.01,
				),
				'units'      => array( 'px', 'em', 'rem', 'vh' ),
				'responsive' => false,
			),
		),
	)
);
