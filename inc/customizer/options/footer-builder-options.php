<?php
/**
 * Header Builder Options
 *
 * @package Base
 */

namespace Base;

use Base\Theme_Customizer;
use function Base\webapp;

ob_start(); ?>
<!-- <div class="base-build-tabs nav-tab-wrapper wp-clearfix">
	<a href="#" class="nav-tab preview-desktop base-build-tabs-button" data-device="desktop">
		<span class="dashicons dashicons-desktop"></span>
		<span><?php esc_html_e( 'Desktop', 'avanam' ); ?></span>
	</a>
	<a href="#" class="nav-tab preview-tablet preview-mobile base-build-tabs-button" data-device="tablet">
		<span class="dashicons dashicons-smartphone"></span>
		<span><?php esc_html_e( 'Tablet / Mobile', 'avanam' ); ?></span>
	</a>
</div> -->
<span class="button button-secondary base-builder-hide-button base-builder-tab-toggle"><span class="dashicons dashicons-no"></span><?php esc_html_e( 'Hide', 'avanam' ); ?></span>
<span class="button button-secondary base-builder-show-button base-builder-tab-toggle"><span class="dashicons dashicons-edit"></span><?php esc_html_e( 'Footer Builder', 'avanam' ); ?></span>
<?php
$builder_tabs = ob_get_clean();
ob_start(); ?>
<div class="base-compontent-tabs nav-tab-wrapper wp-clearfix">
	<a href="#" class="nav-tab base-general-tab base-compontent-tabs-button nav-tab-active" data-tab="general">
		<span><?php esc_html_e( 'General', 'avanam' ); ?></span>
	</a>
	<a href="#" class="nav-tab base-design-tab base-compontent-tabs-button" data-tab="design">
		<span><?php esc_html_e( 'Design', 'avanam' ); ?></span>
	</a>
</div>
<?php
$compontent_tabs = ob_get_clean();
$settings = array(
	'footer_builder' => array(
		'control_type' => 'base_blank_control',
		'section'      => 'footer_builder',
		'settings'     => false,
		'description'  => $builder_tabs,
	),
	'footer_items' => array(
		'control_type' => 'base_builder_control',
		'section'      => 'footer_builder',
		'default'      => webapp()->default( 'footer_items' ),
		'partial'      => array(
			'selector'            => '#colophon',
			'container_inclusive' => true,
			'render_callback'     => 'Base\footer_markup',
		),
		'choices'      => array(
			'footer-navigation'          => array(
				'name'    => esc_html__( 'Footer Navigation', 'avanam' ),
				'section' => 'base_customizer_footer_navigation',
			),
			'footer-social'        => array(
				'name'    => esc_html__( 'Social', 'avanam' ),
				'section' => 'base_customizer_footer_social',
			),
			'footer-html'          => array(
				'name'    => esc_html__( 'Copyright', 'avanam' ),
				'section' => 'base_customizer_footer_html',
			),
			'footer-widget1' => array(
				'name'    => esc_html__( 'Widget 1', 'avanam' ),
				'section' => 'sidebar-widgets-footer1',
			),
			'footer-widget2' => array(
				'name'    => esc_html__( 'Widget 2', 'avanam' ),
				'section' => 'sidebar-widgets-footer2',
			),
			'footer-widget3' => array(
				'name'    => esc_html__( 'Widget 3', 'avanam' ),
				'section' => 'sidebar-widgets-footer3',
			),
			'footer-widget4' => array(
				'name'    => esc_html__( 'Widget 4', 'avanam' ),
				'section' => 'sidebar-widgets-footer4',
			),
			'footer-widget5' => array(
				'name'    => esc_html__( 'Widget 5', 'avanam' ),
				'section' => 'sidebar-widgets-footer5',
			),
			'footer-widget6' => array(
				'name'    => esc_html__( 'Widget 6', 'avanam' ),
				'section' => 'sidebar-widgets-footer6',
			),
		),
		'input_attrs'  => array(
			'group' => 'footer_items',
			'rows'  => array( 'top', 'middle', 'bottom' ),
			'zones' => array(
				'top' => array(
					'top_1' => esc_html__( 'Top - 1', 'avanam' ),
					'top_2' => esc_html__( 'Top - 2', 'avanam' ),
					'top_3' => esc_html__( 'Top - 3', 'avanam' ),
					'top_4' => esc_html__( 'Top - 4', 'avanam' ),
					'top_5' => esc_html__( 'Top - 5', 'avanam' ),
				),
				'middle' => array(
					'middle_1' => esc_html__( 'Middle - 1', 'avanam' ),
					'middle_2' => esc_html__( 'Middle - 2', 'avanam' ),
					'middle_3' => esc_html__( 'Middle - 3', 'avanam' ),
					'middle_4' => esc_html__( 'Middle - 4', 'avanam' ),
					'middle_5' => esc_html__( 'Middle - 5', 'avanam' ),
				),
				'bottom' => array(
					'bottom_1' => esc_html__( 'Bottom - 1', 'avanam' ),
					'bottom_2' => esc_html__( 'Bottom - 2', 'avanam' ),
					'bottom_3' => esc_html__( 'Bottom - 3', 'avanam' ),
					'bottom_4' => esc_html__( 'Bottom - 4', 'avanam' ),
					'bottom_5' => esc_html__( 'Bottom - 5', 'avanam' ),
				),
			),
		),
	),
	'footer_tab_settings' => array(
		'control_type' => 'base_blank_control',
		'section'      => 'footer_layout',
		'settings'     => false,
		'priority'     => 1,
		'description'  => $compontent_tabs,
	),
	'footer_available_items' => array(
		'control_type' => 'base_available_control',
		'section'      => 'footer_layout',
		'settings'     => false,
		'input_attrs'  => array(
			'group'  => 'footer_items',
			'zones'  => array( 'top', 'middle', 'bottom' ),
		),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
	),
	'footer_wrap_background' => array(
		'control_type' => 'base_background_control',
		'section'      => 'footer_layout',
		'label'        => esc_html__( 'Footer Background', 'avanam' ),
		'default'      => webapp()->default( 'footer_wrap_background' ),
		'live_method'     => array(
			array(
				'type'     => 'css_background',
				'selector' => '#colophon',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'input_attrs'  => array(
			'tooltip'  => __( 'Footer Background', 'avanam' ),
		),
	),
	'enable_footer_on_bottom' => array(
		'control_type' => 'base_switch_control',
		'sanitize'     => 'base_sanitize_toggle',
		'section'      => 'footer_layout',
		'default'      => webapp()->default( 'enable_footer_on_bottom' ),
		'label'        => esc_html__( 'Keep footer on bottom of screen', 'avanam' ),
		'transport'    => 'refresh',
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
	),
);

Theme_Customizer::add_settings( $settings );

