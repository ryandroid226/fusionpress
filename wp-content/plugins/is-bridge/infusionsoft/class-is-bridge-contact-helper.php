<?php

/**
 * The settings of the plugin.
 *
 * @link       http://holtech.us
 * @since      1.0.0
 *
 * @package    Is_Bridge
 * @subpackage Is_Bridge/infusionsoft
 */

/**
 * Class Is_Bridge_Contact_Helper
 *
 */
class Is_Bridge_Contact_Helper {

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
	 * @var      Object    $infusionsoft    An instance of the Infusionsoft Object
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
	}

	public function getInfContacts($conId = 0, $selectFields = array("FirstName", "LastName", "Email"), $returnFormat = "array"){
		if($conId < 1){
			return;
		}
		$conId = $conId;

		$contacts = $this->infusionsoft->contacts('xml')->load($conId, $selectFields);

		switch($returnFormat){
			case "array":
				return $contacts;
				break;
			case "json":
				return json_encode($contacts);
				break;
			default:
				return $contacts;
				break;
		}
	}

}