<?php

/**
 * The settings of the plugin.
 *
 * @link       http://holtech.us
 * @since      1.0.0
 *
 * @package    Is_Bridge
 * @subpackage Is_Bridge/admin
 */

/**
 * Class WordPress_Plugin_Template_Settings
 *
 */
class Is_Bridge_Admin_Settings {

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
	 * @var      Object    $infusionsoft    An instance of the Infusionsoft API Object
	 */
	private $infusionsoft;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $infusionsoft ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->infusionsoft = $infusionsoft;

		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
	}

	/**
	 * This function introduces the theme options into the 'Appearance' menu and into a top-level
	 * 'WPPB Demo' menu.
	 */
	public function setup_plugin_options_menu() {

		//Add the menu to the Plugins set of menu items
		add_plugins_page(
			'IS Bridge Options', 					// The title to be displayed in the browser window for this page.
			'IS Bridge Options',					// The text to be displayed for this menu item
			'manage_options',					// Which type of users can see this menu item
			'is_bridge_options',			// The unique ID - that is, the slug - for this menu item
			array( $this, 'render_settings_page_content')				// The name of the function to call when rendering this menu's page
		);

	}

	/**
	 * Provides default values for the Input Options.
	 *
	 * @return array
	 */
	public function default_infusionsoft_bridge_options() {

		$defaults = array(
			'infusionsoft_auth_token' => '',
			'client_key'		=>	'Client Key',
			'client_secret'		=>	'Client Secret',
			'infusionsoft_app_id'		=>	'Infusionsoft App ID'
		);

		return $defaults;

	}

	/**
	 * Renders a simple page to display for the theme menu defined above.
	 */
	public function render_settings_page_content( $active_tab = '' ) {
		?>
		<!-- Create a header in the default WordPress 'wrap' container -->
		<div class="wrap">

			<?php

				$authLink = null;
				if($this->infusionsoft == null){
					$this->infusionsoft = apply_filters('get_infusionsoft_object', $this->infusionsoft);
				} else {
					// If we are returning from Infusionsoft we need to exchange the code for an
					// access token.
					if (isset($_GET['code'])) {
						$code = $_GET['code'];
						do_action('process_request_code', $code);
					}

				}
			?>

			<h2><?php _e( 'IS Bridge Options', 'is-bridge' ); ?></h2>
			<?php settings_errors(); ?>

			<?php if( isset( $_GET[ 'tab' ] ) ) {
				$active_tab = $_GET[ 'tab' ];
			} else {
				$active_tab = 'infusionsoft_bridge_options';
			} // end if/else ?>

			<style type="text/css">
				p.submit {
					display: inline-block;
					margin-right: 40px;
				}
				a.button.button-primary {
					vertical-align: middle;
				}
			</style>

			<?php apply_filters('is_details_check', 'h4'); ?>

			<h2 class="nav-tab-wrapper">
				<a href="?page=is_bridge_options&tab=infusionsoft_bridge_options" class="nav-tab <?php echo $active_tab == 'infusionsoft_bridge_options' ? 'nav-tab-active' : ''; ?>"><?php _e( 'IS Bridge Options', 'is-bridge' ); ?></a>
			</h2>

			<form method="post" action="options.php">
				<?php

				if( $active_tab == 'infusionsoft_bridge_options' ) {

					settings_fields( 'is_bridge_options' );
					do_settings_sections( 'is_bridge_options' );

				} else {

					settings_fields( 'is_bridge_options' );
					do_settings_sections( 'is_bridge_options' );

				} // end if/else

				submit_button();

				// do_action('is_auth_link_or_notice');
				echo apply_filters('is_auth_link', "Authorize", "a", array("button", "button-primary"));

				?>
			</form>

		</div><!-- /.wrap -->
	<?php
	}

	/**
	 * This function provides a simple description for the Input Examples page.
	 *
	 * It's called from the 'wppb-demo_theme_initialize_infusionsoft_bridge_options_options' function by being passed as a parameter
	 * in the add_settings_section function.
	 */
	public function is_bridge_options_callback() {
		$options = get_option('is_bridge_options');
		// var_dump($options);
		echo '<p>' . __( 'Settings for the Infusionsoft API Bridge.', 'is-bridge' ) . '</p>';
	} // end general_options_callback


	/**
	 * Initializes the theme's input example by registering the Sections,
	 * Fields, and Settings. This particular group of options is used to demonstration
	 * validation and sanitization.
	 *
	 * This function is registered with the 'admin_init' hook.
	 */
	public function initialize_infusionsoft_bridge_options() {
		//delete_option('is_bridge_options');
		if( false == get_option( 'is_bridge_options' ) ) {
			$default_array = $this->default_infusionsoft_bridge_options();
			update_option( 'is_bridge_options', $default_array );
		} // end if

		add_settings_section(
			'is_bridge_options_section',
			__( 'Infusionsoft Bridge Options', 'is-bridge' ),
			array( $this, 'is_bridge_options_callback'),
			'is_bridge_options'
		);

		add_settings_field(
			'Client Key',
			__( 'Client Key', 'is-bridge' ),
			array( $this, 'client_key_callback'),
			'is_bridge_options',
			'is_bridge_options_section'
		);

		add_settings_field(
			'Client Secret',
			__( 'Client Secret', 'is-bridge' ),
			array( $this, 'client_secret_callback'),
			'is_bridge_options',
			'is_bridge_options_section'
		);

		add_settings_field(
			'Infusionsoft App ID',
			__( 'Infusionsoft App ID', 'is-bridge' ),
			array( $this, 'infusionsoft_app_id_callback'),
			'is_bridge_options',
			'is_bridge_options_section'
		);

		register_setting(
			'is_bridge_options',
			'is_bridge_options',
			array( $this, 'validate_infusionsoft_bridge_options')
		);

	}

	public function client_key_callback() {

		$options = get_option( 'is_bridge_options' );

		// Render the output
		echo '<input type="text" id="client_key" name="is_bridge_options[client_key]" value="' . $options['client_key'] . '" />';

	} // end input_element_callback

	public function client_secret_callback() {

		$options = get_option( 'is_bridge_options' );

		// Render the output
		echo '<input type="text" id="client_secret" name="is_bridge_options[client_secret]" value="' . $options['client_secret'] . '" />';

	} // end input_element_callback

	public function infusionsoft_app_id_callback() {

		$options = get_option( 'is_bridge_options' );

		// Render the output
		echo '<input type="text" id="infusionsoft_app_id" name="is_bridge_options[infusionsoft_app_id]" value="' . $options['infusionsoft_app_id'] . '" />';

	} // end input_element_callback

	public function validate_infusionsoft_bridge_options( $input ) {

		// Create our array for storing the validated options
		$output = array();

		// Loop through each of the incoming options
		foreach( $input as $key => $value ) {

			// Check to see if the current option has a value. If so, process it.
			if( isset( $input[$key] ) ) {

				// Strip all HTML and PHP tags and properly handle quoted strings
				$output[$key] = strip_tags( stripslashes( $input[ $key ] ) );

			} // end if

		} // end foreach

		// Return the array processing any additional functions filtered by this action
		return apply_filters( 'validate_infusionsoft_bridge_options', $output, $input );

	} // end validate_infusionsoft_bridge_options

}