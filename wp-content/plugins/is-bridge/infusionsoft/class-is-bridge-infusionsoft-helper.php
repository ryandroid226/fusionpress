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
 * Class WordPress_Plugin_Template_Settings
 *
 */
class Is_Bridge_Infusionsoft_Helper {

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
	 * The Client ID set in the options of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $clientId
	 */
	private $clientId;

	/**
	 * The Client Secret set in the options of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $clientSecret
	 */
	private $clientSecret;

	/**
	 * The App ID set in the options of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $appId
	 */
	private $appId;

	/**
	 * The Infusionsoft Auth Token.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $authToken
	 */
    private $authToken;

    /**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Object    $infusionsoft    An instance of the Infusionsoft Object
	 */
	private $infusionsoft;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      boolean    $isIsAuthed    Flag to indicate whether or not we are authorized with Infusiosoft
	 */
	private $isIsAuthed;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->infusionsoft = null;

		$this->load_dependencies();
		$this->setClassOptions();

		if($this->isInfusionReady())
		{
			$this->infusionsoft = new \Infusionsoft\Infusionsoft(array(
				'clientId'     => $this->clientId,
				'clientSecret' => $this->clientSecret,
				'redirectUri'  => admin_url('plugins.php?page=is_bridge_options&'),
				'debug' => true
			));
		}

		$this->isIsAuthed = false;
		$this->isIsAuthed = $this->checkIsAuthStatus();
	}

	 /**
	 * Load the required dependencies for the Admin facing functionality.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wppb_Demo_Plugin_Admin_Settings. Registers the admin settings and page.
	 *
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
        require_once plugin_dir_path( dirname( __FILE__ ) ) .  'infusionsoft/vendor/autoload.php';

	}

	private function setClassOptions(){
		$is_bridge_options = get_option('is_bridge_options');
        $this->clientId = $is_bridge_options['client_key'];
		$this->clientSecret = $is_bridge_options['client_secret'];
		$this->appId = $is_bridge_options['infusionsoft_app_id'];
	}

	private function isInfusionReady(){
		if($this->clientId == "Client Key"
			|| $this->clientId == ""
			|| $this->clientSecret == "Client Secret"
			|| $this->clientSecret == ""
			|| $this->appId == "Infusionsoft App ID"
			|| $this->appId == "")
		{ return false; }
		return true;
	}

	private function checkIsAuthStatus(){
		$is_bridge_options = get_option('is_bridge_options');
		if(isset($is_bridge_options['infusionsoft_auth_token']) && $is_bridge_options['infusionsoft_auth_token'] != ""){
			$authToken = $is_bridge_options['infusionsoft_auth_token'];
			$authToken = base64_decode($authToken);
			$this->authToken = unserialize($authToken);
			$this->infusionsoft->setToken($this->authToken);
			//echo '<pre>'.var_dump($this->infusionsoft->isTokenExpired()).'</pre>';
			if(!$this->infusionsoft->isTokenExpired()){
				return true;
			} else {
				$this->refreshExpiredToken();
				return false;
			}
		}
		return false;
	}

	public function isDetailsCheck($elemType = ""){
		if($this->clientId == "Client Key"
			|| $this->clientId == ""
			|| $this->clientSecret == "Client Secret"
			|| $this->clientSecret == "")
		{
			return sprintf('<%s id="is_details_alert">%s</%s>', $elemType, "Enter your Infusionsoft App details below to proceed.", $elemType);
		}
	}

	public function authLink($text = "", $type = "", $elementClasses = ""){
		if($this->clientId != "Client Key" && $this->clientId != "" && $this->clientSecret != "Client Secret" && $this->clientSecret != "")
        {
			$authLink = $this->infusionsoft->getAuthorizationUrl();
			$buttonText = ($text != "") ? $text : "Click here to authorized";
			$linkMarkup = sprintf('<%s class="%s" href="%s">%s</%s>', $type, implode(" ", $elementClasses), $authLink, $buttonText, $type);
			return $linkMarkup;
		}
	}

	public function processRequestCode($code) {
		$token = null;
		$newCode = false;

		// Attempting to use Session variables to prevent a bad $code being sent to requestAccessToken
		if(isset($_SESSION['is_plugin_code'])){
			$data = json_decode($_SESSION['is_plugin_code'], true);
			if(!in_array($code, $data)){
				try{
					$token = $this->infusionsoft->requestAccessToken($code);
					$newCode = true;
				} catch(ClientException $e) { $token = null; }
			}
		} else {
			$data = array();
			try{
				$token = $this->infusionsoft->requestAccessToken($code);
				$newCode = true;
			} catch(ClientException $e) { $token = null; }
		}

		if(!is_null($token)){
			$this->updateAuthTokenOption($token);
		}

		if($newCode) { array_push($data, $code); }
		$_SESSION['is_plugin_code'] = json_encode($data);
	}

	private function refreshExpiredToken(){
		$token = $this->infusionsoft->refreshAccessToken();
		echo '<pre>'.var_dump($token).'</pre>';
		$this->updateAuthTokenOption($token);
		$this->checkIsAuthStatus();
	}

	private function updateAuthTokenOption($token){
		$is_bridge_options = get_option('is_bridge_options');
		$is_bridge_options['infusionsoft_auth_token'] = base64_encode(serialize($token));
		update_option( 'is_bridge_options', $is_bridge_options, true );
	}

	public function isIsAuthed($authed = false){
		$authed = $this->isIsAuthed;
		return $authed;
	}

	public function getInfusionsoft(){
		// if($this->isIsAuthed){
			return $this->infusionsoft;
		// } else {
		// 	return null;
		// }
	}

}