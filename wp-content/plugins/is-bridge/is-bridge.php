<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://holtech.us
 * @since             1.0.0
 * @package           Is_Bridge
 *
 * @wordpress-plugin
 * Plugin Name:       IS Bridge
 * Plugin URI:        http://holtech.us
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Ryan Holt
 * Author URI:        http://holtech.us
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       is-bridge
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'IS_BRIDGE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-is-bridge-activator.php
 */
function activate_is_bridge() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-is-bridge-activator.php';
	Is_Bridge_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-is-bridge-deactivator.php
 */
function deactivate_is_bridge() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-is-bridge-deactivator.php';
	Is_Bridge_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_is_bridge' );
register_deactivation_hook( __FILE__, 'deactivate_is_bridge' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-is-bridge.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_is_bridge() {

	$plugin = new Is_Bridge();
	$plugin->run();

}
run_is_bridge();
