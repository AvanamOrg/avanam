<?php
/**
 * Base\Theme class
 *
 * @package Base
 */

namespace Base;

use InvalidArgumentException;

/**
 * Main class for the theme.
 *
 * This class takes care of initializing theme features and available template tags.
 */
class Theme {
	/**
	 * Instance Control
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Associative array of theme components, keyed by their slug.
	 *
	 * @var array
	 */
	public $components = array();

	/**
	 * The template tags instance, providing access to all available template tags.
	 *
	 * @var Template_Tags
	 */
	protected $template_tags;

	/**
	 * Main Base\Theme Class Instance.
	 *
	 * Insures that only one instance of Base Class exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @static
	 * @static var array $instance
	 *
	 * @return Base\Theme Class
	 */
	public static function instance() {

		// Return if already instantiated.
		if ( self::is_instantiated() ) {
			return self::$instance;
		}

		// Setup the singleton.
		self::setup_instance();

		self::$instance->initialize();

		// Return the instance.
		return self::$instance;

	}

	/**
	 * Setup the singleton instance
	 *
	 * @access private
	 */
	private static function setup_instance() {
		self::$instance = new Theme();
	}

	/**
	 * Return whether the main loading class has been instantiated or not.
	 *
	 * @access private
	 * @return boolean True if instantiated. False if not.
	 */
	private static function is_instantiated() {

		// Return true if instance is correct class.
		if ( ! empty( self::$instance ) && ( self::$instance instanceof Theme ) ) {
			return true;
		}

		// Return false if not instantiated correctly.
		return false;
	}

	/**
	 * Constructor.
	 *
	 * Sets the theme components.
	 *
	 * @param array $components Optional. List of theme components. Only intended for custom initialization, typically
	 *                          the theme components are declared by the theme itself. Each theme component must
	 *                          implement the Component_Interface interface.
	 *
	 * @throws InvalidArgumentException Thrown if one of the $components does not implement Component_Interface.
	 */
	public function __construct( array $components = array() ) {
		spl_autoload_register( array( $this, 'autoload' ) );

		if ( empty( $components ) ) {
			$components = $this->get_default_components();
		}

		// Set the components.
		foreach ( $components as $component ) {

			// Bail if a component is invalid.
			if ( ! $component instanceof Component_Interface ) {
				throw new InvalidArgumentException(
					sprintf(
						/* translators: 1: classname/type of the variable, 2: interface name */
						__( 'The theme component %1$s does not implement the %2$s interface.', 'avanam' ),
						gettype( $component ),
						Component_Interface::class
					)
				);
			}

			$this->components[ $component->get_slug() ] = $component;
		}

		// Instantiate the template tags instance for all theme templating components.
		$this->template_tags = new Template_Tags(
			array_filter(
				$this->components,
				function( Component_Interface $component ) {
					return $component instanceof Templating_Component_Interface;
				}
			)
		);
	}

	/**
	 * Custom autoloader function for theme classes.
	 *
	 * @access private
	 *
	 * @param string $class_name Class name to load.
	 * @return bool True if the class was loaded, false otherwise.
	 */
	private function autoload( $class_name ) {
		$namespace = 'Base';

		if ( strpos( $class_name, $namespace . '\\' ) !== 0 ) {
			return false;
		}
		$parts = explode( '\\', substr( $class_name, strlen( $namespace . '\\' ) ) );

		$path = get_template_directory() . '/inc/components';
		foreach ( $parts as $part ) {
			$path .= '/' . strtolower( $part );
		}
		$path .= '.php';
		if ( ! file_exists( $path ) ) {
			return false;
		}
		require_once $path; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound

		return true;
	}
	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'avanam' ), '1.0' );
	}

	/**
	 * Disable un-serializing of the class.
	 *
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'avanam' ), '1.0' );
	}


	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 *
	 * This method must only be called once in the request lifecycle.
	 */
	public function initialize() {
		array_walk(
			$this->components,
			function( Component_Interface $component ) {
				$component->initialize();
			}
		);
	}

	/**
	 * Retrieves the template tags instance, the entry point exposing template tag methods.
	 *
	 * Calling `webapp()` is a short-hand for calling this method on the main theme instance. The instance then allows
	 * for actual template tag methods to be called. For example, if there is a template tag called `posted_on`, it can
	 * be accessed via `webapp()->posted_on()`.
	 *
	 * @return Template_Tags Template tags instance.
	 */
	public function template_tags() : Template_Tags {
		return $this->template_tags;
	}

	/**
	 * Retrieves the component for a given slug.
	 *
	 * This should typically not be used from outside of the theme classes infrastructure.
	 *
	 * @param string $slug Slug identifying the component.
	 * @return Component_Interface Component for the slug.
	 *
	 * @throws InvalidArgumentException Thrown when no theme component with the given slug exists.
	 */
	public function component( string $slug ) : Component_Interface {
		if ( ! isset( $this->components[ $slug ] ) ) {
			throw new InvalidArgumentException(
				sprintf(
					/* translators: %s: slug */
					__( 'No theme component with the slug %s exists.', 'avanam' ),
					$slug
				)
			);
		}

		return $this->components[ $slug ];
	}

	/**
	 * Gets the default theme components.
	 *
	 * This method is called if no components are passed to the constructor, which is the common scenario.
	 *
	 * @return array List of theme components to use by default.
	 */
	protected function get_default_components() : array {
		$components = array(
			new Options\Component(),
			new Localization\Component(),
			new Base_Support\Component(),
			new Editor\Component(),
			new Accessibility\Component(),
			new Image_Sizes\Component(),
			new AMP\Component(),
			new Microdata\Component(),
			//new PWA\Component(),
			new Comments\Component(),
			new Nav_Menus\Component(),
			new Custom_Header\Component(),
			new Custom_Footer\Component(),
			new Custom_Logo\Component(),
			new Color_Palette\Component(),
			new Styles\Component(),
			new Scripts\Component(),
			new Breadcrumbs\Component(),
			new Template_Parts\Component(),
			new Clean_Frontend\Component(),
			new Icons\Component(),
			new Layout\Component(),
			new Entry_Title\Component(),
			new Archive_Title\Component(),
			new Third_Party\Component(),
			new Updater\Component(),
		);
		if ( class_exists( '\Elementor\Plugin' ) ) {
			$components[] = new Elementor\Component();
		}
		if ( class_exists( 'ElementorPro\Modules\ThemeBuilder\Module' ) ) {
			$components[] = new Elementor_Pro\Component();
		}
		if ( class_exists( 'FLThemeBuilderLayoutData' ) ) {
			$components[] = new BeaverThemer\Component();
		}
		if ( class_exists( 'FLBuilderModel' ) ) {
			$components[] = new Beaver\Component();
		}
		if ( class_exists( 'TUTOR\Tutor' ) ) {
			$components[] = new TutorLMS\Component();
		}
		if ( class_exists( 'woocommerce' ) ) {
			$components[] = new Woocommerce\Component();
		}
		if ( class_exists( 'HT_Knowledge_Base' ) ) {
			$components[] = new Heroic_Kb\Component();
		}
		if ( defined( 'JETPACK__VERSION' ) ) {
			$components[] = new Jetpack\Component();
		}
		if ( class_exists( 'LifterLMS' ) ) {
			$components[] = new LifterLMS\Component();
		}
		if ( defined( 'LEARNDASH_VERSION' ) ) {
			$components[] = new LearnDash\Component();
		}
		if ( class_exists( 'Essential_Real_Estate' ) ) {
			$components[] = new Essential_Real_Estate\Component();
		}
		if ( class_exists( 'Restrict_Content_Pro' ) ) {
			$components[] = new Restrict_Content_Pro\Component();
		}
		if ( class_exists( 'Estatik' ) ) {
			$components[] = new Estatik\Component();
		}
		if ( class_exists( 'BBPress' ) && apply_filters( 'base_theme_enable_bbpress_component', true ) ) {
			$components[] = new BBPress\Component();
		}
		if ( defined( 'BP_PLATFORM_VERSION' ) ) {
			$components[] = new BuddyBoss\Component();
		}
		if ( defined( 'POLYLANG_VERSION' ) ) {
			$components[] = new Polylang\Component();
		}
		if ( defined( 'RANK_MATH_VERSION' ) ) {
			$components[] = new Rankmath\Component();
		}
		if ( defined( 'TRIBE_EVENTS_FILE' ) ) {
			$components[] = new The_Events_Calendar\Component();
		}
		if ( defined( 'GIVE_VERSION' ) ) {
			$components[] = new Give\Component();
		}
		if ( defined( 'WPZOOM_RCB_VERSION' ) ) {
			$components[] = new Zoom_Recipe_Card\Component();
		}
		return $components;
	}
}
