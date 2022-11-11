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
<div class="base-build-tabs nav-tab-wrapper wp-clearfix">
	<a href="#" class="nav-tab preview-desktop base-build-tabs-button" data-device="desktop">
		<span class="dashicons dashicons-desktop"></span>
		<span><?php esc_html_e( 'Desktop', 'avanam' ); ?></span>
	</a>
	<a href="#" class="nav-tab preview-tablet preview-mobile base-build-tabs-button" data-device="tablet">
		<span class="dashicons dashicons-smartphone"></span>
		<span><?php esc_html_e( 'Tablet / Mobile', 'avanam' ); ?></span>
	</a>
</div>
<span class="button button-secondary base-builder-hide-button base-builder-tab-toggle"><span class="dashicons dashicons-no"></span><?php esc_html_e( 'Hide', 'avanam' ); ?></span>
<span class="button button-secondary base-builder-show-button base-builder-tab-toggle"><span class="dashicons dashicons-edit"></span><?php esc_html_e( 'Header Builder', 'avanam' ); ?></span>
<?php
$builder_tabs = ob_get_clean();
ob_start();
?>
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
	'header_builder' => array(
		'control_type' => 'base_blank_control',
		'section'      => 'header_builder',
		'settings'     => false,
		'description'  => $builder_tabs,
	),
	'header_desktop_items' => array(
		'control_type' => 'base_builder_control',
		'section'      => 'header_builder',
		'default'      => webapp()->default( 'header_desktop_items' ),
		'context'      => array(
			array(
				'setting' => '__device',
				'value'   => 'desktop',
			),
		),
		'partial'      => array(
			'selector'            => '#masthead',
			'container_inclusive' => true,
			'render_callback'     => 'Base\header_markup',
		),
		'choices'      => array(
			'logo'          => array(
				'name'    => esc_html__( 'Logo', 'avanam' ),
				'section' => 'title_tagline',
			),
			'navigation'          => array(
				'name'    => esc_html__( 'Primary Navigation', 'avanam' ),
				'section' => 'base_customizer_primary_navigation',
			),
			'navigation-2'        => array(
				'name'    => esc_html__( 'Secondary Navigation', 'avanam' ),
				'section' => 'base_customizer_secondary_navigation',
			),
			'search' => array(
				'name'    => esc_html__( 'Search', 'avanam' ),
				'section' => 'base_customizer_header_search',
			),
			'button'        => array(
				'name'    => esc_html__( 'Button', 'avanam' ),
				'section' => 'base_customizer_header_button',
			),
			'social'        => array(
				'name'    => esc_html__( 'Social', 'avanam' ),
				'section' => 'base_customizer_header_social',
			),
			'html'          => array(
				'name'    => esc_html__( 'HTML', 'avanam' ),
				'section' => 'base_customizer_header_html',
			),
		),
		'input_attrs'  => array(
			'group' => 'header_desktop_items',
			'rows'  => array( 'top', 'main', 'bottom' ),
			'zones' => array(
				'top' => array(
					'top_left'         => is_rtl() ? esc_html__( 'Top - Right', 'avanam' ) : esc_html__( 'Top - Left', 'avanam' ),
					'top_left_center'  => is_rtl() ? esc_html__( 'Top - Right Center', 'avanam' ) : esc_html__( 'Top - Left Center', 'avanam' ),
					'top_center'       => esc_html__( 'Top - Center', 'avanam' ),
					'top_right_center' => is_rtl() ? esc_html__( 'Top - Left Center', 'avanam' ) : esc_html__( 'Top - Right Center', 'avanam' ),
					'top_right'        => is_rtl() ? esc_html__( 'Top - Left', 'avanam' ) : esc_html__( 'Top - Right', 'avanam' ),
				),
				'main' => array(
					'main_left'         => is_rtl() ? esc_html__( 'Main - Right', 'avanam' ) : esc_html__( 'Main - Left', 'avanam' ),
					'main_left_center'  => is_rtl() ? esc_html__( 'Main - Right Center', 'avanam' ) : esc_html__( 'Main - Left Center', 'avanam' ),
					'main_center'       => esc_html__( 'Main - Center', 'avanam' ),
					'main_right_center' => is_rtl() ? esc_html__( 'Main - Left Center', 'avanam' ) : esc_html__( 'Main - Right Center', 'avanam' ),
					'main_right'        => is_rtl() ? esc_html__( 'Main - Left', 'avanam' ) : esc_html__( 'Main - Right', 'avanam' ),
				),
				'bottom' => array(
					'bottom_left'         => is_rtl() ? esc_html__( 'Bottom - Right', 'avanam' ) : esc_html__( 'Bottom - Left', 'avanam' ),
					'bottom_left_center'  => is_rtl() ? esc_html__( 'Bottom - Right Center', 'avanam' ) : esc_html__( 'Bottom - Left Center', 'avanam' ),
					'bottom_center'       => esc_html__( 'Bottom - Center', 'avanam' ),
					'bottom_right_center' => is_rtl() ? esc_html__( 'Bottom - Left Center', 'avanam' ) : esc_html__( 'Bottom - Right Center', 'avanam' ),
					'bottom_right'        => is_rtl() ? esc_html__( 'Bottom - Left', 'avanam' ) : esc_html__( 'Bottom - Right', 'avanam' ),
				),
			),
		),
	),
	'header_tab_settings' => array(
		'control_type' => 'base_blank_control',
		'section'      => 'header_layout',
		'settings'     => false,
		'priority'     => 1,
		'description'  => $compontent_tabs,
	),
	'header_desktop_available_items' => array(
		'control_type' => 'base_available_control',
		'section'      => 'header_layout',
		'settings'     => false,
		'input_attrs'  => array(
			'group'  => 'header_desktop_items',
			'zones'  => array( 'top', 'main', 'bottom' ),
		),
		'context'      => array(
			array(
				'setting' => '__device',
				'value'   => 'desktop',
			),
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
	),
	'header_mobile_items' => array(
		'control_type' => 'base_builder_control',
		'section'      => 'header_builder',
		'transport'    => 'refresh',
		'default'      => webapp()->default( 'header_mobile_items' ),
		'context'      => array(
			array(
				'setting'  => '__device',
				'operator' => 'in',
				'value'    => array( 'tablet', 'mobile' ),
			),
		),
		'partial'      => array(
			'selector'            => '#mobile-header',
			'container_inclusive' => true,
			'render_callback'     => 'Base\mobile_header',
		),
		'choices'      => array(
			'mobile-logo'          => array(
				'name'    => esc_html__( 'Logo', 'avanam' ),
				'section' => 'title_tagline',
			),
			'mobile-navigation' => array(
				'name'    => esc_html__( 'Mobile Navigation', 'avanam' ),
				'section' => 'base_customizer_mobile_navigation',
			),
			// 'mobile-navigation2'          => array(
			// 	'name'    => esc_html__( 'Horizontal Navigation', 'avanam' ),
			// 	'section' => 'mobile_horizontal_navigation',
			// ),
			'search' => array(
				'name'    => esc_html__( 'Search Toggle', 'avanam' ),
				'section' => 'base_customizer_header_search',
			),
			'mobile-button'        => array(
				'name'    => esc_html__( 'Button', 'avanam' ),
				'section' => 'base_customizer_mobile_button',
			),
			'mobile-social'        => array(
				'name'    => esc_html__( 'Social', 'avanam' ),
				'section' => 'base_customizer_mobile_social',
			),
			'mobile-html'          => array(
				'name'    => esc_html__( 'HTML', 'avanam' ),
				'section' => 'base_customizer_mobile_html',
			),
			'popup-toggle'          => array(
				'name'    => esc_html__( 'Trigger', 'avanam' ),
				'section' => 'base_customizer_mobile_trigger',
			),
		),
		'input_attrs'  => array(
			'group' => 'header_mobile_items',
			'rows'  => array( 'popup', 'top', 'main', 'bottom' ),
			'zones' => array(
				'popup' => array(
					'popup_content' => esc_html__( 'Popup Content', 'avanam' ),
				),
				'top' => array(
					'top_left'   => is_rtl() ? esc_html__( 'Top - Right', 'avanam' ) : esc_html__( 'Top - Left', 'avanam' ),
					'top_center' => esc_html__( 'Top - Center', 'avanam' ),
					'top_right'  => is_rtl() ? esc_html__( 'Top - Left', 'avanam' ) : esc_html__( 'Top - Right', 'avanam' ),
				),
				'main' => array(
					'main_left'   => is_rtl() ? esc_html__( 'Main - Right', 'avanam' ) : esc_html__( 'Main - Left', 'avanam' ),
					'main_center' => esc_html__( 'Main - Center', 'avanam' ),
					'main_right'  => is_rtl() ? esc_html__( 'Main - Left', 'avanam' ) : esc_html__( 'Main - Right', 'avanam' ),
				),
				'bottom' => array(
					'bottom_left'   => is_rtl() ? esc_html__( 'Bottom - Right', 'avanam' ) : esc_html__( 'Bottom - Left', 'avanam' ),
					'bottom_center' => esc_html__( 'Bottom - Center', 'avanam' ),
					'bottom_right'  => is_rtl() ? esc_html__( 'Bottom - Left', 'avanam' ) : esc_html__( 'Bottom - Right', 'avanam' ),
				),
			),
		),
	),
	'header_mobile_available_items' => array(
		'control_type' => 'base_available_control',
		'section'      => 'header_layout',
		'settings'     => false,
		'input_attrs'  => array(
			'group'  => 'header_mobile_items',
			'zones'  => array( 'popup', 'top', 'main', 'bottom' ),
		),
		'context'      => array(
			array(
				'setting'  => '__device',
				'operator' => 'in',
				'value'    => array( 'tablet', 'mobile' ),
			),
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
	),
	'header_transparent_link' => array(
		'control_type' => 'base_focus_button_control',
		'section'      => 'header_layout',
		'settings'     => false,
		'priority'     => 20,
		'label'        => esc_html__( 'Transparent Header', 'avanam' ),
		'input_attrs'  => array(
			'section' => 'base_customizer_transparent_header',
		),
	),
	'header_sticky_link' => array(
		'control_type' => 'base_focus_button_control',
		'section'      => 'header_layout',
		'settings'     => false,
		'priority'     => 20,
		'label'        => esc_html__( 'Sticky Header', 'avanam' ),
		'input_attrs'  => array(
			'section' => 'base_customizer_header_sticky',
		),
	),
	'header_wrap_background' => array(
		'control_type' => 'base_background_control',
		'section'      => 'header_layout',
		'label'        => esc_html__( 'Header Background', 'avanam' ),
		'default'      => webapp()->default( 'header_wrap_background' ),
		'live_method'     => array(
			array(
				'type'     => 'css_background',
				'selector' => '#masthead',
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
			'tooltip'  => __( 'Header Background', 'avanam' ),
		),
	),
	'header_mobile_switch' => array(
		'control_type' => 'base_range_control',
		'section'      => 'header_layout',
		'transport'    => 'refresh',
		'label'        => esc_html__( 'Screen size to switch to mobile header', 'avanam' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'default'      => webapp()->default( 'header_mobile_switch' ),
		'input_attrs'  => array(
			'min'        => array(
				'px'  => 0,
			),
			'max'        => array(
				'px'  => 4000,
			),
			'step'       => array(
				'px'  => 1,
			),
			'units'      => array( 'px' ),
			'responsive' => false,
		),
	),
);

Theme_Customizer::add_settings( $settings );

