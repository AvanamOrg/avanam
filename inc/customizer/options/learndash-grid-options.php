<?php
/**
 * Grid Layout Options
 *
 * @package Base
 */

namespace Base;

use Base\Theme_Customizer;
use function Base\webapp;

$settings = array(
	'sfwd-grid_layout_tabs' => array(
		'control_type' => 'base_tab_control',
		'section'      => 'sfwd_grid_layout',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'avanam' ),
				'target' => 'sfwd_grid_layout',
			),
			'design' => array(
				'label'  => __( 'Design', 'avanam' ),
				'target' => 'sfwd_grid_layout_design',
			),
			'active' => 'general',
		),
	),
	'sfwd-grid_layout_tabs_design' => array(
		'control_type' => 'base_tab_control',
		'section'      => 'sfwd_grid_layout_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => array(
			'general' => array(
				'label'  => __( 'General', 'avanam' ),
				'target' => 'sfwd_grid_layout',
			),
			'design' => array(
				'label'  => __( 'Design', 'avanam' ),
				'target' => 'sfwd_grid_layout_design',
			),
			'active' => 'design',
		),
	),
	'learndash_course_grid' => array(
		'control_type' => 'base_switch_control',
		'sanitize'     => 'base_sanitize_toggle',
		'section'      => 'sfwd_grid_layout',
		'priority'     => 3,
		'default'      => webapp()->default( 'learndash_course_grid' ),
		'label'        => esc_html__( 'Override Course Grid Styles', 'avanam' ),
		'transport'    => 'refresh',
	),
	'learndash_course_grid_style' => array(
		'control_type' => 'base_radio_icon_control',
		'section'      => 'sfwd_grid_layout',
		'priority'     => 7,
		'label'        => esc_html__( 'Content Style', 'avanam' ),
		'default'      => webapp()->default( 'learndash_course_grid_style' ),
		'transport'    => 'refresh',
		'context'      => array(
			array(
				'setting'    => 'learndash_course_grid',
				'operator'   => '=',
				'value'      => true,
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'boxed' => array(
					'tooltip' => __( 'Boxed', 'avanam' ),
					'icon'    => 'gridBoxed',
				),
				'unboxed' => array(
					'tooltip' => __( 'Unboxed', 'avanam' ),
					'icon'    => 'gridUnboxed',
				),
			),
			'responsive' => false,
			'class'      => 'base-two-col',
		),
	),
	'sfwd-grid_title_font' => array(
		'control_type' => 'base_typography_control',
		'section'      => 'sfwd_grid_layout_design',
		'label'        => esc_html__( 'Course Grid Entry Title Font', 'avanam' ),
		'default'      => webapp()->default( 'sfwd-grid_title_font' ),
		'live_method'     => array(
			array(
				'type'     => 'css_typography',
				'selector' => '.ld-course-list-items .ld_course_grid.entry .entry-title',
				'property' => 'font',
				'key'      => 'typography',
			),
		),
		'context'      => array(
			array(
				'setting'    => 'learndash_course_grid',
				'operator'   => '=',
				'value'      => true,
			),
		),
		'input_attrs'  => array(
			'id'             => 'sfwd-grid_title_font',
			'headingInherit' => true,
		),
	),
);

Theme_Customizer::add_settings( $settings );

