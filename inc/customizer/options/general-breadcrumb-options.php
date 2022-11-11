<?php
/**
 * Breadcrumb Options
 *
 * @package Base
 */

namespace Base;

use Base\Theme_Customizer;
use function Base\webapp;

Theme_Customizer::add_settings(
	array(
		'breadcrumb_engine' => array(
			'control_type' => 'base_select_control',
			'section'      => 'breadcrumbs',
			'transport'    => 'refresh',
			'default'      => webapp()->default( 'breadcrumb_engine' ),
			'label'        => esc_html__( 'Breadcrumb Engine', 'avanam' ),
			'input_attrs'  => array(
				'options' => array(
					'' => array(
						'name' => __( 'Default', 'avanam' ),
					),
					'rankmath' => array(
						'name' => __( 'RankMath (must have activated in plugin)', 'avanam' ),
					),
					'yoast' => array(
						'name' => __( 'Yoast (must have activated in plugin)', 'avanam' ),
					),
					'seopress' => array(
						'name' => __( 'SEOPress (must have activated in plugin)', 'avanam' ),
					),
				),
			),
		),
		'breadcrumb_home_icon' => array(
			'control_type' => 'base_switch_control',
			'sanitize'     => 'base_sanitize_toggle',
			'section'      => 'breadcrumbs',
			'default'      => webapp()->default( 'breadcrumb_home_icon' ),
			'label'        => esc_html__( 'Use icon for home?', 'avanam' ),
			'transport'    => 'refresh',
			'context'      => array(
				array(
					'setting'    => 'breadcrumb_engine',
					'operator'   => '=',
					'value'      => '',
				),
			),
		),
	)
);
