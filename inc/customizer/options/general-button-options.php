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
		'buttons_color' => array(
			'control_type' => 'base_color_control',
			'section'      => 'general_buttons',
			'label'        => esc_html__( 'Text Colors', 'avanam' ),
			'default'      => webapp()->default( 'buttons_color' ),
			'live_method'     => array(
				array(
					'type'     => 'global',
					'selector' => '--global-palette-btn',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				),
				array(
					'type'     => 'global',
					'selector' => '--global-palette-btn-hover',
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
		'buttons_background' => array(
			'control_type' => 'base_color_control',
			'section'      => 'general_buttons',
			'label'        => esc_html__( 'Background Colors', 'avanam' ),
			'default'      => webapp()->default( 'buttons_background' ),
			'live_method'     => array(
				array(
					'type'     => 'global',
					'selector' => '--global-palette-btn-bg',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'color',
				),
				array(
					'type'     => 'global',
					'selector' => '--global-palette-btn-bg-hover',
					'property' => 'background',
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
				'allowGradient' => true,
			),
		),
		'buttons_border_colors' => array(
			'control_type' => 'base_color_control',
			'section'      => 'general_buttons',
			'label'        => esc_html__( 'Border Colors', 'avanam' ),
			'default'      => webapp()->default( 'buttons_border' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => 'button, .button, .wp-block-button__link, input[type="button"], input[type="reset"], input[type="submit"]',
					'property' => 'border-color',
					'pattern'  => '$',
					'key'      => 'color',
				),
				array(
					'type'     => 'css',
					'selector' => 'button:hover, .button:hover, .wp-block-button__link:hover, input[type="button"]:hover, input[type="reset"]:hover, input[type="submit"]:hover',
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
		'buttons_border' => array(
			'control_type' => 'base_border_control',
			'section'      => 'general_buttons',
			'label'        => esc_html__( 'Border', 'avanam' ),
			'default'      => webapp()->default( 'buttons_border' ),
			'live_method'     => array(
				array(
					'type'     => 'css_border',
					'selector' => 'button, .button, .wp-block-button__link, input[type="button"], input[type="reset"], input[type="submit"]',
					'property' => 'border',
					'pattern'  => '$',
					'key'      => 'border',
				),
			),
			'input_attrs'  => array(
				'responsive' => true,
				'color'      => false,
			),
		),
		'buttons_border_radius' => array(
			'control_type' => 'base_range_control',
			'section'      => 'general_buttons',
			'label'        => esc_html__( 'Border Radius', 'avanam' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => 'button, .button, .wp-block-button__link, input[type="button"], input[type="reset"], input[type="submit"]',
					'property' => 'border-radius',
					'pattern'  => '$',
					'key'      => 'size',
				),
			),
			'default'      => webapp()->default( 'buttons_border_radius' ),
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
				'responsive' => true,
			),
		),
		'buttons_typography' => array(
			'control_type' => 'base_typography_control',
			'section'      => 'general_buttons',
			'label'        => esc_html__( 'Font', 'avanam' ),
			'default'      => webapp()->default( 'buttons_typography' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => 'button, .button, .wp-block-button__link, input[type="button"], input[type="reset"], input[type="submit"]',
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
				'id' => 'buttons_typography',
				'options' => 'no-color',
			),
		),
		'buttons_padding' => array(
			'control_type' => 'base_measure_control',
			'section'      => 'general_buttons',
			'priority'     => 10,
			'default'      => webapp()->default( 'buttons_margin' ),
			'label'        => esc_html__( 'Padding', 'avanam' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => 'button, .button, .wp-block-button__link, input[type="button"], input[type="reset"], input[type="submit"]',
					'property' => 'padding',
					'pattern'  => '$',
					'key'      => 'measure',
				),
			),
			'input_attrs'  => array(
				'responsive' => true,
			),
		),
		'buttons_shadow' => array(
			'control_type' => 'base_shadow_control',
			'section'      => 'general_buttons',
			'priority'     => 20,
			'label'        => esc_html__( 'Button Shadow', 'avanam' ),
			'live_method'     => array(
				array(
					'type'     => 'css_boxshadow',
					'selector' => 'button, .button, .wp-block-button__link, input[type="button"], input[type="reset"], input[type="submit"]',
					'property' => 'box-shadow',
					'pattern'  => '$',
					'key'      => '',
				),
			),
			'default'      => webapp()->default( 'buttons_shadow' ),
		),
		'buttons_shadow_hover' => array(
			'control_type' => 'base_shadow_control',
			'section'      => 'general_buttons',
			'priority'     => 20,
			'label'        => esc_html__( 'Button Hover State Shadow', 'avanam' ),
			'live_method'     => array(
				array(
					'type'     => 'css_boxshadow',
					'selector' => 'button:hover, .button:hover, .wp-block-button__link:hover, input[type="button"]:hover, input[type="reset"]:hover, input[type="submit"]:hover',
					'property' => 'box-shadow',
					'pattern'  => '$',
					'key'      => '',
				),
			),
			'default'      => webapp()->default( 'buttons_shadow_hover' ),
		),
	)
);
