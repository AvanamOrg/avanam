<?php
/**
 * Base\LearnDash\Component class
 *
 * @package Base
 */

namespace Base\LearnDash;

use Base\Component_Interface;
use LearnDash_Settings_Section;
use function Base\webapp;
use function add_action;
use function add_filter;
use function have_posts;
use function the_post;
use function is_search;
use function get_template_part;
use function get_post_type;

/**
 * Class for adding LearnDash plugin support.
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'learndash';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'learndash_styles' ), 60 );
		add_filter( 'learndash_wrapper_class', array( $this, 'learndash_lesson_class' ), 10, 2 );
		add_filter( 'learndash_course_grid_template', array( $this, 'learndash_course_grid_template' ), 10 );
		add_filter( 'ld_course_list', array( $this, 'learndash_course_grid_class' ), 10, 3 );
		add_action( 'after_setup_theme', array( $this, 'action_add_editor_styles' ) );
	}
	/**
	 * Filters HTML output of course list.
	 *
	 * @since 2.1.0
	 *
	 * @param string $output HTML output of category dropdown.
	 * @param array  $atts   Shortcode attributes.
	 * @param array  $filter  Arguments to retrieve posts.
	 */
	public function learndash_course_grid_class( $output, $atts, $filter ) {
		if ( defined( 'LEARNDASH_COURSE_GRID_VERSION' ) && version_compare( LEARNDASH_COURSE_GRID_VERSION, '2.0.0', '<' ) && webapp()->option( 'learndash_course_grid' ) ) {
			// Return if not a grid.
			if ( $atts['course_grid'] == 'false' || 
			$atts['course_grid'] === false || 
			empty( $atts['course_grid'] ) ) {
				return $output;
			}
			$col   = empty( $atts['col'] ) ? LEARNDASH_COURSE_GRID_COLUMNS : intval( $atts['col'] );
			$col   = $col > 6 ? 6 : $col;
			$smcol = $col == 1 ? 1 : ceil( $col / 2 );

			$output = str_replace( 'ld-course-list-items row', 'ld-course-list-items content-wrap grid-sm-col-' . $smcol . ' grid-lg-col-' . $col . ' grid-cols', $output );

		}
		return $output;
	}
	/**
	 * Enqueues WordPress theme styles for the editor.
	 */
	public function action_add_editor_styles() {
		// Enqueue block editor stylesheet.
		add_editor_style( 'assets/css/editor/learndash-editor-styles.min.css' );
	}
	/**
	 * Override grid template file.
	 *
	 * @param string $template the template to load.
	 */
	public function learndash_course_grid_template( $template ) {
		if ( defined( 'LEARNDASH_COURSE_GRID_VERSION' ) && version_compare( LEARNDASH_COURSE_GRID_VERSION, '2.0.0', '<' ) && webapp()->option( 'learndash_course_grid' ) ) {
			$template = get_template_directory() . '/inc/components/learndash/course_list_template.php';
		}
		return $template;
	}
	/**
	 * Add some css styles for learndash
	 *
	 * @param string $class the class for the wrapper.
	 * @param object $post the post object.
	 */
	public function learndash_lesson_class( $class, $post ) {
		if ( is_object( $post ) && 'sfwd-lessons' === $post->post_type ) {
			$class = $class . ' entry-content';
		}
		return $class;
	}
	/**
	 * Add some css styles for learndash
	 */
	public function learndash_styles() {
		wp_enqueue_style( 'base-learndash', get_theme_file_uri( '/assets/css/learndash.min.css' ), array(), AVANAM_VERSION );
		if ( class_exists( 'LearnDash_Settings_Section' ) && apply_filters( 'base_learndash_colors', true ) && ! defined( 'LDX_DESIGN_UPGRADE_PRO_LEARNDASH_VERSION' ) ) {
			$colors = array(
				'primary'   => \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Theme_LD30', 'color_primary' ),
				'secondary' => \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Theme_LD30', 'color_secondary' ),
			);
			if ( isset( $colors['primary'] ) && empty( $colors['primary'] ) ) {
				ob_start();
				?>
				.ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price.ribbon-enrolled {
					background: var(--global-palette-btn-bg-hover);
				}
				.ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price.ribbon-enrolled:before {
					border-top-color:  var(--global-palette-btn-bg);
    				border-right-color:  var(--global-palette-btn-bg);
				}
				.ld-course-list-items .ld_course_grid .btn-primary {
					border-color: var(--global-palette-btn-bg);
					background: var(--global-palette-btn-bg);
					color: var(--global-palette-btn);
					box-shadow: 0px 0px 0px -7px rgba(0,0,0,0.0);
				}
				.ld-course-list-items .ld_course_grid .btn-primary:hover {
					color: var(--global-palette-btn-hover);
					border-color: var(--global-palette-btn-bg-hover);
					background: var(--global-palette-btn-bg-hover);
					box-shadow: 0px 15px 25px -7px rgba(0,0,0,0.1);
				}
				.learndash-wrapper .ld-item-list .ld-item-list-item.ld-is-next,
				.learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem label:focus-within {
					border-color: var(--global-palette1);
				}
				/*
				.learndash-wrapper a:not(.ld-button):not(#quiz_continue_link):not(.ld-focus-menu-link):not(.btn-blue):not(#quiz_continue_link):not(.ld-js-register-account):not(#ld-focus-mode-course-heading):not(#btn-join):not(.ld-item-name):not(.ld-table-list-item-preview):not(.ld-lesson-item-preview-heading),
				*/

				.learndash-wrapper .ld-breadcrumbs a,
				.learndash-wrapper .ld-lesson-item.ld-is-current-lesson .ld-lesson-item-preview-heading,
				.learndash-wrapper .ld-lesson-item.ld-is-current-lesson .ld-lesson-title,
				.learndash-wrapper .ld-primary-color-hover:hover,
				.learndash-wrapper .ld-primary-color,
				.learndash-wrapper .ld-primary-color-hover:hover,
				.learndash-wrapper .ld-primary-color,
				.learndash-wrapper .ld-tabs .ld-tabs-navigation .ld-tab.ld-active,
				.learndash-wrapper .ld-button.ld-button-transparent,
				.learndash-wrapper .ld-button.ld-button-reverse,
				.learndash-wrapper .ld-icon-certificate,
				.learndash-wrapper .ld-login-modal .ld-login-modal-login .ld-modal-heading,
				#wpProQuiz_user_content a,
				.learndash-wrapper .ld-item-list .ld-item-list-item a.ld-item-name:hover,
				.learndash-wrapper .ld-focus-comments__heading-actions .ld-expand-button,
				.learndash-wrapper .ld-focus-comments__heading a,
				.learndash-wrapper .ld-focus-comments .comment-respond a,
				.learndash-wrapper .ld-focus-comment .ld-comment-reply a.comment-reply-link:hover,
				.learndash-wrapper .ld-expand-button.ld-button-alternate {
					color: var(--global-palette1) !important;
				}

				.learndash-wrapper .ld-focus-comment.bypostauthor>.ld-comment-wrapper,
				.learndash-wrapper .ld-focus-comment.role-group_leader>.ld-comment-wrapper,
				.learndash-wrapper .ld-focus-comment.role-administrator>.ld-comment-wrapper {
					background-color: <?php echo esc_attr( $this->learndash_hex2rgb( webapp()->palette_option( 'palette1' ), '0.03' ) ); ?> !important;
				}


				.learndash-wrapper .ld-primary-background,
				.learndash-wrapper .ld-tabs .ld-tabs-navigation .ld-tab.ld-active:after {
					background: var(--global-palette1) !important;
				}



				.learndash-wrapper .ld-course-navigation .ld-lesson-item.ld-is-current-lesson .ld-status-incomplete,
				.learndash-wrapper .ld-focus-comment.bypostauthor:not(.ptype-sfwd-assignment) >.ld-comment-wrapper>.ld-comment-avatar img,
				.learndash-wrapper .ld-focus-comment.role-group_leader>.ld-comment-wrapper>.ld-comment-avatar img,
				.learndash-wrapper .ld-focus-comment.role-administrator>.ld-comment-wrapper>.ld-comment-avatar img {
					border-color: var(--global-palette1) !important;
				}



				.learndash-wrapper .ld-loading::before {
					border-top:3px solid var(--global-palette1) !important;
				}

				.learndash-wrapper .ld-button:hover:not(.learndash-link-previous-incomplete):not(.ld-button-transparent),
				#learndash-tooltips .ld-tooltip:after,
				#learndash-tooltips .ld-tooltip,
				.learndash-wrapper .ld-primary-background,
				.learndash-wrapper .btn-join,
				.learndash-wrapper #btn-join,
				.learndash-wrapper .ld-button:not(.ld-js-register-account):not(.learndash-link-previous-incomplete):not(.ld-button-transparent),
				.learndash-wrapper .ld-expand-button,
				.learndash-wrapper .wpProQuiz_content .wpProQuiz_button:not(.wpProQuiz_button_reShowQuestion):not(.wpProQuiz_button_restartQuiz),
				.learndash-wrapper .wpProQuiz_content .wpProQuiz_button2,
				.learndash-wrapper .ld-focus .ld-focus-sidebar .ld-course-navigation-heading,
				.learndash-wrapper .ld-focus .ld-focus-sidebar .ld-focus-sidebar-trigger,
				.learndash-wrapper .ld-focus-comments .form-submit #submit,
				.learndash-wrapper .ld-login-modal input[type='submit'],
				.learndash-wrapper .ld-login-modal .ld-login-modal-register,
				.learndash-wrapper .wpProQuiz_content .wpProQuiz_certificate a.btn-blue,
				.learndash-wrapper .ld-focus .ld-focus-header .ld-user-menu .ld-user-menu-items a,
				#wpProQuiz_user_content table.wp-list-table thead th,
				#wpProQuiz_overlay_close,
				.learndash-wrapper .ld-expand-button.ld-button-alternate .ld-icon {
					background-color: var(--global-palette1) !important;
				}


				.learndash-wrapper .ld-focus .ld-focus-header .ld-user-menu .ld-user-menu-items:before {
					border-bottom-color: var(--global-palette1) !important;
				}

				.learndash-wrapper .ld-button.ld-button-transparent:hover {
					background: transparent !important;
				}

				.learndash-wrapper .ld-focus .ld-focus-header .sfwd-mark-complete .learndash_mark_complete_button,
				.learndash-wrapper .ld-focus .ld-focus-header #sfwd-mark-complete #learndash_mark_complete_button,
				.learndash-wrapper .ld-button.ld-button-transparent,
				.learndash-wrapper .ld-button.ld-button-alternate,
				.learndash-wrapper .ld-expand-button.ld-button-alternate {
					background-color:transparent !important;
				}

				.learndash-wrapper .ld-focus-header .ld-user-menu .ld-user-menu-items a,
				.learndash-wrapper .ld-button.ld-button-reverse:hover,
				.learndash-wrapper .ld-alert-success .ld-alert-icon.ld-icon-certificate,
				.learndash-wrapper .ld-alert-warning .ld-button:not(.learndash-link-previous-incomplete),
				.learndash-wrapper .ld-primary-background.ld-status {
					color:white !important;
				}

				.learndash-wrapper .ld-status.ld-status-unlocked {
					background-color: <?php echo esc_attr( $this->learndash_hex2rgb( webapp()->palette_option( 'palette1' ), '0.2' ) ); ?> !important;
					color: var(--global-palette1) !important;
				}

				.learndash-wrapper .wpProQuiz_content .wpProQuiz_addToplist {
					background-color: <?php echo esc_attr( $this->learndash_hex2rgb( webapp()->palette_option( 'palette1' ), '0.1' ) ); ?> !important;
					border: 1px solid var(--global-palette1) !important;
				}

				.learndash-wrapper .wpProQuiz_content .wpProQuiz_toplistTable th {
					background: var(--global-palette1) !important;
				}

				.learndash-wrapper .wpProQuiz_content .wpProQuiz_toplistTrOdd {
					background-color: <?php echo esc_attr( $this->learndash_hex2rgb( webapp()->palette_option( 'palette1' ), '0.1' ) ); ?> !important;
				}

				.learndash-wrapper .wpProQuiz_content .wpProQuiz_reviewDiv li.wpProQuiz_reviewQuestionTarget {
					background-color: var(--global-palette1) !important;
				}

				<?php

				if ( isset( $colors['secondary'] ) && empty( $colors['secondary'] ) ) {
					?>

					.learndash-wrapper #quiz_continue_link,
					.learndash-wrapper .ld-secondary-background,
					.learndash-wrapper .learndash_mark_complete_button,
					.learndash-wrapper #learndash_mark_complete_button,
					.learndash-wrapper .ld-status-complete,
					.learndash-wrapper .ld-alert-success .ld-button,
					.learndash-wrapper .ld-alert-success .ld-alert-icon {
						background-color: var(--global-palette2) !important;
					}
					.learndash-wrapper .learndash_mark_complete_button:hover, .learndash-wrapper #learndash_mark_complete_button:hover {
						background-color: var(--global-palette2) !important;
					}

					.learndash-wrapper .wpProQuiz_content a#quiz_continue_link {
						background-color: var(--global-palette2) !important;
					}

					.learndash-wrapper .course_progress .sending_progress_bar {
						background: var(--global-palette2) !important;
					}

					.learndash-wrapper .wpProQuiz_content .wpProQuiz_button_reShowQuestion:hover, .learndash-wrapper .wpProQuiz_content .wpProQuiz_button_restartQuiz:hover {
						background-color: var(--global-palette2) !important;
						opacity: 0.75;
					}

					.learndash-wrapper .ld-secondary-color-hover:hover,
					.learndash-wrapper .ld-secondary-color,
					.learndash-wrapper .ld-focus .ld-focus-header .sfwd-mark-complete .learndash_mark_complete_button,
					.learndash-wrapper .ld-focus .ld-focus-header #sfwd-mark-complete #learndash_mark_complete_button,
					.learndash-wrapper .ld-focus .ld-focus-header .sfwd-mark-complete:after {
						color: var(--global-palette2) !important;
					}

					.learndash-wrapper .ld-secondary-in-progress-icon {
						border-left-color: var(--global-palette2) !important;
						border-top-color: var(--global-palette2) !important;
					}

					.learndash-wrapper .ld-alert-success {
						border-color: var(--global-palette2);
						background-color: transparent !important;
					}

					.learndash-wrapper .wpProQuiz_content .wpProQuiz_reviewQuestion li.wpProQuiz_reviewQuestionSolved,
					.learndash-wrapper .wpProQuiz_content .wpProQuiz_box li.wpProQuiz_reviewQuestionSolved {
						background-color: var(--global-palette2) !important;
					}

					.learndash-wrapper .wpProQuiz_content  .wpProQuiz_reviewLegend span.wpProQuiz_reviewColor_Answer {
						background-color: var(--global-palette2) !important;
					}

					<?php
				}
				$custom_css = ob_get_clean();
				if ( ! empty( $custom_css ) ) {
					wp_add_inline_style( 'base-learndash', $custom_css );
				}
			}
		}
	}
	/**
	 * Converts the hex color values to rgb.
	 *
	 * @param string            $color  Color value in hex format.
	 * @param float|int|boolean $opacity The opacity of color.
	 *
	 * @return string Color value in rgb format.
	 */
	public function learndash_hex2rgb( $color, $opacity = false ) {

		$default = 'transparent';

		// Return default if no color provided.
		if ( empty( $color ) ) {
			return $default;
		}

		// Sanitize $color if "#" is provided.
		if ( '#' === $color[0] ) {
			$color = substr( $color, 1 );
		}

			// Check if color has 6 or 3 characters and get values.
		if ( strlen( $color ) == 6 ) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return $default;
		}

		// Convert hexadec to rgb.
		$rgb = array_map( 'hexdec', $hex );

		// Check if opacity is set(rgba or rgb).
		if ( $opacity ) {
			if ( abs( $opacity ) > 1 ) {
				$opacity = 1.0;
			}
			$output = 'rgba(' . implode( ',', $rgb ) . ',' . $opacity . ')';
		} else {
			$output = 'rgb(' . implode( ',', $rgb ) . ')';
		}

		// Return rgb(a) color string.
		return $output;
	}
}
