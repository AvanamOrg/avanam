<?php
/**
 * Calls in Templates using theme hooks.
 *
 * @package Base
 */

namespace Base;

use function Base\webapp;
use function get_template_part;
use function add_action;

defined( 'ABSPATH' ) || exit;

/**
 * Main Call for Base Header
 */
function header_markup() {
	if ( webapp()->has_header() ) {
		get_template_part( 'template-parts/header/base' );
	}
}

/**
 * Header Top Row
 */
function top_header() {
	if ( webapp()->display_header_row( 'top' ) ) {
		webapp()->get_template( 'template-parts/header/header', 'row', array( 'row' => 'top' ) );
	}
}

/**
 * Header Main Row
 */
function main_header() {
	if ( webapp()->display_header_row( 'main' ) ) {
		webapp()->get_template( 'template-parts/header/header', 'row', array( 'row' => 'main' ) );
	}
}

/**
 * Header Bottom Row
 */
function bottom_header() {
	if ( webapp()->display_header_row( 'bottom' ) ) {
		webapp()->get_template( 'template-parts/header/header', 'row', array( 'row' => 'bottom' ) );
	}
}

/**
 * Header Column
 *
 * @param string $row the column row.
 * @param string $column the row column.
 */
function header_column( $row, $column ) {
	webapp()->render_header( $row, $column );
}

/**
 * Mobile Header
 */
function mobile_header() {
	get_template_part( 'template-parts/header/mobile' );
}

/**
 * Mobile Header Top Row
 */
function mobile_top_header() {
	if ( webapp()->display_mobile_header_row( 'top' ) ) {
		webapp()->get_template( 'template-parts/header/mobile-header', 'row', array( 'mobile_row' => 'top' ) );
	}
}

/**
 * Mobile Header Main Row
 */
function mobile_main_header() {
	if ( webapp()->display_mobile_header_row( 'main' ) ) {
		webapp()->get_template( 'template-parts/header/mobile-header', 'row', array( 'mobile_row' => 'main' ) );
	}
}

/**
 * Mobile Header Bottom Row
 */
function mobile_bottom_header() {
	if ( webapp()->display_mobile_header_row( 'bottom' ) ) {
		webapp()->get_template( 'template-parts/header/mobile-header', 'row', array( 'mobile_row' => 'bottom' ) );
	}
}

/**
 * Mobile Header Column
 *
 * @param string $row the header row.
 * @param string $column the row column.
 */
function mobile_header_column( $row, $column ) {
	webapp()->render_header( $row, $column, 'mobile' );
}

/**
 * Header Row Class.
 *
 * @param string $row the header row.
 */
function header_row_class( $row ) {
	$classes = 'site-' . esc_attr( $row ) . '-header-wrap site-header-row-container site-header-focus-item site-header-row-layout-' . esc_attr( webapp()->sub_option( 'header_' . $row . '_layout', 'desktop' ) ) . esc_attr( webapp()->option( 'header_sticky' ) === $row ? ' base-sticky-header' : '' );
	return apply_filters( 'base-header-row-class-string', $classes );
}

/**
 * Desktop Site Branding
 */
function site_branding() {
	$layout   = webapp()->option( 'logo_layout' );
	$includes = array();
	$layouts  = array();
	if ( is_array( $layout ) && isset( $layout['include'] ) ) {
		if ( isset( $layout['layout'] ) ) {
			if ( isset( $layout['layout']['desktop'] ) && ! empty( $layout['layout']['desktop'] ) ) {
				$layouts['desktop'] = $layout['layout']['desktop'];
			}
		}
		if ( isset( $layout['include']['desktop'] ) && ! empty( $layout['include']['desktop'] ) ) {
			if ( strpos( $layout['include']['desktop'], 'logo' ) !== false ) {
				if ( ! in_array( 'logo', $includes, true ) ) {
					$includes[] = 'logo';
				}
			}
			if ( strpos( $layout['include']['desktop'], 'title' ) !== false ) {
				if ( ! in_array( 'title', $includes, true ) ) {
					$includes[] = 'title';
				}
			}
			if ( strpos( $layout['include']['desktop'], 'tagline' ) !== false ) {
				if ( ! in_array( 'tagline', $includes, true ) ) {
					$includes[] = 'tagline';
				}
			}
		}
	}
	$layout_slug = isset( $layouts['desktop'] ) ? $layouts['desktop'] : 'standard';
	if ( 'title_logo' === $layout_slug || 'title_tag_logo' === $layout_slug ) {
		$layout_class = 'standard-reverse';
	} elseif ( 'top_logo_title' === $layout_slug || 'top_logo_title_tag' === $layout_slug ) {
		$layout_class = 'vertical';
	} elseif ( 'top_title_logo' === $layout_slug || 'top_title_tag_logo' === $layout_slug ) {
		$layout_class = 'vertical-reverse';
	} elseif ( 'top_title_logo_tag' === $layout_slug ) {
		$layout_class = 'vertical site-title-top';
	} elseif ( 'standard' === $layout_slug && ! in_array( 'title', $includes, true ) ) {
		$layout_class = 'standard site-brand-logo-only';
	} else {
		$layout_class = 'standard';
	}

	echo '<div class="site-branding branding-layout-' . esc_attr( $layout_class ) . '">';
	webapp()->customizer_quick_link();

	$has_desktop_tagline_and_logo = in_array( 'tagline', $includes, true ) && ( ! isset( $layouts['desktop'] ) || ( isset( $layouts['desktop'] ) && 'top_title_logo_tag' !== $layouts['desktop'] ) );

	echo '<a class="brand' . ( in_array( 'logo', $includes, true ) && ( webapp()->option( 'custom_logo' ) || ( ! webapp()->option( 'custom_logo' ) && webapp()->option( 'use_logo_icon' ) && webapp()->option( 'logo_icon' ) ) ) ? ' has-logo-image' : '' ) . ( in_array( 'logo', $includes, true ) && 'no' !== webapp()->option( 'header_sticky' ) && webapp()->option( 'header_sticky_custom_logo' ) && webapp()->option( 'header_sticky_logo' ) ? ' has-sticky-logo' : '' ) . '" href="' . esc_url( apply_filters( 'base_logo_url', home_url( '/' ) ) ) . '" rel="home" aria-label="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
	foreach ( $includes as $include ) {
		switch ( $include ) {
			case 'logo':
				do_action( 'before_base_logo_output' );
				if ( webapp()->desk_transparent_header() && webapp()->option( 'transparent_header_custom_logo' ) && webapp()->option( 'transparent_header_logo' ) ) {
					render_custom_logo( 'transparent_header_logo', 'base-transparent-logo' );
				} else if ( ! webapp()->option( 'custom_logo' ) && webapp()->option( 'use_logo_icon' ) && webapp()->option( 'logo_icon' ) ) {
					logo_icon();
				} else {
					custom_logo();
				}
				if ( 'no' !== webapp()->option( 'header_sticky' ) && webapp()->option( 'header_sticky_custom_logo' ) && webapp()->option( 'header_sticky_logo' ) ) {
					render_custom_logo( 'header_sticky_logo', 'base-sticky-logo' );
				}
				break;
			case 'title':
				echo '<div class="site-title-wrap">';
				echo '<p class="site-title">' . get_bloginfo( 'name' ) . '</p>'; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
				if ( $has_desktop_tagline_and_logo ) {
					echo '<p class="site-description">' . get_bloginfo( 'description' ) . '</p>'; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
				}
				echo '</div>';
				break;
			case 'tagline':
				if ( isset( $layouts['desktop'] ) && 'top_title_logo_tag' === $layouts['desktop'] ) {
					echo '<p class="site-description">' . get_bloginfo( 'description' ) . '</p>'; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
				}
				break;
		}
	}
	echo '</a>';
	echo '</div>';
}
/**
 * Logo Icon Render
 */
function logo_icon() {
	echo '<span class="logo-icon">';
	$icon = webapp()->option( 'logo_icon' );
	if ( 'custom' === $icon ) {
		echo webapp()->option( 'logo_icon_custom' ); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
	} else {
		webapp()->print_icon( $icon, '', false );
	}
	echo '</span>';
}
/**
 * Desktop Navigation
 */
function primary_navigation() {
	?>
	<nav id="site-navigation" class="main-navigation header-navigation nav--toggle-sub header-navigation-style-<?php echo esc_attr( webapp()->option( 'primary_navigation_style' ) ); ?> header-navigation-dropdown-animation-<?php echo esc_attr( webapp()->option( 'dropdown_navigation_reveal' ) ); ?>" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'avanam' ); ?>">
		<?php webapp()->customizer_quick_link(); ?>
		<div class="primary-menu-container header-menu-container">
			<?php
			if ( webapp()->is_primary_nav_menu_active() ) {
				webapp()->display_primary_nav_menu( array( 'menu_id' => 'primary-menu' ) );
			} else {
				webapp()->display_fallback_menu();
			}
			?>
		</div>
	</nav><!-- #site-navigation -->
	<?php
}

/**
 * Desktop Navigation
 */
function secondary_navigation() {
	?>
	<nav id="secondary-navigation" class="secondary-navigation header-navigation nav--toggle-sub header-navigation-style-<?php echo esc_attr( webapp()->option( 'secondary_navigation_style' ) ); ?> header-navigation-dropdown-animation-<?php echo esc_attr( webapp()->option( 'dropdown_navigation_reveal' ) ); ?>" role="navigation" aria-label="<?php esc_attr_e( 'Secondary Navigation', 'avanam' ); ?>">
		<?php webapp()->customizer_quick_link(); ?>
		<div class="secondary-menu-container header-menu-container">
			<?php
			if ( webapp()->is_secondary_nav_menu_active() ) {
				webapp()->display_secondary_nav_menu( array( 'menu_id' => 'secondary-menu' ) );
			} else {
				webapp()->display_fallback_menu();
			}
			?>
		</div>
	</nav><!-- #secondary-navigation -->
	<?php
}

/**
 * Output custom logo
 *
 * @param string $option_string the image option id string
 * @param string $custom_class the image custom class.
 */
function render_custom_logo( $option_string = '', $custom_class = 'extra-custom-logo' ) {
	$html = '';
	if ( empty( $option_string ) ) {
		return;
	}
	$custom_logo_id = webapp()->option( $option_string );

	// We have a logo. Logo is go.
	if ( $custom_logo_id ) {
		$custom_logo_attr = array(
			'class' => 'custom-logo ' . $custom_class,
			'loading' => false,
		);

		/*
		* If the logo alt attribute is empty, get the site title and explicitly
		* pass it to the attributes used by wp_get_attachment_image().
		*/
		$image_alt = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true );
		if ( empty( $image_alt ) ) {
			$custom_logo_attr['alt'] = get_bloginfo( 'name', 'display' );
		}
		$type = get_post_mime_type( $custom_logo_id );
		if ( isset( $type ) && 'image/svg+xml' === $type ) {
			$custom_logo_attr['class'] = 'custom-logo ' . $custom_class . ' svg-logo-image';
		}

		/*
		* If the alt attribute is not empty, there's no need to explicitly pass
		* it because wp_get_attachment_image() already adds the alt attribute.
		*/
		$html = wp_get_attachment_image( $custom_logo_id, 'full', false, $custom_logo_attr );
	} elseif ( is_customize_preview() ) {
		// If no logo is set but we're in the Customizer, leave a placeholder (needed for the live preview).
		$html = '<img class="custom-logo"/></a>';
	}
	/**
	 * Filters the custom logo output.
	 *
	 * @param string $html    Custom logo HTML output.
	 * @param string $option_string the ID of the logo option.
	 */
	echo apply_filters( 'base_extra_custom_logo', $html, $option_string ); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
}

/**
 * Output custom logo
 *
 * @param integer $blog_id the site ID for multisites.
 */
function custom_logo( $blog_id = 0 ) {
	$html          = '';
	$switched_blog = false;

	if ( is_multisite() && ! empty( $blog_id ) && get_current_blog_id() !== (int) $blog_id ) {
		switch_to_blog( $blog_id );
		$switched_blog = true;
	}

	$custom_logo_id = webapp()->option( 'custom_logo' );

	// We have a logo. Logo is go.
	if ( $custom_logo_id ) {
		$custom_logo_attr = array(
			'class' => 'custom-logo',
			'loading' => false,
		);

		/*
		* If the logo alt attribute is empty, get the site title and explicitly
		* pass it to the attributes used by wp_get_attachment_image().
		*/
		$image_alt = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true );
		if ( empty( $image_alt ) ) {
			$custom_logo_attr['alt'] = get_bloginfo( 'name', 'display' );
		}

		$type = get_post_mime_type( $custom_logo_id );
		if ( isset( $type ) && 'image/svg+xml' === $type ) {
			$custom_logo_attr['class'] = 'custom-logo svg-logo-image';
		}

		/*
		* If the alt attribute is not empty, there's no need to explicitly pass
		* it because wp_get_attachment_image() already adds the alt attribute.
		*/
		$html = wp_get_attachment_image( $custom_logo_id, 'full', false, $custom_logo_attr );
	} elseif ( is_customize_preview() ) {
		// If no logo is set but we're in the Customizer, leave a placeholder (needed for the live preview).
		$html = '<img class="custom-logo"/></a>';
	}

	if ( $switched_blog ) {
		restore_current_blog();
	}
	/**
	 * Filters the custom logo output.
	 *
	 * @since 4.5.0
	 * @since 4.6.0 Added the `$blog_id` parameter.
	 *
	 * @param string $html    Custom logo HTML output.
	 * @param int    $blog_id ID of the blog to get the custom logo for.
	 */
	echo apply_filters( 'base_custom_logo', $html, $blog_id ); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
}

/**
 * Mobile Site Branding
 */
function mobile_site_branding() {
	$layout   = webapp()->option( 'logo_layout' );
	$includes = array();
	$layouts  = array();
	if ( is_array( $layout ) && isset( $layout['include'] ) ) {
		foreach ( array( 'mobile', 'tablet', 'desktop' ) as $device ) {
			if ( isset( $layout['layout'] ) ) {
				if ( isset( $layout['layout'][ $device ] ) && ! empty( $layout['layout'][ $device ] ) ) {
					$layouts[ $device ] = $layout['layout'][ $device ];
				}
			}
			// if ( 'desktop' === $device && ! empty( $includes ) ) {
			// 	continue;
			// }
			if ( isset( $layout['include'][ $device ] ) && ! empty( $layout['include'][ $device ] ) ) {
				if ( strpos( $layout['include'][ $device ], 'logo' ) !== false ) {
					if ( ! in_array( 'logo', $includes, true ) ) {
						$includes[] = 'logo';
					}
				}
				if ( strpos( $layout['include'][ $device ], 'title' ) !== false ) {
					if ( ! in_array( 'title', $includes, true ) ) {
						$includes[] = 'title';
					}
				}
				if ( strpos( $layout['include'][ $device ], 'tagline' ) !== false ) {
					if ( ! in_array( 'tagline', $includes, true ) ) {
						$includes[] = 'tagline';
					}
				}
			}
		}
	}
	if ( isset( $layouts['tablet'] ) ) {
		if ( 'title_logo' === $layouts['tablet'] || 'title_tag_logo' === $layouts['tablet'] ) {
			$tab_layout_class = 'standard-reverse';
		} elseif ( 'top_logo_title' === $layouts['tablet'] || 'top_logo_title_tag' === $layouts['tablet'] ) {
			$tab_layout_class = 'vertical';
		} elseif ( 'top_title_logo' === $layouts['tablet'] || 'top_title_tag_logo' === $layouts['tablet'] ) {
			$tab_layout_class = 'vertical-reverse';
		} elseif ( 'top_title_logo_tag' === $layouts['tablet'] ) {
			$tab_layout_class = 'vertical site-title-top';
		} elseif ( 'standard' === $layouts['tablet'] && ! in_array( 'title', $includes, true ) ) {
			$tab_layout_class = 'standard site-brand-logo-only';
		} elseif ( 'standard' === $layouts['tablet'] ) {
			$tab_layout_class = 'standard';
		} else {
			if ( ! in_array( 'title', $includes, true ) ) {
				$tab_layout_class = 'inherit site-brand-logo-only';
			} else {
				$tab_layout_class = 'inherit';
			}
		}
	} else {
		if ( ! in_array( 'title', $includes, true ) ) {
			$tab_layout_class = 'inherit site-brand-logo-only';
		} else {
			$tab_layout_class = 'inherit';
		}
	}
	if ( isset( $layouts['mobile'] ) ) {
		if ( 'title_logo' === $layouts['mobile'] || 'title_tag_logo' === $layouts['mobile'] ) {
			$mobile_layout_class = 'standard-reverse';
		} elseif ( 'top_logo_title' === $layouts['mobile'] || 'top_logo_title_tag' === $layouts['mobile'] ) {
			$mobile_layout_class = 'vertical';
		} elseif ( 'top_title_logo' === $layouts['mobile'] || 'top_title_tag_logo' === $layouts['mobile'] ) {
			$mobile_layout_class = 'vertical-reverse';
		} elseif ( 'top_title_logo_tag' === $layouts['mobile'] ) {
			$mobile_layout_class = 'vertical site-title-top';
		} elseif ( 'standard' === $layouts['mobile'] && ! in_array( 'title', $includes, true ) ) {
			$mobile_layout_class = 'standard site-brand-logo-only';
		} elseif ( 'standard' === $layouts['mobile'] ) {
			$mobile_layout_class = 'standard';
		} else {
			$mobile_layout_class = 'inherit';
		}
	} else {
		$mobile_layout_class = 'inherit';
	}

	$has_mobile_tagline_and_logo = in_array( 'tagline', $includes, true ) && ( ! isset( $layouts['mobile'] ) || ( isset( $layouts['mobile'] ) && 'top_title_logo_tag' !== $layouts['mobile'] ) );

	echo '<div class="site-branding mobile-site-branding branding-layout-' . esc_attr( isset( $layouts['desktop'] ) ? $layouts['desktop'] : 'standard' ) . ' branding-tablet-layout-' . esc_attr( $tab_layout_class ) . ' branding-mobile-layout-' . esc_attr( $mobile_layout_class ) . '">';
	echo '<a class="brand' . ( in_array( 'logo', $includes, true ) && ( webapp()->option( 'custom_logo' ) || ( ! webapp()->option( 'custom_logo' ) && webapp()->option( 'use_logo_icon' ) && webapp()->option( 'logo_icon' ) ) ) ? ' has-logo-image' : '' ) . ( in_array( 'logo', $includes, true ) && 'no' !== webapp()->option( 'mobile_header_sticky' ) && ( webapp()->option( 'header_sticky_custom_mobile_logo' ) && webapp()->option( 'header_sticky_mobile_logo' ) || webapp()->option( 'header_sticky_custom_logo' ) && webapp()->option( 'header_sticky_logo' ) ) ? ' has-sticky-logo' : '' ) . '" href="' . esc_url( apply_filters( 'base_logo_url', home_url( '/' ) ) ) . '" rel="home" aria-label="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
	foreach ( $includes as $include ) {
		switch ( $include ) {
			case 'logo':
				do_action( 'before_base_mobile_logo_output' );
				if ( webapp()->mobile_transparent_header() && webapp()->option( 'transparent_header_custom_mobile_logo' ) && webapp()->option( 'transparent_header_mobile_logo' ) ) {
					render_custom_logo( 'transparent_header_mobile_logo', 'base-transparent-logo' );
				} elseif ( webapp()->mobile_transparent_header() && webapp()->option( 'transparent_header_custom_logo' ) && webapp()->option( 'transparent_header_logo' ) ) {
					render_custom_logo( 'transparent_header_logo', 'base-transparent-logo' );
				} else if ( ! webapp()->option( 'use_mobile_logo' ) && ! webapp()->option( 'custom_logo' ) && webapp()->option( 'use_logo_icon' ) && webapp()->option( 'logo_icon' ) ) {
					logo_icon();
				} else {
					if ( webapp()->option( 'use_mobile_logo' ) && webapp()->option( 'mobile_logo' ) ) {
						render_custom_logo( 'mobile_logo' );
					} else {
						custom_logo();
					}
				}
				if ( 'no' !== webapp()->option( 'mobile_header_sticky' ) && webapp()->option( 'header_sticky_custom_mobile_logo' ) && webapp()->option( 'header_sticky_mobile_logo' ) ) {
					render_custom_logo( 'header_sticky_mobile_logo', 'base-sticky-logo' );
				} elseif ( 'no' !== webapp()->option( 'mobile_header_sticky' ) && webapp()->option( 'header_sticky_custom_logo' ) && webapp()->option( 'header_sticky_logo' ) ) {
					render_custom_logo( 'header_sticky_logo', 'base-sticky-logo' );
				}
				break;
			case 'title':
				echo '<div class="site-title-wrap">';
				echo '<div class="site-title' . ( ! empty( $layout['include']['mobile'] ) && ( strpos( $layout['include']['mobile'], 'title' ) === false ) ? ' vs-sm-false' : '' ) . ( ( strpos( $layout['include']['tablet'], 'title' ) === false ) ? ' vs-md-false' : '' ) . '">' . get_bloginfo( 'name' ) . '</div>'; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
				if ( $has_mobile_tagline_and_logo ) {
					echo '<div class="site-description' . ( ( strpos( $layout['include']['mobile'], 'tagline' ) === false ) ? ' vs-sm-false' : '' ) . ( ( strpos( $layout['include']['tablet'], 'tagline' ) === false ) ? ' vs-md-false' : '' ) . '">' . get_bloginfo( 'description' ) . '</div>'; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
				}
				echo '</div>';
				break;
			case 'tagline':
				if ( isset( $layouts['desktop'] ) && 'top_title_logo_tag' === $layouts['desktop'] ) {
					echo '<div class="site-description' . ( ( strpos( $layout['include']['mobile'], 'tagline' ) === false ) ? ' vs-sm-false' : '' ) . ( ( strpos( $layout['include']['tablet'], 'tagline' ) === false ) ? ' vs-md-false' : '' ) . '">' . get_bloginfo( 'description' ) . '</div>'; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
				}
				break;
		}
	}
	echo '</a>';
	echo '</div>';
}
/**
 * Mobile Navigation Popup Toggle
 */
function navigation_popup_toggle() {
	add_action( 'wp_footer', 'Base\navigation_popup' );
	?>
	<div class="mobile-toggle-open-container">
		<?php webapp()->customizer_quick_link(); ?>
		<?php
		if ( webapp()->is_amp() ) {
			?>
			<amp-state id="siteNavigationMenu">
				<script type="application/json">
					{
						"expanded": false
					}
				</script>
			</amp-state>
			<?php
		}
		?>
		<button id="mobile-toggle" class="menu-toggle-open drawer-toggle menu-toggle-style-<?php echo esc_attr( webapp()->option( 'mobile_trigger_style' ) ); ?>" aria-label="<?php esc_attr_e( 'Open menu', 'avanam' ); ?>" data-toggle-target="#mobile-drawer" data-toggle-body-class="showing-popup-drawer-from-<?php echo esc_attr( 'sidepanel' === webapp()->option( 'header_popup_layout' ) ? webapp()->option( 'header_popup_side' ) : 'full' ); ?>" aria-expanded="false" data-set-focus=".menu-toggle-close"
			<?php
			if ( webapp()->is_amp() ) {
				?>
				[class]=" siteNavigationMenu.expanded ? 'menu-toggle-open drawer-toggle menu-toggle-style-<?php echo esc_attr( webapp()->option( 'mobile_trigger_style' ) ); ?> active' : 'menu-toggle-open drawer-toggle menu-toggle-style-<?php echo esc_attr( webapp()->option( 'mobile_trigger_style' ) ); ?>' "
				on="tap:AMP.setState( { siteNavigationMenu: { expanded: ! siteNavigationMenu.expanded } } )"
				[aria-expanded]="siteNavigationMenu.expanded ? 'true' : 'false'"
				<?php
			}
			?>
		>
			<?php
			$label = webapp()->option( 'mobile_trigger_label' );
			if ( ! empty( $label ) || is_customize_preview() ) {
				?>
				<span class="menu-toggle-label"><?php echo esc_html( $label ); ?></span>
				<?php
			}
			?>
			<span class="menu-toggle-icon"><?php popup_toggle(); ?></span>
		</button>
	</div>
	<?php
}
add_action( 'base_navigation_popup_toggle', 'Base\navigation_popup_toggle' );
/**
 * Mobile Navigation Popup Toggle
 */
function popup_toggle() {
	$icon = webapp()->option( 'mobile_trigger_icon' );
	webapp()->print_icon( $icon, '', false );
}
/**
 * Mobile Navigation Popup Toggle
 */
function navigation_popup() {
	?>
	<div id="mobile-drawer" class="popup-drawer popup-drawer-layout-<?php echo esc_attr( webapp()->option( 'header_popup_layout' ) ); ?> popup-drawer-animation-<?php echo esc_attr( webapp()->option( 'header_popup_animation' ) ); ?> popup-drawer-side-<?php echo esc_attr( webapp()->option( 'header_popup_side' ) ); ?>" data-drawer-target-string="#mobile-drawer"
		<?php
		if ( webapp()->is_amp() ) {
			?>
			[class]=" siteNavigationMenu.expanded ? 'popup-drawer popup-drawer-layout-<?php echo esc_attr( webapp()->option( 'header_popup_layout' ) ); ?> popup-drawer-side-<?php echo esc_attr( webapp()->option( 'header_popup_side' ) ); ?> show-drawer active' : 'popup-drawer popup-drawer-layout-<?php echo esc_attr( webapp()->option( 'header_popup_layout' ) ); ?> popup-drawer-side-<?php echo esc_attr( webapp()->option( 'header_popup_side' ) ); ?>' "
			<?php
		}
		?>
	>
		<div class="drawer-overlay" data-drawer-target-string="#mobile-drawer"></div>
		<div class="drawer-inner">
			<?php
			if ( 'fullwidth' === webapp()->option( 'header_popup_layout' ) && 'slice' === webapp()->option( 'header_popup_animation' ) ) {
				echo '<div class="pop-slice-background"><div class="pop-portion-bg"></div><div class="pop-portion-bg"></div><div class="pop-portion-bg"></div></div>';
			}
			?>
			<div class="drawer-header">
				<button class="menu-toggle-close drawer-toggle" aria-label="<?php esc_attr_e( 'Close menu', 'avanam' ); ?>"  data-toggle-target="#mobile-drawer" data-toggle-body-class="showing-popup-drawer-from-<?php echo esc_attr( 'sidepanel' === webapp()->option( 'header_popup_layout' ) ? webapp()->option( 'header_popup_side' ) : 'full' ); ?>" aria-expanded="false" data-set-focus=".menu-toggle-open"
				<?php
					if ( webapp()->is_amp() ) {
						?>
						on="tap:AMP.setState( { siteNavigationMenu: { expanded: ! siteNavigationMenu.expanded } } )"
						[aria-expanded]="siteNavigationMenu.expanded ? 'true' : 'false'"
						<?php
					}
				?>
			>
					<span class="toggle-close-bar"></span>
					<span class="toggle-close-bar"></span>
				</button>
			</div>
			<div class="drawer-content mobile-drawer-content content-align-<?php echo esc_attr( webapp()->option( 'header_popup_content_align' ) ); ?> content-valign-<?php echo esc_attr( webapp()->option( 'header_popup_vertical_align' ) ); ?>">
				<?php do_action( 'base_before_mobile_navigation_popup' ); ?>
				<?php webapp()->render_header( 'popup', 'content', 'mobile' ); ?>
				<?php do_action( 'base_after_mobile_navigation_popup' ); ?>
			</div>
		</div>
	</div>
	<?php
}
/**
 * Mobile Navigation
 */
function mobile_navigation() {
	?>
	<nav id="mobile-site-navigation" class="mobile-navigation drawer-navigation drawer-navigation-parent-toggle-<?php echo esc_attr( webapp()->option( 'mobile_navigation_parent_toggle' ) ? 'true' : 'false' ); ?>" role="navigation" aria-label="<?php esc_attr_e( 'Primary Mobile Navigation', 'avanam' ); ?>">
		<?php webapp()->customizer_quick_link(); ?>
		<div class="mobile-menu-container drawer-menu-container">
			<?php
			if ( webapp()->is_mobile_nav_menu_active() ) {
				webapp()->display_mobile_nav_menu( array( 'menu_id' => 'mobile-menu', 'menu_class' => ( webapp()->option( 'mobile_navigation_collapse' ) ? 'menu has-collapse-sub-nav' : 'menu' ) ) );
			} elseif ( webapp()->is_primary_nav_menu_active() ) {
				webapp()->display_primary_nav_menu( array( 'menu_id' => 'mobile-menu', 'menu_class' => ( webapp()->option( 'mobile_navigation_collapse' ) ? 'menu has-collapse-sub-nav' : 'menu' ), 'show_toggles' => ( webapp()->option( 'mobile_navigation_collapse' ) ? true : false ), 'sub_arrows' => false, 'mega_support' => apply_filters( 'base_mobile_allow_mega_support', true ) ) );
			} else {
				webapp()->display_fallback_menu();
			}
			?>
		</div>
	</nav><!-- #site-navigation -->
	<?php
}

/**
 * Desktop HTML
 */
function header_html() {
	$content = webapp()->option( 'header_html_content' );
	if ( $content || is_customize_preview() ) {
		$link_style = webapp()->option( 'header_html_link_style' );
		$wpautop    = webapp()->option( 'header_html_wpautop' );
		echo '<div class="header-html inner-link-style-' . esc_attr( $link_style ) . '">';
		webapp()->customizer_quick_link();
		echo '<div class="header-html-inner">';
		if ( $wpautop ) {
			echo do_shortcode( wpautop( $content ) );
		} else {
			$array = array(
				'<p>[' => '[',
				']</p>' => ']',
				'<p></p>' => '',
				']<br />' => ']',
				'<br />[' => '[',
			);
			$content = strtr( $content, $array );
			echo do_shortcode( $content );
		}
		echo '</div>';
		echo '</div>';
	}
}

/**
 * Mobile HTML
 */
function mobile_html() {
	$content = webapp()->option( 'mobile_html_content' );
	if ( $content || is_customize_preview() ) {
		$link_style = webapp()->option( 'mobile_html_link_style' );
		$wpautop    = webapp()->option( 'mobile_html_wpautop' );
		echo '<div class="mobile-html inner-link-style-' . esc_attr( $link_style ) . '">';
		webapp()->customizer_quick_link();
		echo '<div class="mobile-html-inner">';
		if ( $wpautop ) {
			echo do_shortcode( wpautop( $content ) );
		} else {
			echo do_shortcode( $content );
		}
		echo '</div>';
		echo '</div>';
	}
}
/**
 * Desktop Button
 */
function header_button() {
	$label = webapp()->option( 'header_button_label' );
	if ( 'loggedin' === webapp()->option( 'header_button_visibility' ) && ! is_user_logged_in() ) {
		return;
	}
	if ( 'loggedout' === webapp()->option( 'header_button_visibility' ) && is_user_logged_in() ) {
		return;
	}
	if ( $label || is_customize_preview() ) {
		$wrap_classes   = array();
		$wrap_classes[] = 'header-button-wrap';
		if ( 'loggedin' === webapp()->option( 'header_button_visibility' ) ) {
			$wrap_classes[] = 'vs-logged-out-false';
		}
		if ( 'loggedout' === webapp()->option( 'header_button_visibility' ) ) {
			$wrap_classes[] = 'vs-logged-in-false';
		}
		echo '<div class="' . esc_attr( implode( ' ', $wrap_classes ) ) . '">';
		webapp()->customizer_quick_link();
		echo '<div class="header-button-inner-wrap">';
		$rel = array();
		if ( webapp()->option( 'header_button_target' ) ) {
			$rel[] = 'noopener';
			$rel[] = 'noreferrer';
		}
		if ( webapp()->option( 'header_button_nofollow' ) ) {
			$rel[] = 'nofollow';
		}
		if ( webapp()->option( 'header_button_sponsored' ) ) {
			$rel[] = 'sponsored';
		}
		echo '<a href="' . esc_attr( do_shortcode( webapp()->option( 'header_button_link' ) ) ) . '" target="' . esc_attr( webapp()->option( 'header_button_target' ) ? '_blank' : '_self' ) . '"' . ( ! empty( $rel ) ? ' rel="' . esc_attr( implode( ' ', $rel ) ) . '"' : '' ) . ( ! empty( webapp()->option( 'header_button_download' ) ) ? ' download' : '' ) . ' class="button header-button button-size-' . esc_attr( webapp()->option( 'header_button_size' ) ) . ' button-style-' . esc_attr( webapp()->option( 'header_button_style' ) ) . '">';
		echo esc_html( do_shortcode( $label ) );
		echo '</a>';
		echo '</div>';
		echo '</div>';
	}
}

/**
 * Mobile Button
 */
function mobile_button() {
	$label = webapp()->option( 'mobile_button_label' );
	if ( 'loggedin' === webapp()->option( 'mobile_button_visibility' ) && ! is_user_logged_in() ) {
		return;
	}
	if ( 'loggedout' === webapp()->option( 'mobile_button_visibility' ) && is_user_logged_in() ) {
		return;
	}
	if ( $label || is_customize_preview() ) {
		$wrap_classes   = array();
		$wrap_classes[] = 'mobile-header-button-wrap';
		if ( 'loggedin' === webapp()->option( 'mobile_button_visibility' ) ) {
			$wrap_classes[] = 'vs-logged-out-false';
		}
		if ( 'loggedout' === webapp()->option( 'mobile_button_visibility' ) ) {
			$wrap_classes[] = 'vs-logged-in-false';
		}
		echo '<div class="' . esc_attr( implode( ' ', $wrap_classes ) ) . '">';
		webapp()->customizer_quick_link();
		$rel = array();
		if ( webapp()->option( 'mobile_button_target' ) ) {
			$rel[] = 'noopener';
			$rel[] = 'noreferrer';
		}
		if ( webapp()->option( 'mobile_button_nofollow' ) ) {
			$rel[] = 'nofollow';
		}
		if ( webapp()->option( 'mobile_button_sponsored' ) ) {
			$rel[] = 'sponsored';
		}
		$classes   = array();
		$classes[] = 'button';
		$classes[] = 'mobile-header-button';
		$classes[] = 'button-size-' . esc_attr( webapp()->option( 'mobile_button_size' ) );
		$classes[] = 'button-style-' . esc_attr( webapp()->option( 'mobile_button_style' ) );
		echo '<div class="mobile-header-button-inner-wrap">';
		echo '<a href="' . esc_attr( do_shortcode( webapp()->option( 'mobile_button_link' ) ) ) . '" target="' . esc_attr( webapp()->option( 'mobile_button_target' ) ? '_blank' : '_self' ) . '"' . ( ! empty( $rel ) ? ' rel="' . esc_attr( implode( ' ', $rel ) ) . '"' : '' ) . ' class="' . esc_attr( implode( ' ', $classes ) ) . '">';
		echo esc_html( do_shortcode( $label ) );
		echo '</a>';
		echo '</div>';
		echo '</div>';
	}
}


/**
 * Desktop Cart
 */
function header_cart() {
	if ( class_exists( 'woocommerce' ) ) {
		wp_enqueue_script( 'wc-cart-fragments' );
		global $woocommerce;

		$cartTotal = add_suffix_to_price('total');
		$cartSubTotal = add_suffix_to_price('subtotal');
		$cartItems = WC()->cart->get_cart_contents_count();

		$title      = webapp()->option( 'header_cart_title' );
		$has_subtotal_title = ( str_contains($title, '{cart_total}') || str_contains($title, '{cart_subtotal}') );
		$title      = str_replace( '{cart_total}', $cartTotal, $title );
		$title      = str_replace( '{cart_subtotal}', $cartSubTotal, $title );
		$title      = str_replace( '{cart_count}', $cartItems, $title );
		$label      = webapp()->option( 'header_cart_label' );
		$has_subtotal_label = ( str_contains($label, '{cart_total}') || str_contains($label, '{cart_subtotal}') );
		$label      = str_replace( '{cart_total}', $cartTotal, $label );
		$label      = str_replace( '{cart_subtotal}', $cartSubTotal, $label );
		$label      = str_replace( '{cart_count}', $cartItems, $label );
		$show_total = webapp()->option( 'header_cart_show_total' );
		$icon       = webapp()->option( 'header_cart_icon', 'shopping-bag' );
		$dropdown   = 'header-navigation nav--toggle-sub header-navigation-dropdown-animation-' . esc_attr( webapp()->option( 'dropdown_navigation_reveal' ) );
		echo '<div class="header-cart-wrap base-header-cart' . ( 'dropdown' === webapp()->option( 'header_cart_style' ) ? ' ' . esc_attr( $dropdown ) : '' ) . '">';
		webapp()->customizer_quick_link();
		$cart_contents_count = ( isset( WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0 );
		
		echo '<span class="header-cart-empty-check header-cart-is-empty-' . ( $cart_contents_count > 0 ? 'false' : 'true' ) . '"></span>';
		echo '<div class="header-cart-inner-wrap cart-show-label-' . ( ! empty( $label ) ? 'true' : 'false' ) . ' cart-style-' . esc_attr( webapp()->option( 'header_cart_style' ) ) . ( 'dropdown' === webapp()->option( 'header_cart_style' ) ? ' header-menu-container' : '' ) . '">';
		if ( 'link' === webapp()->option( 'header_cart_style' ) ) {
			echo '<a href="' . esc_url( wc_get_cart_url() ) . '"' . ( ! empty( $label ) ? '' : ' aria-label="' . esc_attr__( 'Shopping Cart', 'avanam' ) . '"' ) . ' class="header-cart-button">';
			webapp()->print_icon( $icon, '', false );
			if ( $show_total ) {
				echo '<span class="header-cart-total header-cart-is-empty-' . ( $cart_contents_count > 0 ? 'false' : 'true' ) . '">' . wp_kses_post( $cart_contents_count ) . '</span>';
			}
			echo '<div class="header-cart-content">';
			if ( ! empty( $title )) {
				?>
				<span class="header-cart-title <?php echo esc_attr( $has_subtotal_title ? 'subtotal' : '' ); ?>"><?php echo wp_kses_post( $title ); ?></span>
				<?php
			}
			if ( ! empty( $label )) {
				?>
				<span class="header-cart-label <?php echo esc_attr( $has_subtotal_label ? 'subtotal' : '' ); ?>"><?php echo wp_kses_post( $label ); ?></span>
				<?php
			}
			echo '</div>';
			echo '</a>';
		} elseif ( 'slide' === webapp()->option( 'header_cart_style' ) ) {
			add_action( 'wp_footer', 'Base\cart_popup', 5 );
			echo '<button data-toggle-target="#cart-drawer"' . ( ! empty( $label ) ? '' : ' aria-label="' . esc_attr__( 'Shopping Cart', 'avanam' ) . '"' ) . ' class="drawer-toggle header-cart-button" data-toggle-body-class="showing-popup-drawer-from-' . esc_attr( webapp()->option( 'header_mobile_cart_popup_side' ) ) . '" aria-expanded="false" data-set-focus=".cart-toggle-close">';
			webapp()->print_icon( $icon, '', false );
			if ( $show_total ) {
				echo '<span class="header-cart-total header-cart-is-empty-' . ( $cart_contents_count > 0 ? 'false' : 'true' ) . '">' . wp_kses_post( $cart_contents_count ) . '</span>';
			}
			echo '<div class="header-cart-content">';
			if ( ! empty( $title )) {
				?>
				<span class="header-cart-title <?php echo esc_attr( $has_subtotal_title ? 'subtotal' : '' ); ?>"><?php echo wp_kses_post( $title ); ?></span>
				<?php
			}
			if ( ! empty( $label )) {
				?>
				<span class="header-cart-label <?php echo esc_attr( $has_subtotal_label ? 'subtotal' : '' ); ?>"><?php echo wp_kses_post( $label ); ?></span>
				<?php
			}
			echo '</div>';
			echo '</button>';
		} elseif ( 'dropdown' === webapp()->option( 'header_cart_style' ) ) {
			echo '<ul id="cart-menu" class="menu woocommerce widget_shopping_cart">';
			echo '<li class="menu-item menu-item-has-children menu-item-base-cart base-menu-has-icon menu-item--has-toggle">';
			echo '<a href="' . esc_url( wc_get_cart_url() ) . '"' . ( ! empty( $label ) ? '' : ' aria-label="' . esc_attr__( 'Shopping Cart', 'avanam' ) . '"' ) . ' class="header-cart-button">';
			webapp()->print_icon( $icon, '', false );
			if ( $show_total ) {
				echo '<span class="header-cart-total header-cart-is-empty-' . ( $cart_contents_count > 0 ? 'false' : 'true' ) . '">' . wp_kses_post( $cart_contents_count ) . '</span>';
			}
			echo '<div class="header-cart-content">';
			if ( ! empty( $title )) {
				?>
				<span class="header-cart-title <?php echo esc_attr( $has_subtotal_title ? 'subtotal' : '' ); ?>"><?php echo wp_kses_post( $title ); ?></span>
				<?php
			}
			if ( ! empty( $label )) {
				?>
				<span class="header-cart-label <?php echo esc_attr( $has_subtotal_label ? 'subtotal' : '' ); ?>"><?php echo wp_kses_post( $label ); ?></span>
				<?php
			}
			echo '</div>';
			echo '</a>';
			echo '<ul class="sub-menu">
			<li class="menu-item menu-item-base-cart-dropdown">
				<div class="base-mini-cart-refresh">';
					woocommerce_mini_cart();
				echo '</div>
			</li>
			</ul>
			</li>
			</ul>';
			if ( is_checkout() ) {
				echo "<script>jQuery( document.body ).on( 'removed_from_cart', function() {jQuery(document.body).trigger('update_checkout'); });</script>";
			}
		}
		echo '</div>';
		echo '</div>';
	}
}

/**
 * Cart Popup Toggle
 */
function cart_popup() {
	?>
	<div id="cart-drawer" class="popup-drawer popup-drawer-layout-sidepanel popup-drawer-side-<?php echo esc_attr( webapp()->option( 'header_cart_popup_side' ) ); ?> popup-mobile-drawer-side-<?php echo esc_attr( webapp()->option( 'header_mobile_cart_popup_side' ) ); ?>" data-drawer-target-string="#cart-drawer">
		<div class="drawer-overlay" data-drawer-target-string="#cart-drawer"></div>
		<div class="drawer-inner">
			<div class="drawer-header">
				<h2 class="side-cart-header"><?php esc_html_e( 'Shopping Cart', 'avanam' ); ?></h2>
				<button class="cart-toggle-close drawer-toggle" aria-label="<?php esc_attr_e( 'Close Cart', 'avanam' ); ?>"  data-toggle-target="#cart-drawer" data-toggle-body-class="showing-popup-drawer-from-<?php echo esc_attr( webapp()->option( 'header_mobile_cart_popup_side' ) ); ?>" aria-expanded="false" data-set-focus=".header-cart-button">
					<?php webapp()->print_icon( 'close', '', false ); ?>
				</button>
			</div>
			<div class="drawer-content woocommerce widget_shopping_cart">
				<?php do_action( 'base-before-side-cart' ); ?>
				<div class="mini-cart-container">
					<div class="base-mini-cart-refresh">
						<?php woocommerce_mini_cart(); ?>
					</div>
				</div>
				<?php do_action( 'base-after-side-cart' ); ?>
			</div>
		</div>
	</div>
	<?php
	if ( is_checkout() ) {
		echo "<script>jQuery( document.body ).on( 'removed_from_cart', function() {jQuery(document.body).trigger('update_checkout'); });</script>";
	}
}

/**
 * Desktop Cart
 */
function mobile_cart() {
	if ( class_exists( 'woocommerce' ) ) {
		wp_enqueue_script( 'wc-cart-fragments' );
		global $woocommerce;

		$cartTotal = add_suffix_to_price('total','mobile');
		$cartSubTotal = add_suffix_to_price('subtotal','mobile');
		$cartItems = WC()->cart->get_cart_contents_count();

		$title      = webapp()->option( 'header_mobile_cart_title' );
		$has_subtotal_title = ( str_contains($title, '{cart_total}') || str_contains($title, '{cart_subtotal}') );
		$title      = str_replace( '{cart_total}', $cartTotal, $title );
		$title      = str_replace( '{cart_subtotal}', $cartSubTotal, $title );
		$title      = str_replace( '{cart_count}', $cartItems, $title );
		$label      = webapp()->option( 'header_mobile_cart_label' );
		$has_subtotal_label = ( str_contains($label, '{cart_total}') || str_contains($label, '{cart_subtotal}') );
		$label      = str_replace( '{cart_total}', $cartTotal, $label );
		$label      = str_replace( '{cart_subtotal}', $cartSubTotal, $label );
		$label      = str_replace( '{cart_count}', $cartItems, $label );
		$show_total = webapp()->option( 'header_mobile_cart_show_total' );
		$icon       = webapp()->option( 'header_mobile_cart_icon', 'shopping-bag' );
		echo '<div class="header-mobile-cart-wrap base-header-cart">';
		webapp()->customizer_quick_link();
		$cart_contents_count = ( isset( WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0 );
		echo '<span class="header-cart-empty-check header-cart-is-empty-' . ( $cart_contents_count > 0 ? 'false' : 'true' ) . '"></span>';
		echo '<div class="header-cart-inner-wrap cart-show-label-' . ( ! empty( $label ) ? 'true' : 'false' ) . ' cart-style-' . esc_attr( webapp()->option( 'header_mobile_cart_style' ) ) . '">';
		if ( 'link' === webapp()->option( 'header_mobile_cart_style' ) ) {
			echo '<a href="' . esc_url( wc_get_cart_url() ) . '"' . ( ! empty( $label ) ? '' : ' aria-label="' . esc_attr__( 'Shopping Cart', 'avanam' ) . '"' ) . ' class="header-cart-button">';
			webapp()->print_icon( $icon, '', false );
			if ( $show_total ) {
				echo '<span class="header-cart-total">' . wp_kses_post( $cart_contents_count ) . '</span>';
			}
			echo '<div class="header-cart-content">';
			if ( ! empty( $title )) {
				?>
				<span class="header-cart-title <?php echo esc_attr( $has_subtotal_title ? 'subtotal' : '' ); ?>"><?php echo wp_kses_post( $title ); ?></span>
				<?php
			}
			if ( ! empty( $label )) {
				?>
				<span class="header-cart-label <?php echo esc_attr( $has_subtotal_label ? 'subtotal' : '' ); ?>"><?php echo wp_kses_post( $label ); ?></span>
				<?php
			}
			echo '</div>';
			echo '</a>';
		} elseif ( 'slide' === webapp()->option( 'header_mobile_cart_style' ) ) {
			add_action( 'wp_footer', 'Base\cart_popup', 5 );
			echo '<button data-toggle-target="#cart-drawer"' . ( ! empty( $label ) ? '' : ' aria-label="' . esc_attr__( 'Shopping Cart', 'avanam' ) . '"' ) . ' class="drawer-toggle header-cart-button" data-toggle-body-class="showing-popup-drawer-from-' . esc_attr( webapp()->option( 'header_mobile_cart_popup_side' ) ) . '" aria-expanded="false" data-set-focus=".cart-toggle-close">';
			webapp()->print_icon( $icon, '', false );
			if ( $show_total ) {
				echo '<span class="header-cart-total">' . wp_kses_post( $cart_contents_count ) . '</span>';
			}
			echo '<div class="header-cart-content">';
			if ( ! empty( $title )) {
				?>
				<span class="header-cart-title <?php echo esc_attr( $has_subtotal_title ? 'subtotal' : '' ); ?>"><?php echo wp_kses_post( $title ); ?></span>
				<?php
			}
			if ( ! empty( $label )) {
				?>
				<span class="header-cart-label <?php echo esc_attr( $has_subtotal_label ? 'subtotal' : '' ); ?>"><?php echo wp_kses_post( $label ); ?></span>
				<?php
			}
			echo '</div>';
			echo '</button>';
		}
		echo '</div>';
		echo '</div>';
	}
}

/**
 * Desktop Social
 */
function header_social() {
	$items      = webapp()->sub_option( 'header_social_items', 'items' );
	$show_label = webapp()->option( 'header_social_show_label' );
	$brand_colors = webapp()->option( 'header_social_brand' );
	$brand_color_class = '';
	if ( 'onhover' === $brand_colors ) {
		$brand_color_class = ' social-show-brand-hover';
	} elseif ( 'untilhover' === $brand_colors ) {
		$brand_color_class = ' social-show-brand-until';
	} elseif ( 'always' === $brand_colors ) {
		$brand_color_class = ' social-show-brand-always';
	}
	echo '<div class="header-social-wrap">';
	webapp()->customizer_quick_link();
	echo '<div class="header-social-inner-wrap element-social-inner-wrap social-show-label-' . ( $show_label ? 'true' : 'false' ) . ' social-style-' . esc_attr( webapp()->option( 'header_social_style' ) ) . esc_attr( $brand_color_class ) . '">';
	if ( is_array( $items ) && ! empty( $items ) ) {
		foreach ( $items as $item ) {
			if ( $item['enabled'] ) {
				$link = webapp()->option( $item['id'] . '_link' );
				if ( 'phone' === $item['id'] ) {
					$link = 'tel:' . str_replace( 'tel:', '', $link );
				} elseif ( 'email' === $item['id'] ) {
					$link = str_replace( 'mailto:', '', $link );
					if ( is_email( $link ) ) {
						$link = 'mailto:' . $link;
					}
				}
				echo '<a href="' . esc_attr( $link ) . '"' . ( $show_label ? '' : ' aria-label="' . esc_attr( $item['label'] ) . '"' ) . ' ' . ( 'phone' === $item['id'] || 'email' === $item['id'] || apply_filters( 'base_social_link_target', false, $item ) ? '' : 'target="_blank" rel="noopener noreferrer"  ' ) . 'class="social-button header-social-item social-link-' . esc_attr( $item['id'] ) . esc_attr( 'image' === $item['source'] ? ' has-custom-image' : '' ) . '">';
				if ( 'image' === $item['source'] ) {
					if ( $item['imageid'] && wp_get_attachment_image( $item['imageid'], 'full', true ) ) {
						echo wp_get_attachment_image( $item['imageid'], 'full', true, array( 'class' => 'social-icon-image', 'style' => 'max-width:' . esc_attr( $item['width'] ) . 'px' ) );
					} elseif ( ! empty( $item['url'] ) ) {
						echo '<img src="' . esc_attr( $item['url'] ) . '" alt="' . esc_attr( $item['label'] ) . '" class="social-icon-image" style="max-width:' . esc_attr( $item['width'] ) . 'px"/>';
					}
				} elseif ( 'svg' === $item['source'] ) {
					if ( ! empty( $item['svg'] ) ) {
						echo '<span class="social-icon-custom-svg" style="max-width:' . esc_attr( $item['width'] ) . 'px">' . $item['svg'] . '</span>';
					}
				} else {
					webapp()->print_icon( $item['icon'], '', false );
				}
				if ( $show_label ) {
					echo '<span class="social-label">' . esc_html( $item['label'] ) . '</span>';
				}
				echo '</a>';
			}
		}
	}
	echo '</div>';
	echo '</div>';
}

/**
 * Mobile Social
 */
function mobile_social() {
	$items      = webapp()->sub_option( 'header_mobile_social_items', 'items' );
	$show_label = webapp()->option( 'header_mobile_social_show_label' );
	$brand_colors = webapp()->option( 'header_mobile_social_brand' );
	$brand_color_class = '';
	if ( 'onhover' === $brand_colors ) {
		$brand_color_class = ' social-show-brand-hover';
	} elseif ( 'untilhover' === $brand_colors ) {
		$brand_color_class = ' social-show-brand-until';
	} elseif ( 'always' === $brand_colors ) {
		$brand_color_class = ' social-show-brand-always';
	}
	echo '<div class="header-mobile-social-wrap">';
	webapp()->customizer_quick_link();
	echo '<div class="header-mobile-social-inner-wrap element-social-inner-wrap social-show-label-' . ( $show_label ? 'true' : 'false' ) . ' social-style-' . esc_attr( webapp()->option( 'header_mobile_social_style' ) ) . esc_attr( $brand_color_class ) . '">';
	if ( is_array( $items ) && ! empty( $items ) ) {
		foreach ( $items as $item ) {
			if ( $item['enabled'] ) {
				$link = webapp()->option( $item['id'] . '_link' );
				if ( 'phone' === $item['id'] ) {
					$link = 'tel:' . str_replace( 'tel:', '', $link );
				} elseif ( 'email' === $item['id'] ) {
					$link = str_replace( 'mailto:', '', $link );
					if ( is_email( $link ) ) {
						$link = 'mailto:' . $link;
					}
				}
				echo '<a href="' . esc_attr( $link ) . '"' . ( $show_label ? '' : ' aria-label="' . esc_attr( $item['label'] ) . '"' ) . ' ' . ( 'phone' === $item['id'] || 'email' === $item['id'] || apply_filters( 'base_social_link_target', false, $item ) ? '' : 'target="_blank" rel="noopener noreferrer"  ' ) . 'class="social-button header-social-item social-link-' . esc_attr( $item['id'] ) . esc_attr( 'image' === $item['source'] ? ' has-custom-image' : '' ) . '">';
				if ( 'image' === $item['source'] ) {
					if ( $item['imageid'] && wp_get_attachment_image( $item['imageid'], 'full', true ) ) {
						echo wp_get_attachment_image( $item['imageid'], 'full', true, array( 'class' => 'social-icon-image', 'style' => 'max-width:' . esc_attr( $item['width'] ) . 'px' ) );
					} elseif ( ! empty( $item['url'] ) ) {
						echo '<img src="' . esc_attr( $item['url'] ) . '" alt="' . esc_attr( $item['label'] ) . '" class="social-icon-image" style="max-width:' . esc_attr( $item['width'] ) . 'px"/>';
					}
				} elseif ( 'svg' === $item['source'] ) {
					if ( ! empty( $item['svg'] ) ) {
						echo '<span class="social-icon-custom-svg" style="max-width:' . esc_attr( $item['width'] ) . 'px">' . $item['svg'] . '</span>';
					}
				} else {
					webapp()->print_icon( $item['icon'], '', false );
				}
				if ( $show_label ) {
					echo '<span class="social-label">' . esc_html( $item['label'] ) . '</span>';
				}
				echo '</a>';
			}
		}
	}
	echo '</div>';
	echo '</div>';
}

/**
 * Header Search Popup Toggle
 */
function header_search() {
	add_action( 'wp_footer', 'Base\search_modal', 20 );
	?>
	<div class="search-toggle-open-container">
		<?php webapp()->customizer_quick_link(); ?>
		<?php
		if ( webapp()->is_amp() ) {
			?>
			<amp-state id="siteSearchModal">
				<script type="application/json">
					{
						"expanded": false
					}
				</script>
			</amp-state>
			<?php
		}
		?>
		<button class="search-toggle-open drawer-toggle search-toggle-style-<?php echo esc_attr( webapp()->option( 'header_search_style' ) ); ?>" aria-label="<?php esc_attr_e( 'View Search Form', 'avanam' ); ?>" data-toggle-target="#search-drawer" data-toggle-body-class="showing-popup-drawer-from-full" aria-expanded="false" data-set-focus="#search-drawer .search-field"
			<?php
			if ( webapp()->is_amp() ) {
				?>
				[class]=" siteSearchModal.expanded ? 'search-toggle-open drawer-toggle search-toggle-style-<?php echo esc_attr( webapp()->option( 'header_search_style' ) ); ?> active' : 'search-toggle-open drawer-toggle search-toggle-style-<?php echo esc_attr( webapp()->option( 'header_search_style' ) ); ?>' "
				on="tap:AMP.setState( { siteSearchModal: { expanded: ! siteSearchModal.expanded } } )"
				[aria-expanded]="siteSearchModal.expanded ? 'true' : 'false'"
				<?php
			}
			?>
		>
			<?php
			$label = webapp()->option( 'header_search_label' );
			if ( ! empty( $label ) || is_customize_preview() ) {
				?>
				<span class="search-toggle-label vs-lg-<?php echo ( webapp()->sub_option( 'header_search_label_visiblity', 'desktop' ) ? 'true' : 'false' ); ?> vs-md-<?php echo ( webapp()->sub_option( 'header_search_label_visiblity', 'tablet' ) ? 'true' : 'false' ); ?> vs-sm-<?php echo ( webapp()->sub_option( 'header_search_label_visiblity', 'mobile' ) ? 'true' : 'false' ); ?>"><?php echo esc_html( $label ); ?></span>
				<?php
			}
			?>
			<span class="search-toggle-icon"><?php search_toggle(); ?></span>
		</button>
	</div>
	<?php
}

/**
 * Search Popup Toggle Icon
 */
function search_toggle() {
	$icon = webapp()->option( 'header_search_icon' );
	webapp()->print_icon( $icon, '', false );
}
/**
 * Search Popup Modal
 */
function search_modal() {
	?>
	<div id="search-drawer" class="popup-drawer popup-drawer-layout-fullwidth" data-drawer-target-string="#search-drawer"
		<?php
		if ( webapp()->is_amp() ) {
			?>
			[class]=" siteSearchModal.expanded ? 'popup-drawer popup-drawer-layout-fullwidth show-drawer active' : 'popup-drawer popup-drawer-layout-fullwidth' "
			<?php
		}
		?>
	>
		<div class="drawer-overlay" data-drawer-target-string="#search-drawer"></div>
		<div class="drawer-inner">
			<div class="drawer-header">
				<button class="search-toggle-close drawer-toggle" aria-label="<?php esc_attr_e( 'Close search', 'avanam' ); ?>"  data-toggle-target="#search-drawer" data-toggle-body-class="showing-popup-drawer-from-full" aria-expanded="false" data-set-focus=".search-toggle-open"
				<?php
				if ( webapp()->is_amp() ) {
					?>
					on="tap:AMP.setState( { siteSearchModal: { expanded: ! siteSearchModal.expanded } } )"
					[aria-expanded]="siteSearchModal.expanded ? 'true' : 'false'"
					<?php
				}
				?>
			>
					<?php webapp()->print_icon( 'close', '', false ); ?>
				</button>
			</div>
			<div class="drawer-content">
				<?php
				if ( class_exists( 'woocommerce' ) && webapp()->option( 'header_search_woo' ) ) {
					get_product_search_form();
				} else {
					get_search_form();
				}
				?>
			</div>
		</div>
	</div>
	<?php
}


function add_suffix_to_price($type = 'total', $device = '') {
	// Determine if decimals should be shown (mobile or desktop setting)
    $show_decimal = $device === 'mobile' 
        ? webapp()->option('header_mobile_cart_show_decimal') 
        : webapp()->option('header_cart_show_decimal');

    // Get the correct cart value and price
    $cart = WC()->cart;
    $price = ($type === 'subtotal') ? $cart->get_cart_subtotal() : $cart->get_cart_total();
    $cart_value = ($type === 'subtotal') ? $cart->subtotal : $cart->total;

    // Only modify price if cart total/subtotal is 0 and the setting is enabled
    if ($cart_value == 0 && $show_decimal) {
        $currency_symbol = get_woocommerce_currency_symbol();
        $price_format = get_woocommerce_price_format();

        // Determine if currency symbol is before or after the price
        if (strpos($price_format, '%1$s') < strpos($price_format, '%2$s')) {
            // Currency symbol is before the price
            $price .= '.00';
        } else {
            // Currency symbol is after the price
            $price = str_replace($currency_symbol, '.00' . $currency_symbol, $price);
        }
    }

    return $price;
}
