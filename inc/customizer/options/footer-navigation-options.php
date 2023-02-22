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
	'footer_navigation_tabs' => array(
		'control_type' => 'base_blank_control',
		'section'      => 'footer_navigation',
		'settings'     => false,
		'priority'     => 1,
		'description'  => $compontent_tabs,
	),
	'footer_navigation_link' => array(
		'control_type' => 'base_focus_button_control',
		'section'      => 'footer_navigation',
		'settings'     => false,
		'priority'     => 5,
		'label'        => esc_html__( 'Select Menu', 'avanam' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'input_attrs'  => array(
			'section' => 'menu_locations',
		),
	),
	'footer_navigation_spacing' => array(
		'control_type' => 'base_range_control',
		'section'      => 'footer_navigation',
		'priority'     => 5,
		'label'        => esc_html__( 'Items Spacing', 'avanam' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul > li > a',
				'property' => 'padding-left',
				'pattern'  => 'calc($ / 2)',
				'key'      => 'size',
			),
			array(
				'type'     => 'css',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul > li > a',
				'property' => 'padding-right',
				'pattern'  => 'calc($ / 2)',
				'key'      => 'size',
			),
		),
		'default'      => webapp()->default( 'footer_navigation_spacing' ),
		'input_attrs'  => array(
			'min'        => array(
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
				'vw'  => 0,
			),
			'max'        => array(
				'px'  => 100,
				'em'  => 12,
				'rem' => 12,
				'vw'  => 12,
			),
			'step'       => array(
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
				'vw'  => 0.01,
			),
			'units'      => array( 'px', 'em', 'rem', 'vw' ),
			'responsive' => false,
		),
	),
	'footer_navigation_stretch' => array(
		'control_type' => 'base_switch_control',
		'sanitize'     => 'base_sanitize_toggle',
		'section'      => 'footer_navigation',
		'priority'     => 5,
		'default'      => webapp()->default( 'footer_navigation_stretch' ),
		'label'        => esc_html__( 'Stretch Menu?', 'avanam' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.footer-navigation-wrap',
				'pattern'  => 'footer-navigation-layout-stretch-$',
				'key'      => 'switch',
			),
		),
	),
	'footer_navigation_vertical_spacing' => array(
		'control_type' => 'base_range_control',
		'section'      => 'footer_navigation',
		'label'        => esc_html__( 'Items Top and Bottom Padding', 'avanam' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul > li > a',
				'property' => 'padding-top',
				'pattern'  => '$',
				'key'      => 'size',
			),
			array(
				'type'     => 'css',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul > li > a',
				'property' => 'padding-bottom',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => webapp()->default( 'footer_navigation_vertical_spacing' ),
		'input_attrs'  => array(
			'min'        => array(
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
				'vh'  => 0,
			),
			'max'        => array(
				'px'  => 100,
				'em'  => 12,
				'rem' => 12,
				'vh'  => 12,
			),
			'step'       => array(
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
				'vh'  => 0.01,
			),
			'units'      => array( 'px', 'em', 'rem', 'vh' ),
			'responsive' => false,
		),
	),
	'footer_navigation_align' => array(
		'control_type' => 'base_radio_icon_control',
		'section'      => 'footer_navigation',
		'label'        => esc_html__( 'Content Align', 'avanam' ),
		'priority'     => 5,
		'default'      => webapp()->default( 'footer_navigation_align' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.footer-navigation-wrap',
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
	'footer_navigation_vertical_align' => array(
		'control_type' => 'base_radio_icon_control',
		'section'      => 'footer_navigation',
		'label'        => esc_html__( 'Content Vertical Align', 'avanam' ),
		'priority'     => 5,
		'default'      => webapp()->default( 'footer_navigation_vertical_align' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'live_method'  => array(
			array(
				'type'     => 'class',
				'selector' => '.footer-navigation-wrap',
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
	'footer_navigation_color' => array(
		'control_type' => 'base_color_control',
		'section'      => 'footer_navigation',
		'label'        => esc_html__( 'Navigation Colors', 'avanam' ),
		'default'      => webapp()->default( 'footer_navigation_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul li a',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul li a:hover',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
			),
			array(
				'type'     => 'css',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul li.current-menu-item > a, #colophon .footer-navigation .footer-menu-container > ul li.current_page_item > a',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'active',
			),
		),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
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
				'active' => array(
					'tooltip' => __( 'Active Color', 'avanam' ),
					'palette' => true,
				),
			),
		),
	),
	'footer_navigation_background' => array(
		'control_type' => 'base_color_control',
		'section'      => 'footer_navigation',
		'label'        => esc_html__( 'Navigation Background', 'avanam' ),
		'default'      => webapp()->default( 'footer_navigation_background' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul li a',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul li a:hover',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'hover',
			),
			array(
				'type'     => 'css',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul li.current-menu-item > a, #colophon .footer-navigation .footer-menu-container > ul > li.current_page_item > a',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'active',
			),
		),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'input_attrs'  => array(
			'colors' => array(
				'color' => array(
					'tooltip' => __( 'Initial Background', 'avanam' ),
					'palette' => true,
				),
				'hover' => array(
					'tooltip' => __( 'Hover Background', 'avanam' ),
					'palette' => true,
				),
				'active' => array(
					'tooltip' => __( 'Active Background', 'avanam' ),
					'palette' => true,
				),
			),
		),
	),
	'footer_navigation_typography' => array(
		'control_type' => 'base_typography_control',
		'section'      => 'footer_navigation',
		'label'        => esc_html__( 'Navigation Font', 'avanam' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'default'      => webapp()->default( 'footer_navigation_typography' ),
		'live_method'     => array(
			array(
				'type'     => 'css_typography',
				'selector' => '#colophon .footer-navigation .footer-menu-container > ul li a',
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
			'id'      => 'footer_navigation_typography',
			'options' => 'no-color',
		),
	),
);

Theme_Customizer::add_settings( $settings );

