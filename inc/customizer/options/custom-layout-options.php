<?php
/**
 * Header Main Row Options
 *
 * @package Base
 */

namespace Base;

use Base\Theme_Customizer;
use function Base\webapp;

$all_post_types    = webapp()->get_post_types_objects();
$extras_post_types = array();
$add_extras        = false;
foreach ( $all_post_types as $post_type_item ) {
	$post_type_name  = $post_type_item->name;
	$post_type_label = $post_type_item->label;
	$ignore_type     = webapp()->get_post_types_to_ignore();
	if ( ! in_array( $post_type_name, $ignore_type, true ) ) {
		$taxonomies = get_object_taxonomies( $post_type_name, 'objects' );
		$taxs = array();
		foreach ( $taxonomies as $term_slug => $term ) {
			if ( ! $term->public || ! $term->show_ui ) {
				continue;
			}
			$taxs[ $term_slug ] = $term->label;
		}
		$settings = array(
			$post_type_name . '_layout_tabs' => array(
				'control_type' => 'base_tab_control',
				'section'      => $post_type_name . '_layout',
				'settings'     => false,
				'priority'     => 1,
				'input_attrs'  => array(
					'general' => array(
						'label'  => __( 'General', 'avanam' ),
						'target' => $post_type_name . '_layout',
					),
					'design' => array(
						'label'  => __( 'Design', 'avanam' ),
						'target' =>  $post_type_name . '_layout_design',
					),
					'active' => 'general',
				),
			),
			$post_type_name . '_layout_tabs_design' => array(
				'control_type' => 'base_tab_control',
				'section'      =>  $post_type_name . '_layout_design',
				'settings'     => false,
				'priority'     => 1,
				'input_attrs'  => array(
					'general' => array(
						'label'  => __( 'General', 'avanam' ),
						'target' => $post_type_name . '_layout',
					),
					'design' => array(
						'label'  => __( 'Design', 'avanam' ),
						'target' => $post_type_name . '_layout_design',
					),
					'active' => 'design',
				),
			),
			'info_' . $post_type_name . '_title' => array(
				'control_type' => 'base_title_control',
				'section'      => $post_type_name . '_layout',
				'priority'     => 2,
				'label'        => $post_type_label . ' ' . esc_html__( 'Title', 'avanam' ),
				'settings'     => false,
			),
			'info_' . $post_type_name . '_title_design' => array(
				'control_type' => 'base_title_control',
				'section'      => $post_type_name . '_layout_design',
				'priority'     => 2,
				'label'        => $post_type_label . ' ' . esc_html__( 'Title', 'avanam' ),
				'settings'     => false,
			),
			$post_type_name . '_title' => array(
				'control_type' => 'base_switch_control',
				'sanitize'     => 'base_sanitize_toggle',
				'section'      => $post_type_name . '_layout',
				'priority'     => 3,
				'default'      => webapp()->default( $post_type_name . '_title', true ),
				'label'        => esc_html__( 'Show Title?', 'avanam' ),
				'transport'    => 'refresh',
			),
			$post_type_name . '_title_layout' => array(
				'control_type' => 'base_radio_icon_control',
				'section'      => $post_type_name . '_layout',
				'label'        => $post_type_label . ' ' . esc_html__( 'Title Layout', 'avanam' ),
				'transport'    => 'refresh',
				'priority'     => 4,
				'default'      => webapp()->default( $post_type_name . '_title_layout', 'normal' ),
				'context'      => array(
					array(
						'setting'    => $post_type_name . '_title',
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
			$post_type_name . '_title_inner_layout' => array(
				'control_type' => 'base_radio_icon_control',
				'section'      => $post_type_name . '_layout',
				'priority'     => 4,
				'default'      => webapp()->default( $post_type_name . '_title_inner_layout', 'standard' ),
				'label'        => esc_html__( 'Container Width', 'avanam' ),
				'context'      => array(
					array(
						'setting'    => $post_type_name . '_title',
						'operator'   => '=',
						'value'      => true,
					),
					array(
						'setting'    => $post_type_name . '_title_layout',
						'operator'   => '=',
						'value'      => 'above',
					),
				),
				'live_method'     => array(
					array(
						'type'     => 'class',
						'selector' => '.' . $post_type_name . '-hero-section',
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
			$post_type_name . '_title_align' => array(
				'control_type' => 'base_radio_icon_control',
				'section'      => $post_type_name . '_layout',
				'label'        => $post_type_label . ' ' . esc_html__( 'Title Align', 'avanam' ),
				'priority'     => 4,
				'default'      => webapp()->default( $post_type_name . '_title_align' ),
				'context'      => array(
					array(
						'setting'    => $post_type_name . '_title',
						'operator'   => '=',
						'value'      => true,
					),
				),
				'live_method'     => array(
					array(
						'type'     => 'class',
						'selector' => '.' . $post_type_name . '-title',
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
			$post_type_name . '_title_height' => array(
				'control_type' => 'base_range_control',
				'section'      => $post_type_name . '_layout',
				'priority'     => 5,
				'label'        => esc_html__( 'Container Min Height', 'avanam' ),
				'context'      => array(
					array(
						'setting'    => $post_type_name . '_title',
						'operator'   => '=',
						'value'      => true,
					),
					array(
						'setting'    => $post_type_name . '_title_layout',
						'operator'   => '=',
						'value'      => 'above',
					),
				),
				'live_method'     => array(
					array(
						'type'     => 'css',
						'selector' => '#inner-wrap .' . $post_type_name . '-hero-section .entry-header',
						'property' => 'min-height',
						'pattern'  => '$',
						'key'      => 'size',
					),
				),
				'default'      => webapp()->default( $post_type_name . '_title_height', array(
					'size' => array(
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					),
					'unit' => array(
						'mobile'  => 'px',
						'tablet'  => 'px',
						'desktop' => 'px',
					),
				) ),
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
			$post_type_name . '_title_elements' => array(
				'control_type' => 'base_sorter_control',
				'section'      => $post_type_name . '_layout',
				'priority'     => 6,
				'default'      => array( 'title', 'breadcrumb', 'meta' ),
				'label'        => esc_html__( 'Title Elements', 'avanam' ),
				'transport'    => 'refresh',
				'settings'     => array(
					'elements'   => $post_type_name . '_title_elements',
					'title'      => $post_type_name . '_title_element_title',
					'breadcrumb' => $post_type_name . '_title_element_breadcrumb',
					'categories' => $post_type_name . '_title_element_categories',
					'meta'       => $post_type_name . '_title_element_meta',
				),
				'input_attrs'  => array(
					'defaults' => array(
						'categories' => array(
							'enabled' => true,
							'style'   => 'normal',
							'divider' => 'vline',
							'taxonomy' => '',
						),
						'title'      => array(
							'enabled' => true,
						),
						'meta'       => array(
							'id'                     => 'meta',
							'enabled'                => false,
							'divider'                => 'dot',
							'author'                 => true,
							'authorImage'            => true,
							'authorEnableLabel'      => true,
							'authorLabel'            => '',
							'date'                   => true,
							'dateTime'               => false,
							'dateEnableLabel'        => false,
							'dateLabel'              => '',
							'dateUpdated'            => false,
							'dateUpdatedTime'        => false,
							'dateUpdatedDifferent'   => false,
							'dateUpdatedEnableLabel' => false,
							'dateUpdatedLabel'       => '',
							'comments'               => false,
							'commentsCondition'      => false,
						),
						'breadcrumb' => array(
							'enabled' => false,
							'show_title' => true,
						),
					),
					'group'      => $post_type_name . '_title_element',
					'taxonomies' => $taxs,
				),
			),
			$post_type_name . '_title_font' => array(
				'control_type' => 'base_typography_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => $post_type_label . ' ' . esc_html__( 'Title Font', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_title_font', array(
					'size' => array(
						'desktop' => '',
					),
					'lineHeight' => array(
						'desktop' => '',
					),
					'family'  => 'inherit',
					'google'  => false,
					'weight'  => '',
					'variant' => '',
					'color'   => '',
				) ),
				'live_method'     => array(
					array(
						'type'     => 'css_typography',
						'selector' => '.wp-site-blocks .' . $post_type_name . '-title h1',
						'property' => 'font',
						'key'      => 'typography',
					),
				),
				'input_attrs'  => array(
					'id'             => $post_type_name . '_title_font',
					'headingInherit' => true,
				),
			),
			$post_type_name . '_title_category_color' => array(
				'control_type' => 'base_color_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => esc_html__( 'Category Colors', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_title_category_color', array(
					'color' => '',
					'hover' => '',
				) ),
				'live_method'     => array(
					array(
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-title .entry-taxonomies, .' . $post_type_name . '-title .entry-taxonomies a',
						'property' => 'color',
						'pattern'  => '$',
						'key'      => 'color',
					),
					array(
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-title .entry-taxonomies a:hover',
						'property' => 'color',
						'pattern'  => '$',
						'key'      => 'hover',
					),
					array(
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-title .entry-taxonomies .category-style-pill a',
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'color',
					),
					array(
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-title .entry-taxonomies .category-style-pill a:hover',
						'property' => 'background',
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
			$post_type_name . '_title_category_font' => array(
				'control_type' => 'base_typography_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => esc_html__( 'Category Font', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_title_category_font', array(
					'size' => array(
						'desktop' => '',
					),
					'lineHeight' => array(
						'desktop' => '',
					),
					'family'  => 'inherit',
					'google'  => false,
					'weight'  => '',
					'variant' => '',
				) ),
				'live_method'     => array(
					array(
						'type'     => 'css_typography',
						'selector' => '.' . $post_type_name . '-title .entry-taxonomies, .' . $post_type_name . '-title .entry-taxonomies a',
						'property' => 'font',
						'key'      => 'typography',
					),
				),
				'input_attrs'  => array(
					'id'      => $post_type_name . '_title_category_font',
					'options' => 'no-color',
				),
			),
			$post_type_name . '_title_breadcrumb_color' => array(
				'control_type' => 'base_color_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => esc_html__( 'Breadcrumb Colors', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_title_breadcrumb_color', array(
					'color' => '',
					'hover' => '',
				) ),
				'live_method'     => array(
					array(
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-title .base-breadcrumbs',
						'property' => 'color',
						'pattern'  => '$',
						'key'      => 'color',
					),
					array(
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-title .base-breadcrumbs a:hover',
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
			$post_type_name . '_title_breadcrumb_font' => array(
				'control_type' => 'base_typography_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => esc_html__( 'Breadcrumb Font', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_title_breadcrumb_font', array(
					'size' => array(
						'desktop' => '',
					),
					'lineHeight' => array(
						'desktop' => '',
					),
					'family'  => 'inherit',
					'google'  => false,
					'weight'  => '',
					'variant' => '',
				) ),
				'live_method'     => array(
					array(
						'type'     => 'css_typography',
						'selector' => '.' . $post_type_name . '-title .base-breadcrumbs',
						'property' => 'font',
						'key'      => 'typography',
					),
				),
				'input_attrs'  => array(
					'id'      => $post_type_name . '_title_breadcrumb_font',
					'options' => 'no-color',
				),
			),
			$post_type_name . '_title_meta_color' => array(
				'control_type' => 'base_color_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => esc_html__( 'Meta Colors', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_title_meta_color' ),
				'live_method'     => array(
					array(
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-title .entry-meta',
						'property' => 'color',
						'pattern'  => '$',
						'key'      => 'color',
					),
					array(
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-title .entry-meta a:hover',
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
			$post_type_name . '_title_meta_font' => array(
				'control_type' => 'base_typography_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => esc_html__( 'Meta Font', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_title_meta_font', array(
					'size' => array(
						'desktop' => '',
					),
					'lineHeight' => array(
						'desktop' => '',
					),
					'family'  => 'inherit',
					'google'  => false,
					'weight'  => '',
					'variant' => '',
				) ),
				'live_method'     => array(
					array(
						'type'     => 'css_typography',
						'selector' => '.' . $post_type_name . '-title .entry-meta',
						'property' => 'font',
						'key'      => 'typography',
					),
				),
				'input_attrs'  => array(
					'id'      => $post_type_name . '_title_meta_font',
					'options' => 'no-color',
				),
			),
			$post_type_name . '_title_background' => array(
				'control_type' => 'base_background_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => $post_type_label . ' ' . esc_html__( 'Title Background', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_title_background', array(
					'desktop' => array(
						'color' => '',
					),
				) ),
				'context'      => array(
					array(
						'setting'    => $post_type_name . '_title',
						'operator'   => '=',
						'value'      => true,
					),
					array(
						'setting'    => $post_type_name . '_title_layout',
						'operator'   => '=',
						'value'      => 'above',
					),
				),
				'live_method'     => array(
					array(
						'type'     => 'css_background',
						'selector' => '#inner-wrap .page-hero-section .entry-hero-container-inner',
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'base',
					),
				),
				'input_attrs'  => array(
					'tooltip'  => $post_type_name . ' ' . __( 'Title Background', 'avanam' ),
				),
			),
			$post_type_name . '_title_featured_image' => array(
				'control_type' => 'base_switch_control',
				'section'      => $post_type_name . '_layout_design',
				'default'      => webapp()->default( $post_type_name . '_title_featured_image' ),
				'label'        => esc_html__( 'Use Featured Image for Background?', 'avanam' ),
				'transport'    => 'refresh',
				'context'      => array(
					array(
						'setting'    => $post_type_name . '_title',
						'operator'   => '=',
						'value'      => true,
					),
					array(
						'setting'    => $post_type_name . '_title_layout',
						'operator'   => '=',
						'value'      => 'above',
					),
				),
			),
			$post_type_name . '_title_overlay_color' => array(
				'control_type' => 'base_color_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => esc_html__( 'Background Overlay Color', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_title_overlay_color', array(
					'color' => '',
				) ),
				'live_method'     => array(
					array(
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-hero-section .hero-section-overlay',
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'color',
					),
				),
				'context'      => array(
					array(
						'setting'    => $post_type_name . '_title',
						'operator'   => '=',
						'value'      => true,
					),
					array(
						'setting'    => $post_type_name . '_title_layout',
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
			$post_type_name . '_title_top_border' => array(
				'control_type' => 'base_border_control',
				'section'      => $post_type_name . '_layout',
				'label'        => esc_html__( 'Top Border', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_title_top_border' ),
				'context'      => array(
					array(
						'setting'    => $post_type_name . '_title',
						'operator'   => '=',
						'value'      => true,
					),
					array(
						'setting'    => $post_type_name . '_title_layout',
						'operator'   => '=',
						'value'      => 'above',
					),
				),
				'live_method'     => array(
					array(
						'type'     => 'css_border',
						'selector' => '.' . $post_type_name . '-hero-section .entry-hero-container-inner',
						'pattern'  => '$',
						'property' => 'border-top',
						'pattern'  => '$',
						'key'      => 'border',
					),
				),
			),
			$post_type_name . '_title_bottom_border' => array(
				'control_type' => 'base_border_control',
				'section'      => $post_type_name . '_layout',
				'label'        => esc_html__( 'Bottom Border', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_title_bottom_border' ),
				'context'      => array(
					array(
						'setting'    => $post_type_name . '_title',
						'operator'   => '=',
						'value'      => true,
					),
					array(
						'setting'    => $post_type_name . '_title_layout',
						'operator'   => '=',
						'value'      => 'above',
					),
				),
				'live_method'     => array(
					array(
						'type'     => 'css_border',
						'selector' => '.' . $post_type_name . '-hero-section .entry-hero-container-inner',
						'pattern'  => '$',
						'property' => 'border-bottom',
						'pattern'  => '$',
						'key'      => 'border',
					),
				),
			),
			'info_' . $post_type_name . '_layout' => array(
				'control_type' => 'base_title_control',
				'section'      => $post_type_name . '_layout',
				'priority'     => 10,
				'label'        => $post_type_label . ' ' . esc_html__( 'Layout', 'avanam' ),
				'settings'     => false,
			),
			'info_' . $post_type_name . '_layout_design' => array(
				'control_type' => 'base_title_control',
				'section'      => $post_type_name . '_layout_design',
				'priority'     => 10,
				'label'        => $post_type_label . ' ' . esc_html__( 'Layout', 'avanam' ),
				'settings'     => false,
			),
			$post_type_name . '_layout' => array(
				'control_type' => 'base_radio_icon_control',
				'section'      => $post_type_name . '_layout',
				'label'        => $post_type_label . ' ' . esc_html__( 'Layout', 'avanam' ),
				'transport'    => 'refresh',
				'priority'     => 10,
				'default'      => webapp()->default( $post_type_name . '_layout', 'normal' ),
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
					'responsive' => false,
					'class'      => 'base-three-col',
				),
			),
			$post_type_name . '_sidebar_id' => array(
				'control_type' => 'base_select_control',
				'section'      => $post_type_name . '_layout',
				'label'        => esc_html__( 'Default Sidebar', 'avanam' ),
				'transport'    => 'refresh',
				'priority'     => 10,
				'default'      => webapp()->default( $post_type_name . '_sidebar_id' ),
				'input_attrs'  => array(
					'options' => webapp()->sidebar_options(),
				),
			),
			$post_type_name . '_content_style' => array(
				'control_type' => 'base_radio_icon_control',
				'section'      => $post_type_name . '_layout',
				'label'        => esc_html__( 'Content Style', 'avanam' ),
				'priority'     => 10,
				'default'      => webapp()->default( $post_type_name . '_content_style', 'boxed' ),
				'live_method'     => array(
					array(
						'type'     => 'class',
						'selector' => 'body.' . str_replace( '_', '-', $post_type_name ),
						'pattern'  => 'content-style-$',
						'key'      => '',
					),
					array(
						'type'     => 'class',
						'selector' => 'body.single-' . ( 'give_forms' === $post_type_name ? $post_type_name : str_replace( '_', '-', $post_type_name ) ),
						'pattern'  => 'content-style-$',
						'key'      => '',
					),
				),
				'input_attrs'  => array(
					'layout' => array(
						'boxed' => array(
							'tooltip' => __( 'Boxed', 'avanam' ),
							'icon' => 'boxed',
						),
						'unboxed' => array(
							'tooltip' => __( 'Unboxed', 'avanam' ),
							'icon' => 'narrow',
						),
					),
					'responsive' => false,
					'class'      => 'base-two-col',
				),
			),
			$post_type_name . '_vertical_padding' => array(
				'control_type' => 'base_radio_icon_control',
				'section'      => $post_type_name . '_layout',
				'label'        => esc_html__( 'Content Vertical Padding', 'avanam' ),
				'priority'     => 10,
				'default'      => webapp()->default( $post_type_name . '_vertical_padding', 'show' ),
				'live_method'     => array(
					array(
						'type'     => 'class',
						'selector' => 'body.' . str_replace( '_', '-', $post_type_name ),
						'pattern'  => 'content-vertical-padding-$',
						'key'      => '',
					),
					array(
						'type'     => 'class',
						'selector' => 'body.single-' . ( 'give_forms' === $post_type_name ? $post_type_name : str_replace( '_', '-', $post_type_name ) ),
						'pattern'  => 'content-vertical-padding-$',
						'key'      => '',
					),
				),
				'input_attrs'  => array(
					'layout' => array(
						'show' => array(
							'name' => __( 'Enable', 'avanam' ),
						),
						'hide' => array(
							'name' => __( 'Disable', 'avanam' ),
						),
						'top' => array(
							'name' => __( 'Top Only', 'avanam' ),
						),
						'bottom' => array(
							'name' => __( 'Bottom Only', 'avanam' ),
						),
					),
					'responsive' => false,
					'class'      => 'base-two-grid',
				),
			),
			$post_type_name . '_background' => array(
				'control_type' => 'base_background_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => esc_html__( 'Site Background', 'avanam' ),
				'default'      => webapp()->default( 'site_background' ),
				'live_method'     => array(
					array(
						'type'     => 'css_background',
						'selector' => 'body.single-' . str_replace( '_', '-', $post_type_name ),
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'base',
					),
					array(
						'type'     => 'css_background',
						'selector' => 'body.' . ( 'give_forms' === $post_type_name ? $post_type_name : str_replace( '_', '-', $post_type_name ) ),
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'base',
					),
				),
				'input_attrs'  => array(
					'tooltip' => $post_type_label . ' ' . __( 'Background', 'avanam' ),
				),
			),
			$post_type_name . '_content_background' => array(
				'control_type' => 'base_background_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => esc_html__( 'Content Background', 'avanam' ),
				'default'      => webapp()->default( 'content_background' ),
				'live_method'  => array(
					array(
						'type'     => 'css_background',
						'selector' => 'body.single-' . ( 'give_forms' === $post_type_name ? $post_type_name : str_replace( '_', '-', $post_type_name ) ) . ' .content-bg, body.single' . ( 'give_forms' === $post_type_name ? $post_type_name : str_replace( '_', '-', $post_type_name ) ) . '.content-style-unboxed .site',
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'base',
					),
				),
				'input_attrs'  => array(
					'tooltip' => $post_type_label . ' ' . __( 'Content Background', 'avanam' ),
				),
			),
		);
		$add_extras = false;
		$extras     = array();
		if ( post_type_supports( $post_type_name, 'thumbnail' ) ) {
			$add_extras = true;
			$extras[ $post_type_name . '_feature' ] = array(
				'control_type' => 'base_switch_control',
				'sanitize'     => 'base_sanitize_toggle',
				'section'      => $post_type_name . '_layout',
				'priority'     => 20,
				'default'      => webapp()->default( $post_type_name . '_feature' ),
				'label'        => esc_html__( 'Show Featured Image?', 'avanam' ),
				'transport'    => 'refresh',
			);
			$extras[ $post_type_name . '_feature_position' ] = array(
				'control_type' => 'base_radio_icon_control',
				'section'      => $post_type_name . '_layout',
				'label'        => esc_html__( 'Featured Image Position', 'avanam' ),
				'priority'     => 20,
				'transport'    => 'refresh',
				'default'      => webapp()->default( $post_type_name . '_feature_position', 'above' ),
				'context'      => array(
					array(
						'setting'    => $post_type_name . '_feature',
						'operator'   => '=',
						'value'      => true,
					),
				),
				'input_attrs'  => array(
					'layout' => array(
						'above' => array(
							'name' => __( 'Above', 'avanam' ),
						),
						'behind' => array(
							'name' => __( 'Behind', 'avanam' ),
						),
						'below' => array(
							'name' => __( 'Below', 'avanam' ),
						),
					),
					'responsive' => false,
				),
			);
			$extras[ $post_type_name . '_feature_ratio'] = array(
				'control_type' => 'base_radio_icon_control',
				'section'      => $post_type_name . '_layout',
				'label'        => esc_html__( 'Featured Image Ratio', 'avanam' ),
				'priority'     => 20,
				'default'      => webapp()->default( $post_type_name . '_feature_ratio', '2-3' ),
				'context'      => array(
					array(
						'setting'    => $post_type_name . '_feature',
						'operator'   => '=',
						'value'      => true,
					),
				),
				'live_method'     => array(
					array(
						'type'     => 'class',
						'selector' => 'body.' . str_replace( '_', '-', $post_type_name ) . ' .article-post-thumbnail',
						'pattern'  => 'base-thumbnail-ratio-$',
						'key'      => '',
					),
					array(
						'type'     => 'class',
						'selector' => 'body.single-' . str_replace( '_', '-', $post_type_name ) . ' .article-post-thumbnail',
						'pattern'  => 'base-thumbnail-ratio-$',
						'key'      => '',
					),
				),
				'input_attrs'  => array(
					'layout' => array(
						'inherit' => array(
							'name' => __( 'Inherit', 'avanam' ),
						),
						'1-1' => array(
							'name' => __( '1:1', 'avanam' ),
						),
						'3-4' => array(
							'name' => __( '4:3', 'avanam' ),
						),
						'2-3' => array(
							'name' => __( '3:2', 'avanam' ),
						),
						'9-16' => array(
							'name' => __( '16:9', 'avanam' ),
						),
						'1-2' => array(
							'name' => __( '1:2', 'avanam' ),
						),
					),
					'responsive' => false,
					'class' => 'base-three-col-short',
				),
			);
		}
		if ( post_type_supports( $post_type_name, 'comments' ) ) {
			$add_extras = true;
			$extras[ $post_type_name . '_comments' ] = array(
				'control_type' => 'base_switch_control',
				'sanitize'     => 'base_sanitize_toggle',
				'section'      => $post_type_name . '_layout',
				'priority'     => 20,
				'default'      => webapp()->default( $post_type_name . '_comments' ),
				'label'        => esc_html__( 'Show Comments?', 'avanam' ),
				'transport'    => 'refresh',
			);
		}
		if ( $add_extras ) {
			$settings = array_merge(
				$settings,
				$extras
			);
		}
		// Archives
		$base_custom_post_type_archive_settings = array(
			$post_type_name . '_archive_tabs' => array(
				'control_type' => 'base_tab_control',
				'section'      => $post_type_name . '_archive',
				'settings'     => false,
				'priority'     => 1,
				'input_attrs'  => array(
					'general' => array(
						'label'  => __( 'General', 'avanam' ),
						'target' => $post_type_name . '_archive',
					),
					'design' => array(
						'label'  => __( 'Design', 'avanam' ),
						'target' => $post_type_name . '_archive_design',
					),
					'active' => 'general',
				),
			),
			$post_type_name . '_archive_tabs_design' => array(
				'control_type' => 'base_tab_control',
				'section'      => $post_type_name . '_archive_design',
				'settings'     => false,
				'priority'     => 1,
				'input_attrs'  => array(
					'general' => array(
						'label'  => __( 'General', 'avanam' ),
						'target' => $post_type_name . '_archive',
					),
					'design' => array(
						'label'  => __( 'Design', 'avanam' ),
						'target' => $post_type_name . '_archive_design',
					),
					'active' => 'design',
				),
			),
			'info_' . $post_type_name . '_archive_title' => array(
				'control_type' => 'base_title_control',
				'section'      => $post_type_name . '_archive',
				'priority'     => 2,
				'label'        => esc_html__( 'Archive Title', 'avanam' ),
				'settings'     => false,
			),
			'info_' . $post_type_name . '_archive_title_design' => array(
				'control_type' => 'base_title_control',
				'section'      => $post_type_name . '_archive_design',
				'priority'     => 2,
				'label'        => esc_html__( 'Archive Title', 'avanam' ),
				'settings'     => false,
			),
			$post_type_name . '_archive_title' => array(
				'control_type' => 'base_switch_control',
				'sanitize'     => 'base_sanitize_toggle',
				'section'      => $post_type_name . '_archive',
				'priority'     => 3,
				'default'      => webapp()->default( $post_type_name . '_archive_title', true ),
				'label'        => esc_html__( 'Show Archive Title?', 'avanam' ),
				'transport'    => 'refresh',
			),
			$post_type_name . '_archive_title_layout' => array(
				'control_type' => 'base_radio_icon_control',
				'section'      => $post_type_name . '_archive',
				'label'        => esc_html__( 'Archive Title Layout', 'avanam' ),
				'transport'    => 'refresh',
				'priority'     => 4,
				'default'      => webapp()->default( $post_type_name . '_archive_title_layout', 'above' ),
				'context'      => array(
					array(
						'setting'    => $post_type_name . '_archive_title',
						'operator'   => '=',
						'value'      => true,
					),
				),
				'input_attrs'  => array(
					'layout' => array(
						'normal' => array(
							'name' => __( 'In Content', 'avanam' ),
							'icon'    => 'incontent',
						),
						'above' => array(
							'name' => __( 'Above Content', 'avanam' ),
							'icon'    => 'abovecontent',
						),
					),
					'responsive' => false,
					'class'      => 'base-two-col',
				),
			),
			$post_type_name . '_archive_title_inner_layout' => array(
				'control_type' => 'base_radio_icon_control',
				'section'      => $post_type_name . '_archive',
				'priority'     => 4,
				'default'      => webapp()->default( $post_type_name . '_archive_title_inner_layout', 'standard' ),
				'label'        => esc_html__( 'Container Width', 'avanam' ),
				'context'      => array(
					array(
						'setting'    => $post_type_name . '_archive_title',
						'operator'   => '=',
						'value'      => true,
					),
					array(
						'setting'    => $post_type_name . '_archive_title_layout',
						'operator'   => '=',
						'value'      => 'above',
					),
				),
				'live_method'     => array(
					array(
						'type'     => 'class',
						'selector' => '.' . $post_type_name . '-archive-hero-section',
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
			$post_type_name . '_archive_title_align' => array(
				'control_type' => 'base_radio_icon_control',
				'section'      => $post_type_name . '_archive',
				'label'        => esc_html__( 'Archive Title Align', 'avanam' ),
				'priority'     => 4,
				'default'      => webapp()->default( $post_type_name . '_archive_title_align', array(
					'mobile'  => '',
					'tablet'  => '',
					'desktop' => '',
				) ),
				'context'      => array(
					array(
						'setting'    => $post_type_name . '_archive_title',
						'operator'   => '=',
						'value'      => true,
					),
				),
				'live_method'     => array(
					array(
						'type'     => 'class',
						'selector' => '.' . $post_type_name . '-archive-title',
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
			$post_type_name . '_archive_title_height' => array(
				'control_type' => 'base_range_control',
				'section'      => $post_type_name . '_archive',
				'priority'     => 5,
				'label'        => esc_html__( 'Container Min Height', 'avanam' ),
				'context'      => array(
					array(
						'setting'    => $post_type_name . '_archive_title',
						'operator'   => '=',
						'value'      => true,
					),
					array(
						'setting'    => $post_type_name . '_archive_title_layout',
						'operator'   => '=',
						'value'      => 'above',
					),
				),
				'live_method'     => array(
					array(
						'type'     => 'css',
						'selector' => '#inner-wrap .' . $post_type_name . '-archive-hero-section .entry-header',
						'property' => 'min-height',
						'pattern'  => '$',
						'key'      => 'size',
					),
				),
				'default'      => webapp()->default( $post_type_name . '_archive_title_height', array(
					'size' => array(
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					),
					'unit' => array(
						'mobile'  => 'px',
						'tablet'  => 'px',
						'desktop' => 'px',
					),
				) ),
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
			$post_type_name . '_archive_title_elements' => array(
				'control_type' => 'base_sorter_control',
				'section'      => $post_type_name . '_archive',
				'priority'     => 6,
				'default'      => webapp()->default( $post_type_name . '_archive_title_elements', array( 'breadcrumb', 'title', 'description' ) ),
				'label'        => esc_html__( 'Title Elements', 'avanam' ),
				'transport'    => 'refresh',
				'settings'     => array(
					'elements'    => $post_type_name . '_archive_title_elements',
					'title'       => $post_type_name . '_archive_title_element_title',
					'breadcrumb'  => $post_type_name . '_archive_title_element_breadcrumb',
					'description' => $post_type_name . '_archive_title_element_description',
				),
				'input_attrs'  => array(
					'groupe'   => $post_type_name . '_archive_title_elements',
					'defaults' => array(
						'title'      => webapp()->default( $post_type_name . '_archive_element_title', array(
							'enabled' => true,
						) ),
						'description'      => webapp()->default( $post_type_name . '_archive_title_element_description', array(
							'enabled' => true,
						) ),
						'breadcrumb'       => webapp()->default( $post_type_name . '_archive_title_element_breadcrumb', array(
							'enabled' => false,
							'show_title' => true,
						) ),
					),
				),
			),
			$post_type_name . '_archive_title_color' => array(
				'control_type' => 'base_color_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Title Color', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_archive_title_color', array(
					'color' => '',
				) ),
				'live_method'     => array(
					array(
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-archive-title h1',
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
			$post_type_name . '_archive_title_description_color' => array(
				'control_type' => 'base_color_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Description Colors', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_archive_title_description_color', array(
					'color' => '',
					'hover' => '',
				) ),
				'live_method'     => array(
					array(
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-archive-title .archive-description',
						'property' => 'color',
						'pattern'  => '$',
						'key'      => 'color',
					),
					array(
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-archive-title .archive-description a:hover',
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
			$post_type_name . '_archive_title_breadcrumb_color' => array(
				'control_type' => 'base_color_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Breadcrumb Colors', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_archive_title_breadcrumb_color', array(
					'color' => '',
					'hover' => '',
				) ),
				'live_method'     => array(
					array(
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-archive-title .base-breadcrumbs',
						'property' => 'color',
						'pattern'  => '$',
						'key'      => 'color',
					),
					array(
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-archive-title .base-breadcrumbs a:hover',
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
			$post_type_name . '_archive_title_background' => array(
				'control_type' => 'base_background_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Archive Title Background', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_archive_title_background', array(
					'desktop' => array(
						'color' => '',
					),
				) ),
				'context'      => array(
					array(
						'setting'    => $post_type_name . '_archive_title',
						'operator'   => '=',
						'value'      => true,
					),
					array(
						'setting'    => $post_type_name . '_archive_title_layout',
						'operator'   => '=',
						'value'      => 'above',
					),
				),
				'live_method'     => array(
					array(
						'type'     => 'css_background',
						'selector' => '#inner-wrap .' . $post_type_name . '-archive-hero-section .entry-hero-container-inner',
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'base',
					),
				),
				'input_attrs'  => array(
					'tooltip'  => __( 'Title Background', 'avanam' ),
				),
			),
			$post_type_name . '_archive_title_overlay_color' => array(
				'control_type' => 'base_color_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Background Overlay Color', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_archive_title_overlay_color', array(
					'color' => '',
				) ),
				'live_method'     => array(
					array(
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-archive-hero-section .hero-section-overlay',
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'color',
					),
				),
				'context'      => array(
					array(
						'setting'    => $post_type_name . '_archive_title',
						'operator'   => '=',
						'value'      => true,
					),
					array(
						'setting'    => $post_type_name . '_archive_title_layout',
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
			$post_type_name . '_archive_title_border' => array(
				'control_type' => 'base_borders_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Border', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_archive_title_border' ),
				'context'      => array(
					array(
						'setting'    => $post_type_name . '_archive_title',
						'operator'   => '=',
						'value'      => true,
					),
					array(
						'setting'    => $post_type_name . '_archive_title_layout',
						'operator'   => '=',
						'value'      => 'above',
					),
				),
				'settings'     => array(
					'border_top'    => $post_type_name . '_archive_title_top_border',
					'border_bottom' => $post_type_name . '_archive_title_bottom_border',
				),
				'live_method'     => array(
					$post_type_name . '_archive_title_top_border' => array(
						array(
							'type'     => 'css_border',
							'selector' => '.' . $post_type_name . '-archive-hero-section .entry-hero-container-inner',
							'pattern'  => '$',
							'property' => 'border-top',
							'key'      => 'border',
						),
					),
					$post_type_name . '_archive_title_bottom_border' => array( 
						array(
							'type'     => 'css_border',
							'selector' => '.' . $post_type_name . '-archive-hero-section .entry-hero-container-inner',
							'property' => 'border-bottom',
							'pattern'  => '$',
							'key'      => 'border',
						),
					),
				),
			),
			'info_' . $post_type_name . '_archive_layout' => array(
				'control_type' => 'base_title_control',
				'section'      => $post_type_name . '_archive',
				'priority'     => 10,
				'label'        => esc_html__( 'Archive Layout', 'avanam' ),
				'settings'     => false,
			),
			// 'info_' . $post_type_name . '_archive_layout_design' => array(
			// 	'control_type' => 'base_title_control',
			// 	'section'      => $post_type_name . '_archive_design',
			// 	'priority'     => 10,
			// 	'label'        => esc_html__( 'Archive Layout', 'avanam' ),
			// 	'settings'     => false,
			// ),
			$post_type_name . '_archive_layout' => array(
				'control_type' => 'base_radio_icon_control',
				'section'      => $post_type_name . '_archive',
				'label'        => esc_html__( 'Archive Layout', 'avanam' ),
				'transport'    => 'refresh',
				'priority'     => 10,
				'default'      => webapp()->default( $post_type_name . '_archive_layout' ),
				'input_attrs'  => array(
					'layout' => array(
						'normal' => array(
							'name' => __( 'Normal', 'avanam' ),
							'icon' => 'normal',
						),
						'narrow' => array(
							'name' => __( 'Narrow', 'avanam' ),
							'icon' => 'narrow',
						),
						'fullwidth' => array(
							'name' => __( 'Fullwidth', 'avanam' ),
							'icon' => 'fullwidth',
						),
						'left' => array(
							'name' => __( 'Left Sidebar', 'avanam' ),
							'icon' => 'leftsidebar',
						),
						'right' => array(
							'name' => __( 'Right Sidebar', 'avanam' ),
							'icon' => 'rightsidebar',
						),
					),
					'class'      => 'base-three-col',
					'responsive' => false,
				),
			),
			$post_type_name . '_archive_sidebar_id' => array(
				'control_type' => 'base_select_control',
				'section'      => $post_type_name . '_archive',
				'label'        => esc_html__( 'Archive Default Sidebar', 'avanam' ),
				'transport'    => 'refresh',
				'priority'     => 10,
				'default'      => webapp()->default( $post_type_name . '_archive_sidebar_id', 'sidebar-primary' ),
				'input_attrs'  => array(
					'options' => webapp()->sidebar_options(),
				),
			),
			$post_type_name . '_archive_content_style' => array(
				'control_type' => 'base_radio_icon_control',
				'section'      => $post_type_name . '_archive',
				'label'        => esc_html__( 'Content Style', 'avanam' ),
				'priority'     => 10,
				'default'      => webapp()->default( $post_type_name . '_archive_content_style', 'boxed' ),
				'live_method'     => array(
					array(
						'type'     => 'class',
						'selector' => 'body.post-type-archive-' . $post_type_name,
						'pattern'  => 'content-style-$',
						'key'      => '',
					),
				),
				'input_attrs'  => array(
					'layout' => array(
						'boxed' => array(
							'tooltip' => __( 'Boxed', 'avanam' ),
							'icon' => 'gridBoxed',
							'name' => __( 'Boxed', 'avanam' ),
						),
						'unboxed' => array(
							'tooltip' => __( 'Unboxed', 'avanam' ),
							'icon' => 'gridUnboxed',
							'name' => __( 'Unboxed', 'avanam' ),
						),
					),
					'responsive' => false,
					'class'      => 'base-two-col',
				),
			),
			$post_type_name . '_archive_columns' => array(
				'control_type' => 'base_radio_icon_control',
				'section'      => $post_type_name . '_archive',
				'priority'     => 10,
				'label'        => esc_html__( 'Archive Columns', 'avanam' ),
				'transport'    => 'refresh',
				'default'      => webapp()->default( $post_type_name . '_archive_columns', 3 ),
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
					),
					'responsive' => false,
				),
			),
			'info_' . $post_type_name . '_archive_item_layout' => array(
				'control_type' => 'base_title_control',
				'section'      => $post_type_name . '_archive',
				'priority'     => 12,
				'label'        => esc_html__( 'Post Item Layout', 'avanam' ),
				'settings'     => false,
			),
			$post_type_name . '_archive_elements' => array(
				'control_type' => 'base_sorter_control',
				'section'      => $post_type_name . '_archive',
				'priority'     => 12,
				'default'      => webapp()->default( $post_type_name . '_archive_elements', array( 'feature', 'title', 'meta', 'excerpt', 'readmore' ) ),
				'label'        => esc_html__( 'Item Elements', 'avanam' ),
				'transport'    => 'refresh',
				'settings'     => array(
					'elements'   => $post_type_name . '_archive_elements',
					'feature'    => $post_type_name . '_archive_element_feature',
					'categories' => $post_type_name . '_archive_element_categories',
					'title'      => $post_type_name . '_archive_element_title',
					'meta'       => $post_type_name . '_archive_element_meta',
					'excerpt'    => $post_type_name . '_archive_element_excerpt',
					'readmore'   => $post_type_name . '_archive_element_readmore',
				),
				'input_attrs'  => array(
					'groupe'   => $post_type_name . '_archive_elements',
					'sortable' => false,
					'defaults' => array(
						'feature'    => webapp()->default( $post_type_name . '_archive_element_feature', array(
							'enabled' => true,
							'ratio'   => '2-3',
							'size'    => 'medium_large',
						) ),
						'title'      => webapp()->default( $post_type_name . '_archive_element_title', array(
							'enabled' => true,
						) ),
						'categories'      => webapp()->default( $post_type_name . '_archive_element_categories', array(
							'enabled' => false,
							'style'   => 'normal',
							'divider' => 'vline',
							'taxonomy' => '',
						) ),
						'meta'       => webapp()->default( $post_type_name . '_archive_element_meta', array(
							'id'                     => 'meta',
							'enabled'                => false,
							'divider'                => 'dot',
							'author'                 => true,
							'authorLink'             => true,
							'authorImage'            => true,
							'authorImageSize'        => 25,
							'authorEnableLabel'      => true,
							'authorLabel'            => '',
							'date'                   => true,
							'dateTime'               => false,
							'dateEnableLabel'        => false,
							'dateLabel'              => '',
							'dateUpdated'            => false,
							'dateUpdatedTime'        => false,
							'dateUpdatedDifferent'   => false,
							'dateUpdatedEnableLabel' => false,
							'dateUpdatedLabel'       => '',
							'categories'             => false,
							'categoriesEnableLabel'  => false,
							'categoriesLabel'        => '',
							'comments'               => false,
							'commentsCondition'      => false,
						) ),
						'excerpt'    => webapp()->default( $post_type_name . '_archive_element_excerpt', array(
							'enabled'     => true,
							'words'       => 55,
							'fullContent' => false,
						) ),
						'readmore'   => webapp()->default( $post_type_name . '_archive_element_readmore', array(
							'enabled' => true,
						) ),
					),
					'dividers' => array(
						'dot' => array(
							'icon' => 'dot',
						),
						'slash' => array(
							'icon' => 'slash',
						),
						'dash' => array(
							'icon' => 'dash',
						),
						'vline' => array(
							'icon' => 'vline',
						),
						'customicon' => array(
							'icon' => 'hours',
						),
					),
					'taxonomies' => $taxs,
				),
			),
			$post_type_name . '_archive_item_image_placement' => array(
				'control_type' => 'base_radio_icon_control',
				'section'      => $post_type_name . '_archive',
				'label'        => esc_html__( 'Item Image Placement', 'avanam' ),
				'priority'     => 10,
				'default'      => webapp()->default( $post_type_name . '_archive_item_image_placement' ),
				'context'      => array(
					array(
						'setting' => $post_type_name . '_archive_columns',
						'operator'   => '=',
						'value'   => '1',
					),
				),
				'live_method'     => array(
					array(
						'type'     => 'class',
						'selector' => '.' . $post_type_name . '-archive.grid-cols',
						'pattern'  => 'item-image-style-$',
						'key'      => '',
					),
				),
				'input_attrs'  => array(
					'layout' => array(
						'beside' => array(
							'name' => __( 'Beside', 'avanam' ),
						),
						'above' => array(
							'name' => __( 'Above', 'avanam' ),
						),
					),
					'responsive' => false,
				),
			),
			$post_type_name . '_archive_item_title_font' => array(
				'control_type' => 'base_typography_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Post Item Title Font', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_archive_item_title_font', array(
					'size' => array(
						'desktop' => '',
					),
					'lineHeight' => array(
						'desktop' => '',
					),
					'family'  => '',
					'google'  => false,
					'weight'  => '',
					'variant' => '',
				) ),
				'live_method'     => array(
					array(
						'type'     => 'css_typography',
						'selector' => '.loop-entry.type-' . $post_type_name . ' h2.entry-title',
						'property' => 'font',
						'key'      => 'typography',
					),
				),
				'input_attrs'  => array(
					'id'             => $post_type_name . '_archive_item_title_font',
					'headingInherit' => true,
				),
			),
			$post_type_name . '_archive_item_meta_color' => array(
				'control_type' => 'base_color_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Item Meta Colors', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_archive_item_meta_color', array(
					'color' => '',
					'hover' => '',
				) ),
				'live_method'     => array(
					array(
						'type'     => 'css',
						'selector' => '.loop-entry.type-' . $post_type_name . ' .entry-meta',
						'property' => 'color',
						'pattern'  => '$',
						'key'      => 'color',
					),
					array(
						'type'     => 'css',
						'selector' => '.loop-entry.type-' . $post_type_name . ' .entry-meta a:hover',
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
			$post_type_name . '_archive_item_meta_font' => array(
				'control_type' => 'base_typography_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Item Meta Font', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_archive_item_meta_font', array(
					'size' => array(
						'desktop' => '',
					),
					'lineHeight' => array(
						'desktop' => '',
					),
					'family'  => 'inherit',
					'google'  => false,
					'weight'  => '',
					'variant' => '',
				) ),
				'live_method'     => array(
					array(
						'type'     => 'css_typography',
						'selector' => '.loop-entry.type-' . $post_type_name . ' .entry-meta',
						'property' => 'font',
						'key'      => 'typography',
					),
				),
				'input_attrs'  => array(
					'id'      => $post_type_name . '_archive_item_meta_font',
					'options' => 'no-color',
				),
			),
			$post_type_name . '_archive_background' => array(
				'control_type' => 'base_background_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Site Background', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_archive_background' ),
				'live_method'     => array(
					array(
						'type'     => 'css_background',
						'selector' => 'body.post-type-archive-' . $post_type_name,
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'base',
					),
				),
				'input_attrs'  => array(
					'tooltip' => __( 'Archive Background', 'avanam' ),
				),
			),
			$post_type_name . '_archive_content_background' => array(
				'control_type' => 'base_background_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Content Background', 'avanam' ),
				'default'      => webapp()->default( $post_type_name . '_archive_content_background' ),
				'live_method'  => array(
					array(
						'type'     => 'css_background',
						'selector' => 'body.post-type-archive-' . $post_type_name . ' .content-bg, body.post-type-archive-' . $post_type_name . '.content-style-unboxed .site',
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
		Theme_Customizer::add_settings( $base_custom_post_type_archive_settings );
		Theme_Customizer::add_settings( $settings );
	}
}
Theme_Customizer::add_settings(
	array( 
		'info_custom_post_types_placehoder' => array(
			'control_type' => 'base_title_control',
			'section'      => 'custom_posts_placeholder',
			'priority'     => 10,
			'label'        => esc_html__( 'Custom Post Types', 'avanam' ),
			'settings'     => false,
		),
	)
);
