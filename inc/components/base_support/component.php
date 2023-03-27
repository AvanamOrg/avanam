<?php
/**
 * Base\Base_Support\Component class
 *
 * @package Base
 */

namespace Base\Base_Support;

use Base\Component_Interface;
use Base\Templating_Component_Interface;
use WP_Upgrader;
use WP_Ajax_Upgrader_Skin;
use Plugin_Upgrader;
use function Base\webapp;
use function add_action;
use function add_filter;
use function add_theme_support;
use function is_singular;
use function pings_open;
use function esc_url;
use function get_bloginfo;
use function wp_scripts;
use function wp_get_theme;
use function get_template;
use function plugins_api;
use function activate_plugin;
use function get_site_option;
use function is_customize_preview;

/**
 * Class for adding basic theme support, most of which is mandatory to be implemented by all themes.
 *
 * Exposes template tags:
 * * `webapp()->get_version()`
 * * `webapp()->get_post_types()`
 * * `webapp()->get_asset_version( string $filepath )`
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Holds post types.
	 *
	 * @var values of all the post types.
	 */
	protected static $post_types = null;

	/**
	 * Holds post types.
	 *
	 * @var values of all the post types.
	 */
	protected static $post_types_objects = null;

	/**
	 * Holds ignore post types.
	 *
	 * @var values of all the post types.
	 */
	protected static $ignore_post_types = null;

	/**
	 * Holds ignore post types.
	 *
	 * @var values of all the post types.
	 */
	protected static $public_ignore_post_types = null;

	/**
	 * Holds ignore post types.
	 *
	 * @var values of all the post types.
	 */
	protected static $transparent_ignore_post_types = null;

	/**
	 * Static var active plugins
	 *
	 * @var $active_plugins
	 */
	private static $active_plugins;

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'base_support';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'after_setup_theme', array( $this, 'action_essential_theme_support' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'install_starter_script' ) );
		//add_action( 'admin_notices', array( $this, 'base_starter_templates_notice' ) );
		add_action( 'admin_notices', array( $this, 'base_welcome_notice' ) );
		add_action( 'admin_notices', array( $this, 'base_turn_off_gutenberg_plugin_notice' ) );
		//add_action( 'wp_ajax_base_dismiss_notice', array( $this, 'ajax_dismiss_starter_notice' ) );
		add_action( 'wp_ajax_base_dismiss_welcome_notice', array( $this, 'ajax_dismiss_welcome_notice' ) );
		add_action( 'wp_ajax_base_dismiss_gutenberg_notice', array( $this, 'ajax_dismiss_gutenberg_notice' ) );
		add_action( 'wp_ajax_base_install_starter', array( $this, 'install_plugin_ajax_callback' ) );
		add_action( 'wp_head', array( $this, 'action_add_pingback_header' ) );
		add_action( 'wp_head', array( $this, 'action_add_no_js_remove_script' ), 2 );
		add_action( 'wp_footer', array( $this, 'action_add_scrollbar_offset_script' ), 2 );
		add_filter( 'body_class', array( $this, 'filter_body_classes_add_hfeed' ) );
		add_filter( 'embed_defaults', array( $this, 'filter_embed_dimensions' ) );
		add_filter( 'theme_scandir_exclusions', array( $this, 'filter_scandir_exclusions_for_optional_templates' ) );
		add_filter( 'script_loader_tag', array( $this, 'filter_script_loader_tag' ), 10, 2 );
		add_filter( 'body_class', array( $this, 'filter_body_classes_add_link_style' ) );
		add_filter( 'get_search_form', array( $this, 'add_search_icon' ), 99 );
		add_filter( 'get_product_search_form', array( $this, 'add_search_icon' ), 99 );
		add_filter( 'embed_oembed_html', array( $this, 'classic_embed_wrap' ), 90, 4 );
		add_filter( 'excerpt_length', array( $this, 'custom_excerpt_length' ) );
		add_filter( 'the_author_description', 'wpautop' );
	}
	
	/**
	 * Add Notice for the Welcome Notice
	 */
	public function base_welcome_notice() {
		if ( ! defined( 'AVANAM_VERSION' ) || get_option( 'base_disable_welcome_notice' ) ) {
			return;
		}
		?>
		<div id="base-notice-welcome-plugin" class="notice is-dismissible notice-info">
			<h2 class="notice-title"><?php echo esc_html__( 'Thanks for choosing the Avanam Theme!', 'avanam' ); ?></h2>
			<p class="base-notice-description"><?php /* translators: %s: <a> */ printf( esc_html__( 'Using %1$sAvanam Theme%2$s you can customize the styling of your site - including header fooder layout, font, colors and spacing - in your theme settings.', 'avanam' ), '<a href="https://avanam.org/" target="_blank">', '</a>' ); ?></p>
			<?php if ( !isset( $_GET['page'] ) || $_GET['page'] !== 'avanam' ) { ?>
			<p class="install-submit">
				<a href="<?php echo esc_url( admin_url( 'themes.php?page=avanam' ) ); ?>"><button class="button button-primary base-theme-settings"><?php echo esc_html__( 'Go to Theme Settings', 'avanam' ); ?></button></a>
			</p>
			<?php } ?>
		</div>
		<?php
		wp_enqueue_script( 'base-welcome-deactivate' );
		wp_localize_script(
			'base-welcome-deactivate',
			'baseWelcomeDeactivate',
			array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'base-ajax-verification' ),
			)
		);
	}

	/**
	 * Run check to see if we need to dismiss the notice.
	 * If all tests are successful then call the dismiss_notice() method.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function ajax_dismiss_welcome_notice() {

		// Sanity check: Early exit if we're not on a wptrt_dismiss_notice action.
		if ( ! isset( $_POST['action'] ) || 'base_dismiss_welcome_notice' !== $_POST['action'] ) {
			return;
		}
		// Security check: Make sure nonce is OK.
		check_ajax_referer( 'base-ajax-verification', 'security', true );

		// If we got this far, we need to dismiss the notice.
		update_option( 'base_disable_welcome_notice', true, false );
	}

	/**
	 * Add Notice for Base Starter templates
	 */
	public function base_starter_templates_notice() {
		if ( defined( 'AVANAM_STARTER_VERSION' ) || get_option( 'base_starter_plugin_notice' ) || ! current_user_can( 'install_plugins' ) ) {
			return;
		}
		$installed_plugins = get_plugins();
		if ( ! isset( $installed_plugins['avanam-starter/avanam-starter.php'] ) ) {
			$button_label = esc_html__( 'Install Base Starter Templates', 'avanam' );
			$data_action  = 'install';
		} elseif ( ! self::active_plugin_check( 'avanam-starter/avanam-starter.php' ) ) {
			$button_label = esc_html__( 'Activate Base Starter Templates', 'avanam' );
			$data_action  = 'activate';
		} else {
			return;
		}
		?>
		<div id="base-notice-starter-templates" class="notice is-dismissible notice-info">
			<h2 class="notice-title"><?php echo esc_html__( 'Thanks for choosing the Avanam Theme!', 'avanam' ); ?></h2>
			<p class="base-notice-description"><?php /* translators: %s: <strong> <a> */ printf( esc_html__( 'Want to get started with a beautiful %1$sstarter template%2$s? Install the Base Starter Templates plugin to launch an optimized design in minutes.', 'avanam' ), '<a href="https://wordpress.org/plugins/base-starter-templates/" target="_blank">', '</a>', '<strong>', '</strong>' ); ?></p>
			<p class="install-submit">
				<button class="button button-primary base-install-starter-btn" data-redirect-url="<?php echo esc_url( admin_url( 'themes.php?page=avanam-starter' ) ); ?>" data-activating-label="<?php echo esc_attr__( 'Activating...', 'avanam' ); ?>" data-activated-label="<?php echo esc_attr__( 'Activated', 'avanam' ); ?>" data-installing-label="<?php echo esc_attr__( 'Installing...', 'avanam' ); ?>" data-installed-label="<?php echo esc_attr__( 'Installed', 'avanam' ); ?>" data-action="<?php echo esc_attr( $data_action ); ?>"><?php echo esc_html( $button_label ); ?></button>
			</p>
		</div>
		<?php
		wp_enqueue_script( 'base-starter-install' );
		wp_localize_script(
			'base-starter-install',
			'baseStarterInstall',
			array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'base-ajax-verification' ),
				'status'     => $data_action,
			)
		);
	}
	/**
	 * Add Notice for to not use the Gutenberg Plugin
	 */
	public function base_turn_off_gutenberg_plugin_notice() {
		if ( ! defined( 'GUTENBERG_VERSION' ) || get_option( 'base_disable_gutenberg_notice' ) || ! current_user_can( 'install_plugins' ) ) {
			return;
		}
		?>
		<div id="base-notice-gutenberg-plugin" class="notice is-dismissible notice-warning">
			<h2 class="notice-title"><?php echo esc_html__( 'Gutenberg Plugin Detected', 'avanam' ); ?></h2>
			<p class="base-notice-description"><?php /* translators: %s: <a> */ printf( esc_html__( 'The %1$sGutenberg plugin%2$s is not recommended for use in a production site. Many things may be broken by using this plugin. Please deactivate.', 'avanam' ), '<a href="https://wordpress.org/plugins/gutenberg/" target="_blank">', '</a>' ); ?></p>
		</div>
		<?php
		wp_enqueue_script( 'base-gutenberg-deactivate' );
		wp_localize_script(
			'base-gutenberg-deactivate',
			'baseGutenbergDeactivate',
			array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'base-ajax-verification' ),
			)
		);
	}
	/**
	 * Run check to see if we need to dismiss the notice.
	 * If all tests are successful then call the dismiss_notice() method.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function ajax_dismiss_gutenberg_notice() {

		// Sanity check: Early exit if we're not on a wptrt_dismiss_notice action.
		if ( ! isset( $_POST['action'] ) || 'base_dismiss_gutenberg_notice' !== $_POST['action'] ) {
			return;
		}
		// Security check: Make sure nonce is OK.
		check_ajax_referer( 'base-ajax-verification', 'security', true );

		// If we got this far, we need to dismiss the notice.
		update_option( 'base_disable_gutenberg_notice', true, false );
	}
	/**
	 * Run check to see if we need to dismiss the notice.
	 * If all tests are successful then call the dismiss_notice() method.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function ajax_dismiss_starter_notice() {

		// Sanity check: Early exit if we're not on a wptrt_dismiss_notice action.
		if ( ! isset( $_POST['action'] ) || 'base_dismiss_notice' !== $_POST['action'] ) {
			return;
		}
		// Security check: Make sure nonce is OK.
		check_ajax_referer( 'base-ajax-verification', 'security', true );

		// If we got this far, we need to dismiss the notice.
		update_option( 'base_starter_plugin_notice', true, false );
	}

	/**
	 * Option to Install Starter Templates
	 */
	public function install_starter_script() {
		wp_register_script( 'base-starter-install', get_template_directory_uri() . '/assets/js/admin-activate.min.js', array( 'jquery' ), AVANAM_VERSION, false );
		wp_register_script( 'base-gutenberg-deactivate', get_template_directory_uri() . '/assets/js/gutenberg-notice.min.js', array( 'jquery' ), AVANAM_VERSION, false );
	}
	/**
	 * Active Plugin Check
	 *
	 * @param string $plugin_base_name is plugin folder/filename.php.
	 */
	public static function active_plugin_check( $plugin_base_name ) {

		if ( ! self::$active_plugins ) {
			self::get_active_plugins();
		}
		return in_array( $plugin_base_name, self::$active_plugins, true ) || array_key_exists( $plugin_base_name, self::$active_plugins );
	}
	/**
	 * Initialize getting the active plugins list.
	 */
	public static function get_active_plugins() {

		self::$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}
	}
	/**
	 * AJAX callback to install a plugin.
	 */
	public function install_plugin_ajax_callback() {
		if ( ! check_ajax_referer( 'base-ajax-verification', 'security', false ) ) {
			wp_send_json_error( __( 'Security Error, Please reload the page.', 'avanam' ) );
		}
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( __( 'Security Error, Need higher Permissions to install plugin.', 'avanam' ) );
		}
		// Get selected file index or set it to 0.
		$status = empty( $_POST['status'] ) ? 'install' : sanitize_text_field( $_POST['status'] );
		if ( ! function_exists( 'plugins_api' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		}
		if ( ! class_exists( 'WP_Upgrader' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}
		$install = true;
		if ( 'install' === $status ) {
			$api = plugins_api(
				'plugin_information',
				array(
					'slug' => 'avanam-starter',
					'fields' => array(
						'short_description' => false,
						'sections' => false,
						'requires' => false,
						'rating' => false,
						'ratings' => false,
						'downloaded' => false,
						'last_updated' => false,
						'added' => false,
						'tags' => false,
						'compatibility' => false,
						'homepage' => false,
						'donate_link' => false,
					),
				)
			);
			if ( ! is_wp_error( $api ) ) {

				// Use AJAX upgrader skin instead of plugin installer skin.
				// ref: function wp_ajax_install_plugin().
				$upgrader = new \Plugin_Upgrader( new \WP_Ajax_Upgrader_Skin() );

				$installed = $upgrader->install( $api->download_link );
				if ( $installed ) {
					$activate = activate_plugin( 'avanam-starter/avanam-starter.php', '', false, true );
					if ( is_wp_error( $activate ) ) {
						$install = false;
					}
				} else {
					$install = false;
				}
			} else {
				$install = false;
			}
		} elseif ( 'activate' === $status ) {
			$activate = activate_plugin( 'avanam-starter/avanam-starter.php', '', false, true );
			if ( is_wp_error( $activate ) ) {
				$install = false;
			}
		}

		if ( false === $install ) {
			wp_send_json_error( __( 'Error, plugin could not be installed, please install manually.', 'avanam' ) );
		} else {
			wp_send_json_success();
		}
	}
	/**
	 * Filter the excerpt length to 30 words.
	 *
	 * @param int $length Excerpt length.
	 * @return int (Maybe) modified excerpt length.
	 */
	public function custom_excerpt_length( $length ) {
		if ( is_admin() ) {
			return $length;
		}
		if ( is_search() ) {
			if ( webapp()->sub_option( 'search_archive_element_excerpt', 'words' ) ) {
				$length = intval( webapp()->sub_option( 'search_archive_element_excerpt', 'words' ) );
			}
		} else if ( 'post' === get_post_type() ) {
			if ( webapp()->sub_option( 'post_archive_element_excerpt', 'words' ) ) {
				$length = intval( webapp()->sub_option( 'post_archive_element_excerpt', 'words' ) );
			}
		} else {
			if ( webapp()->sub_option( get_post_type() . '_archive_element_excerpt', 'words' ) ) {
				$length = intval( webapp()->sub_option( get_post_type() . '_archive_element_excerpt', 'words' ) );
			}
		}
		return $length;
	}
	/**
	 * Remove comment date.
	 *
	 * @param string|int $date      The comment time, formatted as a date string or Unix timestamp.
	 * @param string     $format    Date format.
	 * @param bool       $gmt       Whether the GMT date is in use.
	 * @param bool       $translate Whether the time is translated.
	 * @param WP_Comment $comment   The comment object.
	 */
	public function remove_comment_time( $date, $format, $gmt, $translate, $comment ) {
		if ( ! is_admin() ) {
			return '';
		}
		return $date;
	}
	/**
	 * Remove comment date.
	 *
	 * @param string|int $date    Formatted date string or Unix timestamp.
	 * @param string     $format  The format of the date.
	 * @param WP_Comment $comment The comment object.
	 */
	public function remove_comment_date( $date, $format, $comment ) {
		if ( ! is_admin() ) {
			return '';
		}
		return $date;
	}
	/**
	 * Wrap embedded media for responsive embeds, pre blocks.
	 *
	 * @param  string $cache The oEmbed markup.
	 * @param  string $url The URL being embedded.
	 * @param  array  $attr An array of attributes.
	 * @param  string $post_ID the post id.
	 */
	public function classic_embed_wrap( $cache, $url, $attr = array(), $post_ID = '' ) {
		if ( doing_filter( 'the_content' ) && ! has_blocks() && ! empty( $cache ) && ! empty( $url ) ) {
			$do_wrap = false;
			$patterns = array(
				'#http://((m|www)\.)?youtube\.com/watch.*#i',
				'#https://((m|www)\.)?youtube\.com/watch.*#i',
				'#http://((m|www)\.)?youtube\.com/playlist.*#i',
				'#https://((m|www)\.)?youtube\.com/playlist.*#i',
				'#http://youtu\.be/.*#i',
				'#https://youtu\.be/.*#i',
				'#https?://(.+\.)?vimeo\.com/.*#i',
				'#https?://(www\.)?dailymotion\.com/.*#i',
				'#https?://dai.ly/*#i',
				'#https?://(www\.)?hulu\.com/watch/.*#i',
				'#https?://wordpress.tv/.*#i',
				'#https?://(www\.)?funnyordie\.com/videos/.*#i',
				'#https?://vine.co/v/.*#i',
				'#https?://(www\.)?collegehumor\.com/video/.*#i',
				'#https?://(www\.|embed\.)?ted\.com/talks/.*#i'
			);
			$patterns = apply_filters( 'base_maybe_wrap_embed_patterns', $patterns );
			foreach ( $patterns as $pattern ) {
				$do_wrap = preg_match( $pattern, $url );
				if ( $do_wrap ) {
					return '<div class="entry-content-asset videofit">' . $cache . '</div>';
				}
			}
		}
		return $cache;
	}

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `webapp()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() : array {
		return array(
			'get_version'                          => array( $this, 'get_version' ),
			'get_asset_version'                    => array( $this, 'get_asset_version' ),
			'get_post_types'                       => array( $this, 'get_post_types' ),
			'get_post_types_objects'               => array( $this, 'get_post_types_objects' ),
			'get_post_types_to_ignore'             => array( $this, 'get_post_types_to_ignore' ),
			'get_transparent_post_types_to_ignore' => array( $this, 'get_transparent_post_types_to_ignore' ),
			'get_public_post_types_to_ignore'      => array( $this, 'get_public_post_types_to_ignore' ),
			'customizer_quick_link'                => array( $this, 'customizer_quick_link' ),
		);
	}
	/**
	 * If in customizer output the quicklink.
	 */
	public static function customizer_quick_link() {
		if ( is_customize_preview() ) {
			?>
			<div class="customize-partial-edit-shortcut base-custom-partial-edit-shortcut">
				<button aria-label="<?php esc_attr_e( 'Click to edit this element.', 'avanam' ); ?>" title="<?php esc_attr_e( 'Click to edit this element.', 'avanam' ); ?>" class="customize-partial-edit-shortcut-button item-customizer-focus"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13.89 3.39l2.71 2.72c.46.46.42 1.24.03 1.64l-8.01 8.02-5.56 1.16 1.16-5.58s7.6-7.63 7.99-8.03c.39-.39 1.22-.39 1.68.07zm-2.73 2.79l-5.59 5.61 1.11 1.11 5.54-5.65zm-2.97 8.23l5.58-5.6-1.07-1.08-5.59 5.6z"></path></svg></button>
			</div>
			<?php
		}
	}
	/**
	 * Get array of post types we want to exclude from use in customizer custom post type settings.
	 *
	 * @return array of post types.
	 */
	public static function get_post_types_to_ignore() {
		if ( is_null( self::$ignore_post_types ) ) {
			$ignore_post_types = array(
				'post',
				'page',
				'product',
				'base_element',
				'base_conversions',
				'base_cloud',
				'base_wootemplate',
				'elementor_library',
				'bst_size_chart',
				'bst_cart_notice',
				'bst_reviews',
				'bst_product_tabs',
				'ele-product-template',
				'ele-p-arch-template',
				'ele-p-loop-template',
				'ele-check-template',
				'shop_order',
				'jet-menu',
				'jet-popup',
				'jet-smart-filters',
				'jet-theme-core',
				'jet-woo-builder',
				'jet-engine',
				'course',
				'lesson',
				'llms_quiz',
				'llms_membership',
				'llms_certificate',
				'llms_my_certificate',
				'sfwd-quiz',
				'sfwd-certificates',
				'sfwd-lessons',
				'sfwd-topic',
				'sfwd-transactions',
				'sfwd-essays',
				'sfwd-assignment',
				'sfwd-courses',
				'tutor_quiz',
				'tutor_assignments',
				'courses',
				'groups',
				'forum',
				'topic',
				'reply',
				'tribe_events',
			);
			self::$ignore_post_types = apply_filters( 'base_customizer_post_type_ignore_array', $ignore_post_types );
		}

		return self::$ignore_post_types;
	}

	/**
	 * Get array of post types we want to exclude from use in customizer transparent header settings.
	 *
	 * @return array of post types.
	 */
	public static function get_transparent_post_types_to_ignore() {
		if ( is_null( self::$transparent_ignore_post_types ) ) {
			$transparent_ignore_post_types = array(
				'post',
				'page',
				'product',
				'base_element',
				'base_conversions',
				'base_cloud',
				'base_wootemplate',
				'elementor_library',
				'fl-theme-layout',
				'bst_size_chart',
				'bst_cart_notice',
				'bst_reviews',
				'bst_product_tabs',
				'shop_order',
				'ele-product-template',
				'ele-p-arch-template',
				'ele-p-loop-template',
				'ele-check-template',
				'jet-menu',
				'jet-popup',
				'jet-smart-filters',
				'jet-theme-core',
				'jet-woo-builder',
				'jet-engine',
				'llms_certificate',
				'llms_my_certificate',
				'sfwd-quiz',
				'ld-exam',
				'sfwd-certificates',
				'sfwd-lessons',
				'sfwd-topic',
				'sfwd-transactions',
				'sfwd-essays',
				'sfwd-assignment',
				'tutor_quiz',
				'tutor_assignments',
			);
			self::$transparent_ignore_post_types = apply_filters( 'base_transparent_post_type_ignore_array', $transparent_ignore_post_types );
		}

		return self::$transparent_ignore_post_types;
	}

	/**
	 * Get array of post types we want to exclude from use in non public areas.
	 *
	 * @return array of post types.
	 */
	public static function get_public_post_types_to_ignore() {
		if ( is_null( self::$public_ignore_post_types ) ) {
			$public_ignore_post_types = array(
				'elementor_library',
				'fl-theme-layout',
				'bst_size_chart',
				'bst_cart_notice',
				'bst_reviews',
				'bst_product_tabs',
				'shop_order',
				'base_element',
				'base_conversions',
				'base_cloud',
				'base_wootemplate',
				'ele-product-template',
				'ele-p-arch-template',
				'ele-p-loop-template',
				'ele-check-template',
				'jet-menu',
				'jet-popup',
				'jet-smart-filters',
				'jet-theme-core',
				'jet-woo-builder',
				'jet-engine',
				'llms_certificate',
				'llms_my_certificate',
				'sfwd-certificates',
				'sfwd-transactions',
				'reply',
			);
			self::$public_ignore_post_types = apply_filters( 'base_public_post_type_ignore_array', $public_ignore_post_types );
		}

		return self::$public_ignore_post_types;
	}

	/**
	 * Get all public post types.
	 *
	 * @return array of post types.
	 */
	public static function get_post_types() {
		if ( is_null( self::$post_types ) ) {
			$args             = array(
				'public' => true,
				'show_in_rest' => true,
				'_builtin' => false,
			);
			$builtin = array(
				'post',
				'page',
			);
			$output           = 'names'; // names or objects, note names is the default.
			$operator         = 'and';
			$post_types       = get_post_types( $args, $output, $operator );
			self::$post_types = apply_filters( 'base_public_post_type_array', array_merge( $builtin, $post_types ) );
		}

		return self::$post_types;
	}

	/**
	 * Get all public post types.
	 *
	 * @return array of post types.
	 */
	public static function get_post_types_objects() {
		if ( is_null( self::$post_types_objects ) ) {
			$args             = array(
				'public' => true,
				'_builtin' => false,
			);
			$output           = 'objects'; // names or objects, note names is the default.
			$operator         = 'and';
			$post_types       = get_post_types( $args, $output, $operator );
			self::$post_types_objects = apply_filters( 'base_public_post_type_objects', $post_types );
		}

		return self::$post_types_objects;
	}

	/**
	 * Adds theme support for essential features.
	 */
	public function action_essential_theme_support() {
		if ( 'em' === webapp()->sub_option( 'content_width', 'unit' ) || 'rem' === webapp()->sub_option( 'content_width', 'unit' ) ) {
			$base_content_width = intval( webapp()->sub_option( 'content_width', 'size' ) * 17 );
		} else {
			$base_content_width = webapp()->sub_option( 'content_width', 'size' );
		}
		$GLOBALS['content_width'] = intval( $base_content_width ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedVariableFound

		// Add default RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// Ensure WordPress manages the document title.
		add_theme_support( 'title-tag' );

		// Ensure WordPress theme features render in HTML5 markup.
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'script',
				'style',
			)
		);

		add_theme_support( 'custom-units' );
		add_theme_support( 'custom-line-height' );

		// Add support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Add support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );

		if ( ! webapp()->option( 'post_comments_date' ) ) {
			add_filter( 'get_comment_date', array( $this, 'remove_comment_date' ), 20, 3 );
			add_filter( 'get_comment_time', array( $this, 'remove_comment_time' ), 20, 5 );
		}

	}
	/**
	 * Adds a tiny script to remove no-js class.
	 */
	public function action_add_no_js_remove_script() {
		if ( ! webapp()->is_amp() ) {
			?>
			<script>document.documentElement.classList.remove( 'no-js' );</script>
			<?php
		}
	}
	/**
	 * Adds a tiny script to set scrollbar offset.
	 */
	public function action_add_scrollbar_offset_script() {
		if ( ! webapp()->is_amp() ) {
			?>
			<script>document.documentElement.style.setProperty('--scrollbar-offset', window.innerWidth - document.documentElement.clientWidth + 'px' );</script>
			<?php
		}
	}
	/**
	 * Adds a pingback url auto-discovery header for singularly identifiable articles.
	 */
	public function action_add_pingback_header() {
		if ( is_singular() && pings_open() ) {
			echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
		}
	}
	/**
	 * Add
	 *
	 * @param string $markup search form markup.
	 *
	 * @return mixed
	 */
	public function add_search_icon( $markup ) {
		$markup = str_replace( '</form>', '<div class="base-search-icon-wrap">' . webapp()->get_icon( 'search', '', false ) . '</div></form>', $markup );
		return $markup;
	}
	/**
	 * Adds a 'hfeed' class to the array of body classes for non-singular pages.
	 *
	 * @param array $classes Classes for the body element.
	 * @return array Filtered body classes.
	 */
	public function filter_body_classes_add_hfeed( array $classes ) : array {
		if ( ! is_singular() ) {
			$classes[] = 'hfeed';
		}

		return $classes;
	}

	/**
	 * Adds a link style class to the array of body classes.
	 *
	 * @param array $classes Classes for the body element.
	 * @return array Filtered body classes.
	 */
	public function filter_body_classes_add_link_style( array $classes ) : array {
		if ( ! webapp()->option( 'enable_scroll_to_id' ) ) {
			$classes[] = 'no-anchor-scroll';
		}
		if ( webapp()->option( 'enable_footer_on_bottom' ) ) {
			$classes[] = 'footer-on-bottom';
		}
		if ( webapp()->option( 'enable_popup_body_animate' ) ) {
			$classes[] = 'animate-body-popup';
		}
		if ( '' !== webapp()->option( 'header_social_brand' ) || '' !== webapp()->option( 'header_mobile_social_brand' ) || '' !== webapp()->option( 'footer_social_brand' ) ) {
			$classes[] = 'social-brand-colors';
		}
		$classes[] = 'hide-focus-outline';
		$classes[] = 'link-style-' . webapp()->sub_option( 'link_color', 'style' );

		return $classes;
	}

	/**
	 * Sets the embed width in pixels, based on the theme's design and stylesheet.
	 *
	 * @param array $dimensions An array of embed width and height values in pixels (in that order).
	 * @return array Filtered dimensions array.
	 */
	public function filter_embed_dimensions( array $dimensions ) : array {
		$dimensions['width'] = 720;
		return $dimensions;
	}

	/**
	 * Excludes any directory named 'optional' from being scanned for theme template files.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/theme_scandir_exclusions/
	 *
	 * @param array $exclusions the default directories to exclude.
	 * @return array Filtered exclusions.
	 */
	public function filter_scandir_exclusions_for_optional_templates( array $exclusions ) : array {
		return array_merge(
			$exclusions,
			array( 'optional' )
		);
	}

	/**
	 * Adds async/defer attributes to enqueued / registered scripts.
	 *
	 * If #12009 lands in WordPress, this function can no-op since it would be handled in core.
	 *
	 * @link https://core.trac.wordpress.org/ticket/12009
	 *
	 * @param string $tag    The script tag.
	 * @param string $handle The script handle.
	 * @return string Script HTML string.
	 */
	public function filter_script_loader_tag( $tag, $handle ) {

		foreach ( array( 'async', 'defer' ) as $attr ) {
			if ( ! wp_scripts()->get_data( $handle, $attr ) ) {
				continue;
			}

			// Prevent adding attribute when already added in #12009.
			if ( ! preg_match( ":\s$attr(=|>|\s):", $tag ) ) {
				$tag = preg_replace( ':(?=></script>):', " $attr", $tag, 1 );
			}

			// Only allow async or defer, not both.
			break;
		}

		return $tag;
	}

	/**
	 * Gets the theme version.
	 *
	 * @return string Theme version number.
	 */
	public function get_version() : string {
		static $theme_version = null;

		if ( null === $theme_version ) {
			$theme_version = wp_get_theme( get_template() )->get( 'Version' );
		}

		return $theme_version;
	}

	/**
	 * Gets the version for a given asset.
	 *
	 * Returns filemtime when WP_DEBUG is true, otherwise the theme version.
	 *
	 * @param string $filepath Asset file path.
	 * @return string Asset version number.
	 */
	public function get_asset_version( string $filepath ) : string {
		if ( WP_DEBUG ) {
			return (string) filemtime( $filepath );
		}

		return $this->get_version();
	}
}
