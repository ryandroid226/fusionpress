<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://holtech.us
 * @since      1.0.0
 *
 * @package    Is_Bridge
 * @subpackage Is_Bridge/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Is_Bridge
 * @subpackage Is_Bridge/public
 * @author     Ryan Holt <rholt@getuwired.com>
 */
class Is_Bridge_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Object    $infusionsoft    Instance of the Infusionsoft class.
	 */
	private $infusionsoft;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $infusionsoft ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->infusionsoft = $infusionsoft;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Is_Bridge_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Is_Bridge_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/is-bridge-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Is_Bridge_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Is_Bridge_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/is-bridge-public.js', array( 'jquery' ), $this->version, false );

	}

	public function isAuthAlert(){
		$isAuthed = false;
		$isAuthed = apply_filters('is_is_authed', $isAuthed);
		if(is_admin() || current_user_can('administrator')){
			if(!$isAuthed || $this->infusionsoft == null){
				?>
					<div class="is_auth_alert_wrap"><span>Infusionsoft is not Authorized! <a href="<?php echo admin_url('plugins.php?page=is_bridge_options&'); ?>">Click Here</a> to authorize.</span></div>
				<?php
			}
		}
	}

}
