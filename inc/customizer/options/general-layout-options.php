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
	'content_width' => array(
		'control_type' => 'base_range_control',
		'section'      => 'general_layout',
		'priority'     => 20,
		'label'        => esc_html__( 'Content Max Width', 'avanam' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.site-container, .site-header-row-layout-contained',
				'property' => 'max-width',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => webapp()->default( 'content_width' ),
		'input_attrs'  => array(
			'min'        => array(
				'px'  => 400,
				'em'  => 30,
				'rem' => 30,
			),
			'max'        => array(
				'px'  => 2000,
				'em'  => 140,
				'rem' => 140,
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
	'content_edge_spacing' => array(
		'control_type' => 'base_range_control',
		'section'      => 'general_layout',
		'priority'     => 20,
		'label'        => esc_html__( 'Content Left/Right Edge Spacing', 'avanam' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.site-container, .site-header-row-layout-contained, .site-footer-row-layout-contained, .entry-hero-layout-contained, .alignfull>.wp-block-cover__inner-container, .alignwide>.wp-block-cover__inner-container',
				'property' => 'padding-left',
				'pattern'  => '$',
				'key'      => 'size',
			),
			array(
				'type'     => 'css',
				'selector' => '.site-container, .site-header-row-layout-contained, .site-footer-row-layout-contained, .entry-hero-layout-contained, .alignfull>.wp-block-cover__inner-container, .alignwide>.wp-block-cover__inner-container',
				'property' => 'padding-right',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => webapp()->default( 'content_edge_spacing' ),
		'input_attrs'  => array(
			'min'        => array(
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
				'vw'  => 0,
			),
			'max'        => array(
				'px'  => 200,
				'em'  => 12,
				'rem' => 12,
				'vw'  => 40,
			),
			'step'       => array(
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
				'vw'  => 1,
			),
			'units'      => array( 'px', 'em', 'rem', 'vh' ),
			'responsive' => true,
		),
	),
	'content_spacing' => array(
		'control_type' => 'base_range_control',
		'section'      => 'general_layout',
		'priority'     => 20,
		'label'        => esc_html__( 'Content Top and Bottom Spacing', 'avanam' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.content-area',
				'property' => 'margin-top',
				'pattern'  => '$',
				'key'      => 'size',
			),
			array(
				'type'     => 'css',
				'selector' => '.content-area',
				'property' => 'margin-bottom',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => webapp()->default( 'content_spacing' ),
		'input_attrs'  => array(
			'min'        => array(
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
				'vw'  => 0,
			),
			'max'        => array(
				'px'  => 200,
				'em'  => 12,
				'rem' => 12,
				'vw'  => 40,
			),
			'step'       => array(
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
				'vw'  => 1,
			),
			'units'      => array( 'px', 'em', 'rem', 'vh' ),
			'responsive' => true,
		),
	),
	'content_narrow_width' => array(
		'control_type' => 'base_range_control',
		'section'      => 'general_layout',
		'priority'     => 20,
		'label'        => esc_html__( 'Narrow Layout Content Max Width', 'avanam' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.content-width-narrow .content-container.site-container',
				'property' => 'max-width',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => webapp()->default( 'content_narrow_width' ),
		'input_attrs'  => array(
			'min'        => array(
				'px'  => 300,
				'em'  => 20,
				'rem' => 20,
				'vw'  => 20,
			),
			'max'        => array(
				'px'  => 2000,
				'em'  => 140,
				'rem' => 140,
				'vw'  => 100,
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
	'info_general_single_boxed' => array(
		'control_type' => 'base_title_control',
		'section'      => 'general_layout',
		'priority'     => 21,
		'label'        => esc_html__( 'Single Post Boxed', 'avanam' ),
		'settings'     => false,
	),
	'boxed_spacing' => array(
		'control_type' => 'base_range_control',
		'section'      => 'general_layout',
		'priority'     => 22,
		'label'        => esc_html__( 'Single Post Boxed Spacing', 'avanam' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.entry-content-wrap',
				'property' => 'padding',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => webapp()->default( 'boxed_spacing' ),
		'input_attrs'  => array(
			'min'        => array(
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
			),
			'max'        => array(
				'px'  => 200,
				'em'  => 12,
				'rem' => 12,
			),
			'step'       => array(
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
			),
			'units'      => array( 'px', 'em', 'rem' ),
			'responsive' => true,
		),
	),
	'boxed_shadow' => array(
		'control_type' => 'base_shadow_control',
		'section'      => 'general_layout',
		'priority'     => 22,
		'label'        => esc_html__( 'Single Post Boxed Shadow', 'avanam' ),
		'live_method'     => array(
			array(
				'type'     => 'css_boxshadow',
				'selector' => '.entry.single-entry',
				'property' => 'box-shadow',
				'pattern'  => '$',
				'key'      => '',
			),
		),
		'default'      => webapp()->default( 'boxed_shadow' ),
	),
	'boxed_border_radius' => array(
		'control_type' => 'base_measure_control',
		'section'      => 'general_layout',
		'priority'     => 22,
		'label'        => esc_html__( 'Single Post Boxed Border Radius', 'avanam' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.entry.single-entry',
				'property' => 'border-radius',
				'pattern'  => '$',
				'key'      => 'measure',
			),
		),
		'default'      => webapp()->default( 'boxed_border_radius' ),
		'input_attrs'  => array(
			'responsive' => false,
		),
	),
	'info_general_archive_boxed' => array(
		'control_type' => 'base_title_control',
		'section'      => 'general_layout',
		'priority'     => 22,
		'label'        => esc_html__( 'Archive Grid Boxed', 'avanam' ),
		'settings'     => false,
	),
	'boxed_grid_spacing' => array(
		'control_type' => 'base_range_control',
		'section'      => 'general_layout',
		'priority'     => 22,
		'label'        => esc_html__( 'Archive Grid Boxed Spacing', 'avanam' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.loop-entry .entry-content-wrap',
				'property' => 'padding',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => webapp()->default( 'boxed_grid_spacing' ),
		'input_attrs'  => array(
			'min'        => array(
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
			),
			'max'        => array(
				'px'  => 200,
				'em'  => 12,
				'rem' => 12,
			),
			'step'       => array(
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
			),
			'units'      => array( 'px', 'em', 'rem' ),
			'responsive' => true,
		),
	),
	'boxed_grid_shadow' => array(
		'control_type' => 'base_shadow_control',
		'section'      => 'general_layout',
		'priority'     => 22,
		'label'        => esc_html__( 'Archive Grid Boxed Shadow', 'avanam' ),
		'live_method'     => array(
			array(
				'type'     => 'css_boxshadow',
				'selector' => '.entry.loop-entry',
				'property' => 'box-shadow',
				'pattern'  => '$',
				'key'      => '',
			),
		),
		'default'      => webapp()->default( 'boxed_grid_shadow' ),
	),
	'boxed_grid_border_radius' => array(
		'control_type' => 'base_measure_control',
		'section'      => 'general_layout',
		'priority'     => 22,
		'label'        => esc_html__( 'Archive Grid Boxed Border Radius', 'avanam' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.entry.loop-entry',
				'property' => 'border-radius',
				'pattern'  => '$',
				'key'      => 'measure',
			),
			array(
				'type'     => 'css',
				'selector' => '.entry.loop-entry:after',
				'property' => 'border-radius',
				'pattern'  => '$',
				'key'      => 'measure',
			),
		),
		'default'      => webapp()->default( 'boxed_grid_border_radius' ),
		'input_attrs'  => array(
			'responsive' => false,
		),
	),
);

Theme_Customizer::add_settings( $settings );

