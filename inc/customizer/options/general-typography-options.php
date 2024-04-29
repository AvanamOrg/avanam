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
		// 'load_font_pairing' => array(
		// 	'control_type' => 'base_font_pairing',
		// 	'section'      => 'general_typography',
		// 	'label'        => esc_html__( 'Font Pairings', 'avanam' ),
		// 	'settings'     => false,
		// ),
		'base_font' => array(
			'control_type' => 'base_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'Base Font', 'avanam' ),
			'default'      => webapp()->default( 'base_font' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => 'body',
					'property' => 'font',
					'key'      => 'typography',
				),
				array(
					'type'     => 'css',
					'property' => '--global-body-font-family',
					'selector' => 'body',
					'pattern'  => '$',
					'key'      => 'family',
				),
			),
			'input_attrs'  => array(
				'id'         => 'base_font',
				'canInherit' => false,
			),
		),
		'load_base_italic' => array(
			'control_type' => 'base_switch_control',
			'sanitize'     => 'base_sanitize_toggle',
			'section'      => 'general_typography',
			'default'      => webapp()->default( 'load_base_italic' ),
			'label'        => esc_html__( 'Load Italics Font Styles', 'avanam' ),
			'context'      => array(
				array(
					'setting' => 'base_font',
					'operator'   => 'load_italic',
					'value'   => 'true',
				),
			),
		),
		'info_heading' => array(
			'control_type' => 'base_title_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'Headings', 'avanam' ),
			'settings'     => false,
		),
		'heading_font' => array(
			'control_type' => 'base_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'Heading Font Family', 'avanam' ),
			'default'      => webapp()->default( 'heading_font' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => 'h1,h2,h3,h4,h5,h6',
					'property' => 'font',
					'key'      => 'family',
				),
				array(
					'type'     => 'css',
					'property' => '--global-heading-font-family',
					'selector' => 'body',
					'pattern'  => '$',
					'key'      => 'family',
				),
			),
			'input_attrs'  => array(
				'id'      => 'heading_font',
				'options' => 'family',
			),
		),
		'h1_font' => array(
			'control_type' => 'base_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'H1 Font', 'avanam' ),
			'default'      => webapp()->default( 'h1_font' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => 'h1',
					'property' => 'font',
					'key'      => 'typography',
				),
			),
			'input_attrs'  => array(
				'id'             => 'h1_font',
				'headingInherit' => true,
			),
		),
		'h2_font' => array(
			'control_type' => 'base_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'H2 Font', 'avanam' ),
			'default'      => webapp()->default( 'h2_font' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => 'h2',
					'property' => 'font',
					'key'      => 'typography',
				),
			),
			'input_attrs'  => array(
				'id'             => 'h2_font',
				'headingInherit' => true,
			),
		),
		'h3_font' => array(
			'control_type' => 'base_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'H3 Font', 'avanam' ),
			'default'      => webapp()->default( 'h3_font' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => 'h3',
					'property' => 'font',
					'key'      => 'typography',
				),
			),
			'input_attrs'  => array(
				'id'             => 'h3_font',
				'headingInherit' => true,
			),
		),
		'h4_font' => array(
			'control_type' => 'base_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'H4 Font', 'avanam' ),
			'default'      => webapp()->default( 'h4_font' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => 'h4',
					'property' => 'font',
					'key'      => 'typography',
				),
			),
			'input_attrs'  => array(
				'id'             => 'h4_font',
				'headingInherit' => true,
			),
		),
		'h5_font' => array(
			'control_type' => 'base_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'H5 Font', 'avanam' ),
			'default'      => webapp()->default( 'h5_font' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => 'h5',
					'property' => 'font',
					'key'      => 'typography',
				),
			),
			'input_attrs'  => array(
				'id'             => 'h5_font',
				'headingInherit' => true,
			),
		),
		'h6_font' => array(
			'control_type' => 'base_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'H6 Font', 'avanam' ),
			'default'      => webapp()->default( 'h6_font' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => 'h6',
					'property' => 'font',
					'key'      => 'typography',
				),
			),
			'input_attrs'  => array(
				'id'             => 'h6_font',
				'headingInherit' => true,
			),
		),
		'info_above_title_heading' => array(
			'control_type' => 'base_title_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'Title Above Content', 'avanam' ),
			'settings'     => false,
		),
		'title_above_font' => array(
			'control_type' => 'base_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'H1 Title', 'avanam' ),
			'default'      => webapp()->default( 'title_above_font' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => '.entry-hero h1',
					'property' => 'font',
					'key'      => 'typography',
				),
			),
			'input_attrs'  => array(
				'id'             => 'title_above_font',
				'headingInherit' => true,
			),
		),
		'title_above_breadcrumb_font' => array(
			'control_type' => 'base_typography_control',
			'section'      => 'general_typography',
			'label'        => esc_html__( 'Breadcrumbs', 'avanam' ),
			'default'      => webapp()->default( 'title_above_breadcrumb_font' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => '.entry-hero .base-breadcrumbs',
					'property' => 'font',
					'key'      => 'typography',
				),
			),
			'input_attrs'  => array(
				'id'      => 'title_above_breadcrumb_font',
			),
		),
		'font_rendering' => array(
			'control_type' => 'base_switch_control',
			'sanitize'     => 'base_sanitize_toggle',
			'section'      => 'general_typography',
			'transport'    => 'refresh',
			'default'      => webapp()->default( 'font_rendering' ),
			'label'        => esc_html__( 'Enable Font Smoothing', 'avanam' ),
		),
		'google_subsets' => array(
			'control_type' => 'base_check_icon_control',
			'section'      => 'general_typography',
			'sanitize'     => 'base_sanitize_google_subsets',
			'priority'     => 20,
			'default'      => array(),
			'label'        => esc_html__( 'Google Font Subsets', 'avanam' ),
			'input_attrs'  => array(
				'options' => array(
					'latin-ext' => array(
						'name' => __( 'Latin Extended', 'avanam' ),
					),
					'cyrillic' => array(
						'name' => __( 'Cyrillic', 'avanam' ),
					),
					'cyrillic-ext' => array(
						'name' => __( 'Cyrillic Extended', 'avanam' ),
					),
					'greek' => array(
						'name' => __( 'Greek', 'avanam' ),
					),
					'greek-ext' => array(
						'name' => __( 'Greek Extended', 'avanam' ),
					),
					'vietnamese' => array(
						'name' => __( 'Vietnamese', 'avanam' ),
					),
					'arabic' => array(
						'name' => __( 'Arabic', 'avanam' ),
					),
					'khmer' => array(
						'name' => __( 'Khmer', 'avanam' ),
					),
					'chinese' => array(
						'name' => __( 'Chinese', 'avanam' ),
					),
					'chinese-simplified' => array(
						'name' => __( 'Chinese Simplified', 'avanam' ),
					),
					'tamil' => array(
						'name' => __( 'Tamil', 'avanam' ),
					),
					'bengali' => array(
						'name' => __( 'Bengali', 'avanam' ),
					),
					'devanagari' => array(
						'name' => __( 'Devanagari', 'avanam' ),
					),
					'hebrew' => array(
						'name' => __( 'Hebrew', 'avanam' ),
					),
					'korean' => array(
						'name' => __( 'Korean', 'avanam' ),
					),
					'thai' => array(
						'name' => __( 'Thai', 'avanam' ),
					),
					'telugu' => array(
						'name' => __( 'Telugu', 'avanam' ),
					),
				),
			),
		),
	)
);
