<?php
/**
 * Build Welcome Page with settings.
 *
 * @package Base
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Build Welcome Page class
 *
 * @category class
 */
class Base_Dashboard_Settings {

	/**
	 * Settings of this class
	 *
	 * @var array
	 */
	public static $settings = array();

	/**
	 * Instance of this class
	 *
	 * @var null
	 */
	private static $instance = null;
	/**
	 * Static var active plugins
	 *
	 * @var $active_plugins
	 */
	private static $active_plugins;

	/**
	 * Instance Control
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Class Constructor.
	 */
	public function __construct() {
		// only load if admin.
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'add_menu' ) );
		}
		add_action( 'init', array( $this, 'load_api_settings' ) );
	}


	/**
	 * Returns a base64 URL for the SVG for use in the menu.
	 *
	 * @param  bool $base64 Whether or not to return base64-encoded SVG.
	 * @return string
	 */
	private function get_icon_svg( $base64 = true ) {
		$svg = '<svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="512.000000pt" height="512.000000pt" viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet"><g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="#9ca2a7" stroke="none"><path d="M3786 4604 c-27 -8 -61 -23 -75 -32 -49 -30 -113 -97 -137 -144 -65 -128 -47 -329 42 -445 l25 -33 -340 0 -341 0 1 -27 c0 -16 17 -134 37 -263 l37 -235 258 -3 257 -2 0 -450 0 -450 -592 0 -591 0 59 63 c140 146 215 308 233 505 25 275 -63 511 -256 692 -296 276 -749 338 -1358 184 -143 -36 -285 -83 -285 -94 0 -10 190 -532 196 -539 2 -2 54 16 116 39 62 23 165 56 228 73 104 27 130 30 275 31 144 1 166 -1 215 -22 81 -32 132 -74 165 -134 25 -46 29 -64 30 -133 0 -96 -28 -165 -94 -233 -104 -107 -325 -203 -623 -272 -54 -12 -98 -28 -98 -34 0 -6 35 -129 78 -274 l77 -263 50 6 c86 11 285 55 368 82 l79 24 40 -27 c68 -47 163 -159 201 -236 119 -241 38 -513 -187 -623 -74 -36 -202 -58 -296 -50 -350 29 -602 276 -766 749 -20 58 -40 106 -44 106 -4 0 -113 -47 -241 -104 -217 -96 -234 -106 -231 -127 2 -13 24 -84 48 -157 87 -265 193 -442 379 -628 279 -280 598 -414 987 -414 492 0 883 207 1042 552 86 186 89 443 6 655 l-28 73 409 0 409 0 0 -720 0 -720 335 0 335 0 0 1435 0 1435 305 0 c291 0 305 1 305 19 0 10 -16 127 -35 261 -19 134 -35 244 -35 246 0 2 -147 4 -327 4 l-327 0 36 38 c53 54 88 138 95 225 13 179 -53 314 -185 375 -70 32 -194 39 -266 16z"/></g></svg>';

		if ( $base64 ) {
			return 'data:image/svg+xml;base64,' . base64_encode( $svg );
		}

		return $svg;
	}
	/**
	 * Allow settings visibility to be changed.
	 */
	public function settings_user_capabilities() {
		$cap = apply_filters( 'base_admin_settings_capability', 'manage_options' );
		return $cap;
	}
	/**
	 * Redirect to the settings page on activation.
	 *
	 * @param string $key setting key.
	 */
	public static function get_data_options( $key ) {
		if ( ! isset( self::$settings[ $key ] ) ) {
			self::$settings[ $key ] = get_option( $key, array() );
		}
		return self::$settings[ $key ];
	}
	/**
	 * Add option page menu
	 */
	public function add_menu() {
		add_menu_page( __( 'Avanam - Next Generation Theme', 'avanam' ), __( 'Avanam', 'avanam' ), $this->settings_user_capabilities(), 'avanam', null, $this->get_icon_svg(), 30 );
		$page = add_submenu_page( 'avanam', __( 'Avanam - Next Generation Theme', 'avanam' ), __( 'Settings', 'avanam' ), $this->settings_user_capabilities(), 'avanam', array( $this, 'config_page' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'scripts' ) );
		do_action( 'base_theme_admin_menu' );
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
	 * Loads admin style sheets and scripts
	 */
	public function scripts() {
		$installed_plugins = get_plugins();
		$button_label = esc_html__( 'Browse Base Starter Templates', 'avanam' );
		$data_action  = '';
		if ( ! defined( 'AVANAM_STARTER_VERSION' ) ) {
			if ( ! isset( $installed_plugins['avanam-starter/avanam-starter.php'] ) ) {
				$button_label = esc_html__( 'Install Base Starter Templates', 'avanam' );
				$data_action  = 'install';
			} elseif ( ! self::active_plugin_check( 'avanam-starter/avanam-starter.php' ) ) {
				$button_label = esc_html__( 'Activate Base Starter Templates', 'avanam' );
				$data_action  = 'activate';
			}
		}
		wp_enqueue_style( 'base-dashboard', get_template_directory_uri() . '/inc/dashboard/react/dash-controls.min.css', array( 'wp-components' ), AVANAM_VERSION );
		wp_enqueue_script( 'base-dashboard', get_template_directory_uri() . '/assets/js/admin/dashboard.js', array( 'wp-i18n', 'wp-element', 'wp-plugins', 'wp-components', 'wp-api', 'wp-hooks', 'wp-edit-post', 'lodash', 'wp-block-library', 'wp-block-editor', 'wp-editor', 'jquery' ), AVANAM_VERSION, true );
		wp_localize_script(
			'base-dashboard',
			'baseDashboardParams',
			array(
				'adminURL' => esc_url( admin_url() ),
				'settings' => esc_attr( get_option( 'avanam_theme_config' ) ),
				'changelog' => $this->get_changelog(),
				'proChangelog' => ( class_exists( 'Base_Theme_Pro' ) ? $this->get_pro_changelog() : '' ),
				'starterTemplates' => ( defined( 'AVANAM_STARTER_VERSION' ) ? true : false ),
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'base-ajax-verification' ),
				'proURL'       => esc_url( \Base\webapp()->get_pro_url( 'https://avanam.org/base-theme/premium/', 'https://avanam.org/base-theme/premium/', 'in-app', 'theme-dash' ) ),
				'status'       => $data_action,
				'starterLabel' => $button_label,
				'starterImage' => esc_attr( get_template_directory_uri() . '/assets/images/starter-templates-banner.jpeg' ),
				'starterURL' => esc_url( class_exists( '\\BaseWP\\BaseBlocks\\AvanamOrg\\Uplink\\Register' ) ? admin_url( 'admin.php?page=avanam-starter' ) : admin_url( 'themes.php?page=avanam-starter' ) ),
				'videoImage' => esc_attr( get_template_directory_uri() . '/assets/images/getting-started-video.jpg' ),
			)
		);
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'base-dashboard', 'avanam' );
		}
	}
	/**
	 * Get Changelog ( Largely Borrowed From Neve Theme )
	 */
	public function get_changelog() {
		$changelog      = array();
		$changelog_path = get_template_directory() . '/changelog.txt';
		if ( ! is_file( $changelog_path ) ) {
			return $changelog;
		}
		global $wp_filesystem;
		if ( ! is_object( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		$changelog_string = $wp_filesystem->get_contents( $changelog_path );
		if ( is_wp_error( $changelog_string ) ) {
			return $changelog;
		}
		$changelog = explode( PHP_EOL, $changelog_string );
		$releases  = [];
		foreach ( $changelog as $changelog_line ) {
			if ( empty( $changelog_line ) ) {
				continue;
			}
			if ( substr( ltrim( $changelog_line ), 0, 2 ) === '==' ) {
				if ( isset( $release ) ) {
					$releases[] = $release;
				}
				$changelog_line = trim( str_replace( '=', '', $changelog_line ) );
				$release = array(
					'head'    => $changelog_line,
				);
			} else {
				if ( preg_match( '/[*|-]?\s?(\[fix]|\[Fix]|fix|Fix)[:]?\s?\b/', $changelog_line ) ) {
					//$changelog_line     = preg_replace( '/[*|-]?\s?(\[fix]|\[Fix]|fix|Fix)[:]?\s?\b/', '', $changelog_line );
					$changelog_line = trim( str_replace( [ '*', '-' ], '', $changelog_line ) );
					$release['fix'][] = $changelog_line;
					continue;
				}

				if ( preg_match( '/[*|-]?\s?(\[add]|\[Add]|add|Add)[:]?\s?\b/', $changelog_line ) ) {
					//$changelog_line        = preg_replace( '/[*|-]?\s?(\[add]|\[Add]|add|Add)[:]?\s?\b/', '', $changelog_line );
					$changelog_line = trim( str_replace( [ '*', '-' ], '', $changelog_line ) );
					$release['add'][] = $changelog_line;
					continue;
				}
				$changelog_line = trim( str_replace( [ '*', '-' ], '', $changelog_line ) );
				$release['update'][] = $changelog_line;
			}
		}
		return $releases;
	}
	/**
	 * Get Changelog ( Largely Borrowed From Neve Theme )
	 */
	public function get_pro_changelog() {
		$changelog      = array();
		if ( ! defined( 'BTP_PATH' ) ) {
			return $changelog;
		}
		$changelog_path = BTP_PATH . '/changelog.txt';
		if ( ! is_file( $changelog_path ) ) {
			return $changelog;
		}
		global $wp_filesystem;
		if ( ! is_object( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		$changelog_string = $wp_filesystem->get_contents( $changelog_path );
		if ( is_wp_error( $changelog_string ) ) {
			return $changelog;
		}
		$changelog = explode( PHP_EOL, $changelog_string );
		$releases  = [];
		foreach ( $changelog as $changelog_line ) {
			if ( empty( $changelog_line ) ) {
				continue;
			}
			if ( substr( ltrim( $changelog_line ), 0, 2 ) === '==' ) {
				if ( isset( $release ) ) {
					$releases[] = $release;
				}
				$changelog_line = trim( str_replace( '=', '', $changelog_line ) );
				$release = array(
					'head'    => $changelog_line,
				);
			} else {
				if ( preg_match( '/[*|-]?\s?(\[fix]|\[Fix]|fix|Fix)[:]?\s?\b/', $changelog_line ) ) {
					//$changelog_line     = preg_replace( '/[*|-]?\s?(\[fix]|\[Fix]|fix|Fix)[:]?\s?\b/', '', $changelog_line );
					$changelog_line = trim( str_replace( [ '*', '-' ], '', $changelog_line ) );
					$release['fix'][] = $changelog_line;
					continue;
				}

				if ( preg_match( '/[*|-]?\s?(\[add]|\[Add]|add|Add)[:]?\s?\b/', $changelog_line ) ) {
					//$changelog_line        = preg_replace( '/[*|-]?\s?(\[add]|\[Add]|add|Add)[:]?\s?\b/', '', $changelog_line );
					$changelog_line = trim( str_replace( [ '*', '-' ], '', $changelog_line ) );
					$release['add'][] = $changelog_line;
					continue;
				}
				$changelog_line = trim( str_replace( [ '*', '-' ], '', $changelog_line ) );
				$release['update'][] = $changelog_line;
			}
		}
		return $releases;
	}

	/**
	 * Register settings
	 */
	public function load_api_settings() {

		register_setting(
			'avanam_theme_config',
			'avanam_theme_config',
			array(
				'type'              => 'string',
				'description'       => __( 'Config Theme Modules', 'avanam' ),
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
				'default'           => '',
			)
		);
	}

	/**
	 * Loads config page
	 */
	public function config_page() {
		?>
		<div class="base_theme_dash_head">
			<div class="base_theme_dash_head_container">
				<div class="base_theme_dash_logo">
					<img src="<?php echo esc_attr( apply_filters( 'base_theme_dashboard_logo', get_template_directory_uri() . '/assets/images/avanam-logo.png' ) ); ?>">
				</div>
				<div class="base_theme_dash_version">
					<span>
						<strong><?php esc_html_e( 'Avanam', 'avanam' ); ?></strong>
						<?php echo esc_html( AVANAM_VERSION ); ?>
					</span>
				</div>
			</div>
		</div>
		<div class="wrap base_theme_dash">
			<div class="base_theme_dashboard">
				<h2 class="notices" style="display:none;"></h2>
				<?php settings_errors(); ?>
				<div class="page-grid">
					<div class="base_theme_dashboard_main">
					</div>
					<div class="side-panel">
						<?php do_action( 'base_theme_dash_side_panel' ); ?>
						<div class="support-section sidebar-section components-panel">
							<div class="components-panel__body is-opened">
								<h2><?php esc_html_e( 'Video Tutorials', 'avanam' ); ?></h2>
								<p><?php esc_html_e( 'Want a guide? We have video tutorials to walk you through getting started.', 'avanam' ); ?></p>
								<a href="https://avanam.org/wordpress#learn?utm_source=in-app&utm_medium=theme-dash&utm_campaign=videos" target="_blank" class="sidebar-link"><?php esc_html_e( 'Watch Videos', 'avanam' ); ?></a>
							</div>
						</div>
						<div class="support-section sidebar-section components-panel">
							<div class="components-panel__body is-opened">
								<h2><?php esc_html_e( 'Documentation', 'avanam' ); ?></h2>
								<p><?php esc_html_e( 'Need help? We have a knowledge base full of articles to get you started.', 'avanam' ); ?></p>
								<a href="<?php echo esc_url( \Base\webapp()->get_pro_url( 'https://avanam.org/wordpress#kb', 'https://avanam.org/wordpress#kb', 'in-app', 'theme-dash', 'docs' ) ); ?>" target="_blank" class="sidebar-link"><?php esc_html_e( 'Browse Docs', 'avanam' ); ?></a>
							</div>
						</div>
						<div class="support-section sidebar-section components-panel">
							<div class="components-panel__body is-opened">
								<h2><?php esc_html_e( 'Support', 'avanam' ); ?></h2>
								<p><?php esc_html_e( 'Have a question, we are happy to help! Get in touch with our support team.', 'avanam' ); ?></p>
								<a href="https://avanam.org/wordpress#support?utm_source=in-app&utm_medium=theme-dash&utm_campaign=help" target="_blank" class="sidebar-link"><?php esc_html_e( 'Submit a Ticket', 'avanam' ); ?></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

}
Base_Dashboard_Settings::get_instance();
