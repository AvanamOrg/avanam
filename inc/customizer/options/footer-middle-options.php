<?php
/**
 * Header Main Row Options
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
	'footer_middle_tabs' => array(
		'control_type' => 'base_blank_control',
		'section'      => 'footer_middle',
		'settings'     => false,
		'priority'     => 1,
		'description'  => $compontent_tabs,
	),
	'footer_middle_contain' => array(
		'control_type' => 'base_radio_icon_control',
		'section'      => 'footer_middle',
		'priority'     => 4,
		'default'      => webapp()->default( 'footer_middle_contain' ),
		'label'        => esc_html__( 'Container Width', 'avanam' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.site-middle-footer-wrap',
				'pattern'  => array(
					'desktop' => 'site-footer-row-layout-$',
					'tablet'  => 'site-footer-row-tablet-layout-$',
					'mobile'  => 'site-footer-row-mobile-layout-$',
				),
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
		),
	),
	'footer_middle_columns' => array(
		'control_type' => 'base_radio_icon_control',
		'section'      => 'footer_middle',
		'label'        => esc_html__( 'Columns', 'avanam' ),
		'priority'     => 5,
		//'transport'    => 'refresh',
		'default'      => webapp()->default( 'footer_middle_columns' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'partial'      => array(
			'selector'            => '#colophon',
			'container_inclusive' => true,
			'render_callback'     => 'Base\footer_markup',
		),
		'input_attrs'  => array(
			'layout' => array(
				'1' => array(
					'name' => __( '1', 'avanam' ),
				),
				'2' => array(
					'name' => __( '2', 'avanam' ),
				),
				'3' => array(
					'name' => __( '3', 'avanam' ),
				),
				'4' => array(
					'name' => __( '4', 'avanam' ),
				),
				'5' => array(
					'name' => __( '5', 'avanam' ),
				),
			),
			'responsive' => false,
			'footer'     => 'middle',
		),
	),
	'footer_middle_layout' => array(
		'control_type' => 'base_row_control',
		'section'      => 'footer_middle',
		'label'        => esc_html__( 'Layout', 'avanam' ),
		'priority'     => 5,
		//'transport'    => 'refresh',
		'default'      => webapp()->default( 'footer_middle_layout' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.site-middle-footer-inner-wrap',
				'pattern'  => array(
					'desktop' => 'site-footer-row-column-layout-$',
					'tablet'  => 'site-footer-row-tablet-column-layout-$',
					'mobile'  => 'site-footer-row-mobile-column-layout-$',
				),
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'responsive' => true,
			'footer'     => 'middle',
		),
	),
	'footer_middle_collapse' => array(
		'control_type' => 'base_radio_icon_control',
		'section'      => 'footer_middle',
		'priority'     => 5,
		'default'      => webapp()->default( 'footer_middle_collapse' ),
		'label'        => esc_html__( 'Row Collapse', 'avanam' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
			array(
				'setting'  => '__device',
				'operator' => 'in',
				'value'    => array( 'tablet', 'mobile' ),
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.site-middle-footer-inner-wrap',
				'pattern'  => 'ft-ro-collapse-$',
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'normal' => array(
					'name'    => __( 'Left to Right', 'avanam' ),
					'icon'    => '',
				),
				'rtl' => array(
					'name'    => __( 'Right to Left', 'avanam' ),
					'icon'    => '',
				),
			),
			'responsive' => false,
			'footer'     => 'middle',
		),
	),
	'footer_middle_direction' => array(
		'control_type' => 'base_radio_icon_control',
		'section'      => 'footer_middle',
		'priority'     => 5,
		'default'      => webapp()->default( 'footer_middle_direction' ),
		'label'        => esc_html__( 'Column Direction', 'avanam' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.site-middle-footer-inner-wrap',
				'pattern'  => array(
					'desktop' => 'ft-ro-dir-$',
					'tablet'  => 'ft-ro-t-dir-$',
					'mobile'  => 'ft-ro-m-dir-$',
				),
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'row' => array(
					'tooltip' => __( 'Left to Right', 'avanam' ),
					'name'    => __( 'Row', 'avanam' ),
					'icon'    => '',
				),
				'column' => array(
					'tooltip' => __( 'Top to Bottom', 'avanam' ),
					'name'    => __( 'Column', 'avanam' ),
					'icon'    => '',
				),
			),
			'responsive' => true,
			'footer'     => 'middle',
		),
	),
	'footer_middle_column_spacing' => array(
		'control_type' => 'base_range_control',
		'section'      => 'footer_middle',
		'priority'     => 5,
		'label'        => esc_html__( 'Column Spacing', 'avanam' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'property' => 'grid-column-gap',
				'selector' => '#colophon .site-middle-footer-inner-wrap',
				'pattern'  => '$',
				'key'      => 'size',
			),
			array(
				'type'     => 'css',
				'property' => 'grid-row-gap',
				'selector' => '#colophon .site-middle-footer-inner-wrap',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => webapp()->default( 'footer_middle_column_spacing' ),
		'input_attrs'  => array(
			'min'     => array(
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
			),
			'max'     => array(
				'px'  => 200,
				'em'  => 8,
				'rem' => 8,
			),
			'step'    => array(
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
			),
			'units'   => array( 'px', 'em', 'rem' ),
		),
	),
	'footer_middle_widget_spacing' => array(
		'control_type' => 'base_range_control',
		'section'      => 'footer_middle',
		'priority'     => 5,
		'label'        => esc_html__( 'Widget Spacing', 'avanam' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'property' => 'margin-bottom',
				'selector' => '.site-middle-footer-inner-wrap .widget',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => webapp()->default( 'footer_middle_widget_spacing' ),
		'input_attrs'  => array(
			'min'     => array(
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
			),
			'max'     => array(
				'px'  => 200,
				'em'  => 8,
				'rem' => 8,
			),
			'step'    => array(
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
			),
			'units'   => array( 'px', 'em', 'rem' ),
		),
	),
	'footer_middle_top_spacing' => array(
		'control_type' => 'base_range_control',
		'section'      => 'footer_middle',
		'priority'     => 5,
		'label'        => esc_html__( 'Top Spacing', 'avanam' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#colophon .site-middle-footer-inner-wrap',
				'pattern'  => '$',
				'property' => 'padding-top',
				'key'      => 'size',
			),
		),
		'default'      => webapp()->default( 'footer_middle_top_spacing' ),
		'input_attrs'  => array(
			'min'     => array(
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
			),
			'max'     => array(
				'px'  => 200,
				'em'  => 8,
				'rem' => 8,
			),
			'step'    => array(
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
			),
			'units'   => array( 'px', 'em', 'rem' ),
		),
	),
	'footer_middle_bottom_spacing' => array(
		'control_type' => 'base_range_control',
		'section'      => 'footer_middle',
		'priority'     => 5,
		'label'        => esc_html__( 'Bottom Spacing', 'avanam' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#colophon .site-middle-footer-inner-wrap',
				'pattern'  => '$',
				'property' => 'padding-bottom',
				'key'      => 'size',
			),
		),
		'default'      => webapp()->default( 'footer_middle_bottom_spacing' ),
		'input_attrs'  => array(
			'min'     => array(
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
			),
			'max'     => array(
				'px'  => 200,
				'em'  => 8,
				'rem' => 8,
			),
			'step'    => array(
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
			),
			'units'   => array( 'px', 'em', 'rem' ),
		),
	),
	'footer_middle_height' => array(
		'control_type' => 'base_range_control',
		'section'      => 'footer_middle',
		'priority'     => 5,
		'label'        => esc_html__( 'Min Height', 'avanam' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#colophon .site-middle-footer-inner-wrap',
				'pattern'  => '$',
				'property' => 'min-height',
				'key'      => 'size',
			),
		),
		'default'      => webapp()->default( 'footer_middle_height' ),
		'input_attrs'  => array(
			'min'     => array(
				'px'  => 10,
				'em'  => 1,
				'rem' => 1,
				'vh'  => 2,
			),
			'max'     => array(
				'px'  => 400,
				'em'  => 12,
				'rem' => 12,
				'vh'  => 40,
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
	'footer_middle_widget_title' => array(
		'control_type' => 'base_typography_control',
		'section'      => 'footer_middle',
		'label'        => esc_html__( 'Widget Titles', 'avanam' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'default'      => webapp()->default( 'footer_middle_widget_title' ),
		'live_method'     => array(
			array(
				'type'     => 'css_typography',
				'selector' => '.site-middle-footer-wrap .site-footer-row-container-inner .widget-title',
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
			'id' => 'footer_middle_widget_title',
		),
	),
	'footer_middle_widget_content' => array(
		'control_type' => 'base_typography_control',
		'section'      => 'footer_middle',
		'label'        => esc_html__( 'Widget Content', 'avanam' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'default'      => webapp()->default( 'footer_middle_widget_content' ),
		'live_method'     => array(
			array(
				'type'     => 'css_typography',
				'selector' => '.site-middle-footer-wrap .site-footer-row-container-inner',
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
			'id' => 'footer_middle_widget_content',
		),
	),
	'footer_middle_link_colors' => array(
		'control_type' => 'base_color_control',
		'section'      => 'footer_middle',
		'label'        => esc_html__( 'Link Colors', 'avanam' ),
		'default'      => webapp()->default( 'footer_middle_link_colors' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.site-footer .site-middle-footer-wrap a:where(:not(.button):not(.wp-block-button__link):not(.wp-element-button))',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '.site-footer .site-middle-footer-wrap a:where(:not(.button):not(.wp-block-button__link):not(.wp-element-button)):hover',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
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
			),
		),
	),
	'footer_middle_link_style' => array(
		'control_type' => 'base_select_control',
		'section'      => 'footer_middle',
		'default'      => webapp()->default( 'footer_middle_link_style' ),
		'label'        => esc_html__( 'Link Style', 'avanam' ),
		'input_attrs'  => array(
			'options' => array(
				'plain' => array(
					'name' => __( 'Underline on Hover', 'avanam' ),
				),
				'normal' => array(
					'name' => __( 'Underline', 'avanam' ),
				),
				'noline' => array(
					'name' => __( 'No Underline', 'avanam' ),
				),
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.site-middle-footer-inner-wrap',
				'pattern'  => 'ft-ro-lstyle-$',
				'key'      => '',
			),
		),
	),
	'footer_middle_background' => array(
		'control_type' => 'base_background_control',
		'section'      => 'footer_middle',
		'label'        => esc_html__( 'Middle Row Background', 'avanam' ),
		'default'      => webapp()->default( 'footer_middle_background' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css_background',
				'selector' => '.site-middle-footer-wrap .site-footer-row-container-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'input_attrs'  => array(
			'tooltip'  => __( 'Middle Row Background', 'avanam' ),
		),
	),
	'footer_middle_column_border' => array(
		'control_type' => 'base_border_control',
		'section'      => 'footer_middle',
		'label'        => esc_html__( 'Column Border', 'avanam' ),
		'default'      => webapp()->default( 'footer_middle_column_border' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css_border',
				'selector' => '.site-middle-footer-inner-wrap .site-footer-section:not(:last-child):after',
				'pattern'  => '$',
				'property' => 'border-right',
				'pattern'  => '$',
				'key'      => 'border',
			),
		),
	),
	'footer_middle_border' => array(
		'control_type' => 'base_borders_control',
		'section'      => 'footer_middle',
		'label'        => esc_html__( 'Border', 'avanam' ),
		'default'      => webapp()->default( 'footer_middle_border' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'settings'     => array(
			'border_top'    => 'footer_middle_top_border',
			'border_bottom' => 'footer_middle_bottom_border',
		),
		'live_method'     => array(
			'footer_middle_top_border' => array(
				array(
					'type'     => 'css_border',
					'selector' => array(
						'desktop' => '.site-middle-footer-wrap .site-footer-row-container-inner',
						'tablet'  => '.site-middle-footer-wrap .site-footer-row-container-inner',
						'mobile'  => '.site-middle-footer-wrap .site-footer-row-container-inner',
					),
					'pattern'  => array(
						'desktop' => '$',
						'tablet'  => '$',
						'mobile'  => '$',
					),
					'property' => 'border-top',
					'pattern'  => '$',
					'key'      => 'border',
				),
			),
			'footer_middle_bottom_border' => array( 
				array(
					'type'     => 'css_border',
					'selector' => array(
						'desktop' => '.site-middle-footer-wrap .site-footer-row-container-inner',
						'tablet'  => '.site-middle-footer-wrap .site-footer-row-container-inner',
						'mobile'  => '.site-middle-footer-wrap .site-footer-row-container-inner',
					),
					'pattern'  => array(
						'desktop' => '$',
						'tablet'  => '$',
						'mobile'  => '$',
					),
					'property' => 'border-bottom',
					'pattern'  => '$',
					'key'      => 'border',
				),
			),
		),
	),
	// 'footer_middle_top_border' => array(
	// 	'control_type' => 'base_border_control',
	// 	'section'      => 'footer_middle',
	// 	'label'        => esc_html__( 'Top Border', 'avanam' ),
	// 	'default'      => webapp()->default( 'footer_middle_top_border' ),
	// 	'context'      => array(
	// 		array(
	// 			'setting' => '__current_tab',
	// 			'value'   => 'design',
	// 		),
	// 	),
	// 	'live_method'     => array(
	// 		array(
	// 			'type'     => 'css_border',
	// 			'selector' => array(
	// 				'desktop' => '.site-middle-footer-wrap .site-footer-row-container-inner',
	// 				'tablet'  => '.site-middle-footer-wrap .site-footer-row-container-inner',
	// 				'mobile'  => '.site-middle-footer-wrap .site-footer-row-container-inner',
	// 			),
	// 			'pattern'  => array(
	// 				'desktop' => '$',
	// 				'tablet'  => '$',
	// 				'mobile'  => '$',
	// 			),
	// 			'property' => 'border-top',
	// 			'pattern'  => '$',
	// 			'key'      => 'border',
	// 		),
	// 	),
	// ),
	// 'footer_middle_bottom_border' => array(
	// 	'control_type' => 'base_border_control',
	// 	'section'      => 'footer_middle',
	// 	'label'        => esc_html__( 'Bottom Border', 'avanam' ),
	// 	'default'      => webapp()->default( 'footer_middle_bottom_border' ),
	// 	'context'      => array(
	// 		array(
	// 			'setting' => '__current_tab',
	// 			'value'   => 'design',
	// 		),
	// 	),
	// 	'live_method'     => array(
	// 		array(
	// 			'type'     => 'css_border',
	// 			'selector' => array(
	// 				'desktop' => '.site-middle-footer-wrap .site-footer-row-container-inner',
	// 				'tablet'  => '.site-middle-footer-wrap .site-footer-row-container-inner',
	// 				'mobile'  => '.site-middle-footer-wrap .site-footer-row-container-inner',
	// 			),
	// 			'pattern'  => array(
	// 				'desktop' => '$',
	// 				'tablet'  => '$',
	// 				'mobile'  => '$',
	// 			),
	// 			'property' => 'border-bottom',
	// 			'pattern'  => '$',
	// 			'key'      => 'border',
	// 		),
	// 	),
	// ),
);

Theme_Customizer::add_settings( $settings );

