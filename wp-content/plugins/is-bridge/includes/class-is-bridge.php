<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://holtech.us
 * @since      1.0.0
 *
 * @package    Is_Bridge
 * @subpackage Is_Bridge/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Is_Bridge
 * @subpackage Is_Bridge/includes
 * @author     Ryan Holt <rholt@getuwired.com>
 */
class Is_Bridge {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Is_Bridge_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Object    $infusionsoft    An instance of the Infusionsoft API Object
	 */
	protected $infusionsoft;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'IS_BRIDGE_VERSION' ) ) {
			$this->version = IS_BRIDGE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'is-bridge';
		$this->infusionsoft = null;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_infusionsoft_hooks();
		$this->define_contact_helper_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Is_Bridge_Loader. Orchestrates the hooks of the plugin.
	 * - Is_Bridge_i18n. Defines internationalization functionality.
	 * - Is_Bridge_Admin. Defines all hooks for the admin area.
	 * - Is_Bridge_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-is-bridge-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-is-bridge-i18n.php';

		/**
		 * The class responsible for defining Infusionsoft functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'infusionsoft/class-is-bridge-infusionsoft-helper.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'infusionsoft/class-is-bridge-contact-helper.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-is-bridge-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-is-bridge-public.php';

		$this->loader = new Is_Bridge_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Is_Bridge_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Is_Bridge_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_infusionsoft_hooks() {

		$plugin_infusionsoft = new Is_Bridge_Infusionsoft_Helper( $this->get_plugin_name(), $this->get_version() );
		$this->infusionsoft = $plugin_infusionsoft->getInfusionsoft();

		// var_dump($this->infusionsoft);

		$this->loader->add_action('process_request_code', $plugin_infusionsoft, 'processRequestCode', 10, 1);

		$this->loader->add_filter('is_details_check', $plugin_infusionsoft, 'isDetailsCheck', 10, 1);
		$this->loader->add_filter('is_is_authed', $plugin_infusionsoft, 'isIsAuthed', 10, 1);
		$this->loader->add_filter('is_auth_link', $plugin_infusionsoft, 'authLink', 10, 3);

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_contact_helper_hooks() {
		if(!is_null($this->infusionsoft)){
			// var_dump($this->infusionsoft);
			$plugin_contact_helper = new Is_Bridge_Contact_Helper( $this->get_plugin_name(), $this->get_version(), $this->infusionsoft );

			$this->loader->add_filter('get_inf_contacts', $plugin_contact_helper, 'getInfContacts', 10, 3);
		}
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Is_Bridge_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_settings = new Is_Bridge_Admin_Settings( $this->get_plugin_name(), $this->get_version(), $this->infusionsoft );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_menu', $plugin_settings, 'setup_plugin_options_menu' );
		$this->loader->add_action( 'admin_init', $plugin_settings, 'initialize_infusionsoft_bridge_options' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Is_Bridge_Public( $this->get_plugin_name(), $this->get_version(), $this->infusionsoft );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_body_open', $plugin_public, 'isAuthAlert');

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Is_Bridge_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
