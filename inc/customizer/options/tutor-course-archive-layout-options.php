<?php
/**
 * Tutor Course Layout
 *
 * @package Base
 */

namespace Base;

use Base\Theme_Customizer;
use function Base\webapp;

$settings = array(
	'courses_archive_layout_tabs' => array(
		'control_type' => 'base_tab_control',
		'section'      => 'courses_archive_layout',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'avanam' ),
				'target' => 'courses_archive_layout',
			),
			'design' => array(
				'label'  => __( 'Design', 'avanam' ),
				'target' => 'courses_archive_layout_design',
			),
			'active' => 'general',
		),
	),
	'courses_archive_layout_tabs_design' => array(
		'control_type' => 'base_tab_control',
		'section'      => 'courses_archive_layout_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'avanam' ),
				'target' => 'courses_archive_layout',
			),
			'design' => array(
				'label'  => __( 'Design', 'avanam' ),
				'target' => 'courses_archive_layout_design',
			),
			'active' => 'design',
		),
	),
	'info_courses_archive_title' => array(
		'control_type' => 'base_title_control',
		'section'      => 'courses_archive_layout',
		'priority'     => 2,
		'label'        => esc_html__( 'Archive Title', 'avanam' ),
		'settings'     => false,
	),
	'info_courses_archive_title_design' => array(
		'control_type' => 'base_title_control',
		'section'      => 'courses_archive_layout_design',
		'priority'     => 2,
		'label'        => esc_html__( 'Archive Title', 'avanam' ),
		'settings'     => false,
	),
	'courses_archive_title' => array(
		'control_type' => 'base_switch_control',
		'sanitize'     => 'base_sanitize_toggle',
		'section'      => 'courses_archive_layout',
		'priority'     => 3,
		'default'      => webapp()->default( 'courses_archive_title' ),
		'label'        => esc_html__( 'Show Archive Title?', 'avanam' ),
		'transport'    => 'refresh',
	),
	'courses_archive_title_layout' => array(
		'control_type' => 'base_radio_icon_control',
		'section'      => 'courses_archive_layout',
		'label'        => esc_html__( 'Archive Title Layout', 'avanam' ),
		'transport'    => 'refresh',
		'priority'     => 4,
		'default'      => webapp()->default( 'courses_archive_title_layout' ),
		'context'      => array(
			array(
				'setting'    => 'courses_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'normal' => array(
					'tooltip' => __( 'In Content', 'avanam' ),
					'icon'    => 'incontent',
				),
				'above' => array(
					'tooltip' => __( 'Above Content', 'avanam' ),
					'icon'    => 'abovecontent',
				),
			),
			'responsive' => false,
			'class'      => 'base-two-col',
		),
	),
	'courses_archive_title_inner_layout' => array(
		'control_type' => 'base_radio_icon_control',
		'section'      => 'courses_archive_layout',
		'priority'     => 4,
		'default'      => webapp()->default( 'courses_archive_title_inner_layout' ),
		'label'        => esc_html__( 'Container Width', 'avanam' ),
		'context'      => array(
			array(
				'setting'    => 'courses_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'courses_archive_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.course-archive-hero-section',
				'pattern'  => 'entry-hero-layout-$',
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'standard' => array(
					'tooltip' => __( 'Background Fullwidth, Content Contained', 'avanam' ),
					'name'    => __( 'Standard', 'avanam' ),
					'icon'    => '',
				),
				'fullwidth' => array(
					'tooltip' => __( 'Background & Content Fullwidth', 'avanam' ),
					'name'    => __( 'Fullwidth', 'avanam' ),
					'icon'    => '',
				),
				'contained' => array(
					'tooltip' => __( 'Background & Content Contained', 'avanam' ),
					'name'    => __( 'Contained', 'avanam' ),
					'icon'    => '',
				),
			),
			'responsive' => false,
		),
	),
	'courses_archive_title_align' => array(
		'control_type' => 'base_radio_icon_control',
		'section'      => 'courses_archive_layout',
		'label'        => esc_html__( 'Course Title Align', 'avanam' ),
		'priority'     => 4,
		'default'      => webapp()->default( 'courses_archive_title_align' ),
		'context'      => array(
			array(
				'setting'    => 'courses_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.course-archive-title',
				'pattern'  => array(
					'desktop' => 'title-align-$',
					'tablet'  => 'title-tablet-align-$',
					'mobile'  => 'title-mobile-align-$',
				),
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'left' => array(
					'tooltip'  => __( 'Left Align Title', 'avanam' ),
					'dashicon' => 'editor-alignleft',
				),
				'center' => array(
					'tooltip'  => __( 'Center Align Title', 'avanam' ),
					'dashicon' => 'editor-aligncenter',
				),
				'right' => array(
					'tooltip'  => __( 'Right Align Title', 'avanam' ),
					'dashicon' => 'editor-alignright',
				),
			),
			'responsive' => true,
		),
	),
	'courses_archive_title_height' => array(
		'control_type' => 'base_range_control',
		'section'      => 'courses_archive_layout',
		'priority'     => 5,
		'label'        => esc_html__( 'Container Min Height', 'avanam' ),
		'context'      => array(
			array(
				'setting'    => 'courses_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'courses_archive_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#inner-wrap .course-archive-hero-section .entry-header',
				'property' => 'min-height',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => webapp()->default( 'courses_archive_title_height' ),
		'input_attrs'  => array(
			'min'     => array(
				'px'  => 10,
				'em'  => 1,
				'rem' => 1,
				'vh'  => 2,
			),
			'max'     => array(
				'px'  => 800,
				'em'  => 12,
				'rem' => 12,
				'vh'  => 100,
			),
			'step'    => array(
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
				'vh'  => 1,
			),
			'units'   => array( 'px', 'em', 'rem', 'vh' ),
		),
	),
	'courses_archive_title_elements' => array(
		'control_type' => 'base_sorter_control',
		'section'      => 'courses_archive_layout',
		'priority'     => 6,
		'default'      => webapp()->default( 'courses_archive_title_elements' ),
		'label'        => esc_html__( 'Title Elements', 'avanam' ),
		'transport'    => 'refresh',
		'settings'     => array(
			'elements'    => 'courses_archive_title_elements',
			'title'       => 'courses_archive_title_element_title',
			'breadcrumb'  => 'courses_archive_title_element_breadcrumb',
		),
	),
	'courses_archive_title_color' => array(
		'control_type' => 'base_color_control',
		'section'      => 'courses_archive_layout_design',
		'label'        => esc_html__( 'Title Color', 'avanam' ),
		'default'      => webapp()->default( 'courses_archive_title_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.courses-archive-title h1',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
		),
		'input_attrs'  => array(
			'colors' => array(
				'color' => array(
					'tooltip' => __( 'Color', 'avanam' ),
					'palette' => true,
				),
			),
		),
	),
	'courses_archive_title_breadcrumb_color' => array(
		'control_type' => 'base_color_control',
		'section'      => 'courses_archive_layout_design',
		'label'        => esc_html__( 'Breadcrumb Colors', 'avanam' ),
		'default'      => webapp()->default( 'courses_archive_title_breadcrumb_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.courses-archive-title .base-breadcrumbs',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '.courses-archive-title .base-breadcrumbs a:hover',
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
					'tooltip' => __( 'Link Hover Color', 'avanam' ),
					'palette' => true,
				),
			),
		),
	),
	'courses_archive_title_background' => array(
		'control_type' => 'base_background_control',
		'section'      => 'courses_archive_layout_design',
		'label'        => esc_html__( 'Archive Title Background', 'avanam' ),
		'default'      => webapp()->default( 'courses_archive_title_background' ),
		'context'      => array(
			array(
				'setting'    => 'courses_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'courses_archive_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css_background',
				'selector' => '#inner-wrap .courses-archive-hero-section .entry-hero-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'input_attrs'  => array(
			'tooltip'  => __( 'Course Archive Title Background', 'avanam' ),
		),
	),
	'courses_archive_title_overlay_color' => array(
		'control_type' => 'base_color_control',
		'section'      => 'courses_archive_layout_design',
		'label'        => esc_html__( 'Background Overlay Color', 'avanam' ),
		'default'      => webapp()->default( 'courses_archive_title_overlay_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.courses-archive-hero-section .hero-section-overlay',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			),
		),
		'context'      => array(
			array(
				'setting'    => 'courses_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'courses_archive_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'input_attrs'  => array(
			'colors' => array(
				'color' => array(
					'tooltip' => __( 'Overlay Color', 'avanam' ),
					'palette' => true,
				),
			),
			'allowGradient' => true,
		),
	),
	'courses_archive_title_border' => array(
		'control_type' => 'base_borders_control',
		'section'      => 'courses_archive_layout_design',
		'label'        => esc_html__( 'Border', 'avanam' ),
		'default'      => webapp()->default( 'courses_archive_title_border' ),
		'context'      => array(
			array(
				'setting'    => 'courses_archive_title',
				'operator'   => '=',
				'value'      => true,
			),
			array(
				'setting'    => 'courses_archive_title_layout',
				'operator'   => '=',
				'value'      => 'above',
			),
		),
		'settings'     => array(
			'border_top'    => 'courses_archive_title_top_border',
			'border_bottom' => 'courses_archive_title_bottom_border',
		),
		'live_method'     => array(
			'courses_archive_title_top_border' => array(
				array(
					'type'     => 'css_border',
					'selector' => '.courses-archive-hero-section .entry-hero-container-inner',
					'pattern'  => '$',
					'property' => 'border-top',
					'key'      => 'border',
				),
			),
			'courses_archive_title_bottom_border' => array( 
				array(
					'type'     => 'css_border',
					'selector' => '.courses-archive-hero-section .entry-hero-container-inner',
					'property' => 'border-bottom',
					'pattern'  => '$',
					'key'      => 'border',
				),
			),
		),
	),
	'info_courses_archive_layout' => array(
		'control_type' => 'base_title_control',
		'section'      => 'courses_archive_layout',
		'priority'     => 10,
		'label'        => esc_html__( 'Archive Layout', 'avanam' ),
		'settings'     => false,
	),
	'info_courses_archive_layout_design' => array(
		'control_type' => 'base_title_control',
		'section'      => 'courses_archive_layout_design',
		'priority'     => 10,
		'label'        => esc_html__( 'Archive Layout', 'avanam' ),
		'settings'     => false,
	),
	'courses_archive_layout' => array(
		'control_type' => 'base_radio_icon_control',
		'section'      => 'courses_archive_layout',
		'label'        => esc_html__( 'Archive Layout', 'avanam' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => webapp()->default( 'courses_archive_layout' ),
		'input_attrs'  => array(
			'layout' => array(
				'normal' => array(
					'tooltip' => __( 'Normal', 'avanam' ),
					'icon' => 'normal',
				),
				'narrow' => array(
					'tooltip' => __( 'Narrow', 'avanam' ),
					'icon' => 'narrow',
				),
				'fullwidth' => array(
					'tooltip' => __( 'Fullwidth', 'avanam' ),
					'icon' => 'fullwidth',
				),
				'left' => array(
					'tooltip' => __( 'Left Sidebar', 'avanam' ),
					'icon' => 'leftsidebar',
				),
				'right' => array(
					'tooltip' => __( 'Right Sidebar', 'avanam' ),
					'icon' => 'rightsidebar',
				),
			),
			'class'      => 'base-three-col',
			'responsive' => false,
		),
	),
	'courses_archive_sidebar_id' => array(
		'control_type' => 'base_select_control',
		'section'      => 'courses_archive_layout',
		'label'        => esc_html__( 'Archive Default Sidebar', 'avanam' ),
		'transport'    => 'refresh',
		'priority'     => 10,
		'default'      => webapp()->default( 'courses_archive_sidebar_id' ),
		'input_attrs'  => array(
			'options' => webapp()->sidebar_options(),
		),
	),
	'courses_archive_background' => array(
		'control_type' => 'base_background_control',
		'section'      => 'courses_archive_layout_design',
		'label'        => esc_html__( 'Site Background', 'avanam' ),
		'default'      => webapp()->default( 'courses_archive_background' ),
		'live_method'     => array(
			array(
				'type'     => 'css_background',
				'selector' => 'body.post-type-archive-courses, body.tax-courses_cat',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'input_attrs'  => array(
			'tooltip' => __( 'Course Archive Background', 'avanam' ),
		),
	),
	'courses_archive_content_background' => array(
		'control_type' => 'base_background_control',
		'section'      => 'courses_archive_layout_design',
		'label'        => esc_html__( 'Content Background', 'avanam' ),
		'default'      => webapp()->default( 'courses_archive_content_background' ),
		'live_method'  => array(
			array(
				'type'     => 'css_background',
				'selector' => 'body.post-type-archive-courses .content-bg, body.tax-courses_cat .content-bg, body.tax-courses_cat.content-style-unboxed .site, body.post-type-archive-courses.content-style-unboxed .site',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'input_attrs'  => array(
			'tooltip' => __( 'Archive Content Background', 'avanam' ),
		),
	),
);

Theme_Customizer::add_settings( $settings );

