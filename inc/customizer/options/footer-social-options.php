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
	'footer_social_tabs' => array(
		'control_type' => 'base_tab_control',
		'section'      => 'footer_social',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'avanam' ),
				'target' => 'footer_social',
			),
			'design' => array(
				'label'  => __( 'Design', 'avanam' ),
				'target' => 'footer_social_design',
			),
			'active' => 'general',
		),
	),
	'footer_social_tabs_design' => array(
		'control_type' => 'base_tab_control',
		'section'      => 'footer_social_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'avanam' ),
				'target' => 'footer_social',
			),
			'design' => array(
				'label'  => __( 'Design', 'avanam' ),
				'target' => 'footer_social_design',
			),
			'active' => 'design',
		),
	),
	'footer_social_title' => array(
		'control_type' => 'base_text_control',
		'section'      => 'footer_social',
		'sanitize'     => 'sanitize_text_field',
		'priority'     => 4,
		'label'        => esc_html__( 'Title', 'avanam' ),
		'default'      => webapp()->default( 'footer_social_title' ),
		'partial'      => array(
			'selector'            => '.footer-social-wrap',
			'container_inclusive' => true,
			'render_callback'     => 'Base\footer_social',
		),
	),
	'footer_social_items' => array(
		'control_type' => 'base_social_control',
		'section'      => 'footer_social',
		'priority'     => 6,
		'default'      => webapp()->default( 'footer_social_items' ),
		'label'        => esc_html__( 'Social Items', 'avanam' ),
		'partial'      => array(
			'selector'            => '.footer-social-wrap',
			'container_inclusive' => true,
			'render_callback'     => 'Base\footer_social',
		),
	),
	'footer_social_show_label' => array(
		'control_type' => 'base_switch_control',
		'sanitize'     => 'base_sanitize_toggle',
		'section'      => 'footer_social',
		'priority'     => 8,
		'default'      => webapp()->default( 'footer_social_show_label' ),
		'label'        => esc_html__( 'Show Icon Label?', 'avanam' ),
		'partial'      => array(
			'selector'            => '.footer-social-wrap',
			'container_inclusive' => true,
			'render_callback'     => 'Base\footer_social',
		),
	),
	'footer_social_item_spacing' => array(
		'control_type' => 'base_range_control',
		'section'      => 'footer_social',
		'label'        => esc_html__( 'Item Spacing', 'avanam' ),
		'default'      => webapp()->default( 'footer_social_item_spacing' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#colophon .site-footer-wrap .footer-social-wrap .footer-social-inner-wrap',
				'property' => 'gap',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'input_attrs'  => array(
			'min'        => array(
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
			),
			'max'        => array(
				'px'  => 50,
				'em'  => 3,
				'rem' => 3,
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
	'footer_social_align' => array(
		'control_type' => 'base_radio_icon_control',
		'section'      => 'footer_social',
		'label'        => esc_html__( 'Content Align', 'avanam' ),
		'priority'     => 10,
		'default'      => webapp()->default( 'footer_social_align' ),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.footer-social',
				'pattern'  => array(
					'desktop' => 'content-align-$',
					'tablet'  => 'content-tablet-align-$',
					'mobile'  => 'content-mobile-align-$',
				),
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'left'   => array(
					'tooltip'  => __( 'Left Align', 'avanam' ),
					'dashicon' => 'editor-alignleft',
				),
				'center' => array(
					'tooltip'  => __( 'Center Align', 'avanam' ),
					'dashicon' => 'editor-aligncenter',
				),
				'right'  => array(
					'tooltip'  => __( 'Right Align', 'avanam' ),
					'dashicon' => 'editor-alignright',
				),
			),
			'responsive' => true,
		),
	),
	'footer_social_vertical_align' => array(
		'control_type' => 'base_radio_icon_control',
		'section'      => 'footer_social',
		'label'        => esc_html__( 'Content Vertical Align', 'avanam' ),
		'priority'     => 10,
		'default'      => webapp()->default( 'footer_social_vertical_align' ),
		'live_method'  => array(
			array(
				'type'     => 'class',
				'selector' => '.footer-social',
				'pattern'  => array(
					'desktop' => 'content-valign-$',
					'tablet'  => 'content-tablet-valign-$',
					'mobile'  => 'content-mobile-valign-$',
				),
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'top' => array(
					'tooltip' => __( 'Top Align', 'avanam' ),
					'icon'    => 'aligntop',
				),
				'middle' => array(
					'tooltip' => __( 'Middle Align', 'avanam' ),
					'icon'    => 'alignmiddle',
				),
				'bottom' => array(
					'tooltip' => __( 'Bottom Align', 'avanam' ),
					'icon'    => 'alignbottom',
				),
			),
			'responsive' => true,
		),
	),
	'footer_social_style' => array(
		'control_type' => 'base_radio_icon_control',
		'section'      => 'footer_social_design',
		'priority'     => 10,
		'default'      => webapp()->default( 'footer_social_style' ),
		'label'        => esc_html__( 'Social Style', 'avanam' ),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.footer-social-inner-wrap',
				'pattern'  => 'social-style-$',
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'filled' => array(
					'name' => __( 'Filled', 'avanam' ),
				),
				'outline' => array(
					'name' => __( 'Outline', 'avanam' ),
				),
			),
			'responsive' => false,
		),
	),
	'footer_social_icon_size' => array(
		'control_type' => 'base_range_control',
		'section'      => 'footer_social_design',
		'label'        => esc_html__( 'Icon Size', 'avanam' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.footer-social-wrap .footer-social-inner-wrap',
				'property' => 'font-size',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => webapp()->default( 'footer_social_icon_size' ),
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
	'footer_social_brand' => array(
		'control_type' => 'base_select_control',
		'section'      => 'footer_social_design',
		'transport'    => 'refresh',
		'default'      => webapp()->default( 'footer_social_brand' ),
		'label'        => esc_html__( 'Use Brand Colors?', 'avanam' ),
		'input_attrs'  => array(
			'options' => array(
				'' => array(
					'name' => __( 'No', 'avanam' ),
				),
				'always' => array(
					'name' => __( 'Yes', 'avanam' ),
				),
				'onhover' => array(
					'name' => __( 'On Hover', 'avanam' ),
				),
				'untilhover' => array(
					'name' => __( 'Until Hover', 'avanam' ),
				),
			),
		),
	),
	'footer_social_color' => array(
		'control_type' => 'base_color_control',
		'section'      => 'footer_social_design',
		'label'        => esc_html__( 'Colors', 'avanam' ),
		'default'      => webapp()->default( 'footer_social_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.site-footer .site-footer-wrap .site-footer-section .footer-social-wrap .footer-social-inner-wrap .social-button',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '.site-footer .site-footer-wrap .site-footer-section .footer-social-wrap .footer-social-inner-wrap .social-button:hover',
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
	'footer_social_background' => array(
		'control_type' => 'base_color_control',
		'section'      => 'footer_social_design',
		'label'        => esc_html__( 'Background Colors', 'avanam' ),
		'default'      => webapp()->default( 'footer_social_background' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.site-footer .site-footer-wrap .site-footer-section .footer-social-wrap .footer-social-inner-wrap .social-button',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '.site-footer .site-footer-wrap .site-footer-section .footer-social-wrap .footer-social-inner-wrap .social-button:hover',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'hover',
			),
		),
		'context'      => array(
			array(
				'setting'  => 'footer_social_style',
				'operator' => '=',
				'value'    => 'filled',
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
	'footer_social_border_colors' => array(
		'control_type' => 'base_color_control',
		'section'      => 'footer_social_design',
		'label'        => esc_html__( 'Border Colors', 'avanam' ),
		'default'      => webapp()->default( 'footer_social_border' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.site-footer .site-footer-wrap .site-footer-section .footer-social-wrap .footer-social-inner-wrap .social-button',
				'property' => 'border-color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '.site-footer .site-footer-wrap .site-footer-section .footer-social-wrap .footer-social-inner-wrap .social-button:hover',
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
	'footer_social_border' => array(
		'control_type' => 'base_border_control',
		'section'      => 'footer_social_design',
		'label'        => esc_html__( 'Border', 'avanam' ),
		'default'      => webapp()->default( 'footer_social_border' ),
		'live_method'     => array(
			array(
				'type'     => 'css_border',
				'selector' => '.site-footer .site-footer-wrap .site-footer-section .footer-social-wrap .footer-social-inner-wrap .social-button',
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
	'footer_social_border_radius' => array(
		'control_type' => 'base_range_control',
		'section'      => 'footer_social_design',
		'label'        => esc_html__( 'Border Radius', 'avanam' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.site-footer .site-footer-wrap .site-footer-section .footer-social-wrap .footer-social-inner-wrap .social-button',
				'property' => 'border-radius',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => webapp()->default( 'footer_social_border_radius' ),
		'input_attrs'  => array(
			'min'        => array(
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
				'%'   => 0,
			),
			'max'        => array(
				'px'  => 100,
				'em'  => 12,
				'rem' => 12,
				'%'   => 100,
			),
			'step'       => array(
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
				'%'   => 1,
			),
			'units'      => array( 'px', 'em', 'rem', '%' ),
			'responsive' => false,
		),
	),
	'footer_social_typography' => array(
		'control_type' => 'base_typography_control',
		'section'      => 'footer_social_design',
		'label'        => esc_html__( 'Font', 'avanam' ),
		'context'      => array(
			array(
				'setting'  => 'footer_social_show_label',
				'operator' => '=',
				'value'    => true,
			),
		),
		'default'      => webapp()->default( 'footer_social_typography' ),
		'live_method'     => array(
			array(
				'type'     => 'css_typography',
				'selector' => '.footer-social-wrap a.social-button .social-label',
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
			'id' => 'footer_social_typography',
			'options' => 'no-color',
		),
	),
	'footer_social_margin' => array(
		'control_type' => 'base_measure_control',
		'section'      => 'footer_social_design',
		'priority'     => 10,
		'default'      => webapp()->default( 'footer_social_margin' ),
		'label'        => esc_html__( 'Margin', 'avanam' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#colophon .footer-social-wrap',
				'property' => 'margin',
				'pattern'  => '$',
				'key'      => 'measure',
			),
		),
		'input_attrs'  => array(
			'responsive' => false,
		),
	),
	'footer_social_link_to_social_links' => array(
		'control_type' => 'base_focus_button_control',
		'section'      => 'footer_social',
		'settings'     => false,
		'priority'     => 25,
		'label'        => esc_html__( 'Set Social Links', 'avanam' ),
		'input_attrs'  => array(
			'section' => 'base_customizer_general_social',
		),
	),
);

Theme_Customizer::add_settings( $settings );

