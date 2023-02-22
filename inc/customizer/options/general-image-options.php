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
		'image_border_radius' => array(
			'control_type' => 'base_range_control',
			'section'      => 'general_image',
			'label'        => esc_html__( 'Border Radius', 'avanam' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.entry-content :where(.wp-block-image) img, .entry-content :where(.wp-block-base-image) img',
					'property' => 'border-radius',
					'pattern'  => '$',
					'key'      => 'size',
				),
			),
			'default'      => webapp()->default( 'image_border_radius' ),
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
	)
);

