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
		'comment_form_before_list' => array(
			'control_type' => 'base_switch_control',
			'sanitize'     => 'base_sanitize_toggle',
			'section'      => 'general_comments',
			'default'      => webapp()->default( 'comment_form_before_list' ),
			'label'        => esc_html__( 'Move Comments input above comment list.', 'avanam' ),
			'transport'    => 'refresh',
		),
		'comment_form_remove_web' => array(
			'control_type' => 'base_switch_control',
			'sanitize'     => 'base_sanitize_toggle',
			'section'      => 'general_comments',
			'default'      => webapp()->default( 'comment_form_remove_web' ),
			'label'        => esc_html__( 'Remove Comments Website field.', 'avanam' ),
			'transport'    => 'refresh',
		),
	)
);
