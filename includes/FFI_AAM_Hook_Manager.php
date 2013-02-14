<?php
/**
 * Audio Archive Manager hook management class
 *
 * This class is used to initialize the Wordpress hooks for installation
 * and uninstallation. This class utilizes these hooks to setup 
 * installation and uninstallation, respectively:
 *  - register_activation_hook
 *  - register_uninstall_hook
 *
 * This class also contains the action hook callbacks, which will simply
 * call helper classes to handle the details of the installation and 
 * uninstallation for this plugin.
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @package   includes
 * @since     v1.0 Dev
*/

class FFI_AAM_Hook_Manager {
/**
 * Hold the name of the class which will perform the installation.
 *
 * @access private
 * @type   string 
*/
	
	private $installer;
	
/**
 * Hold the name of the class which will perform the uninstallation.
 *
 * @access private
 * @type   string 
*/
	
	private $uninstaller;
	
/**
 * CONSTRUCTOR
 *
 * This method will register the activation and uninstallation hooks
 * and point the callbacks to the appropriate methods within this class.
 * 
 * @access public
 * @param  string   $installerClass   The name of the class which will perform the plugin installation
 * @param  string   $uninstallerClass The name of the class which will perform the plugin uninstallation
 * @return void
 * @since  v1.0 Dev
*/

	public function __construct($installerClass, $uninstallerClass) {
		$this->installer = $installerClass;
		$this->uninstaller = $uninstallerClass;
		
		register_activation_hook(__FILE__, array($this, "activationHandler"));
		register_uninstall_hook(__FILE__, array($this, "uninstallHandler"));
	}
	
/**
 * This method will be called by Wordpress on plugin activation. It's
 * sole responsibility is to include and instatiate the helper class 
 * which will perform the installation.
 * 
 * @access private
 * @return void
 * @since  v1.0 Dev
*/

	private function activationHandler() {
		require_once(FFI_AAM_REAL_ADDR . "includes/" . $this->installer . ".php");
		new $this->installer(); //Minor performance hit, but is only used in a few cases
	}
	
/**
 * This method will be called by Wordpress on plugin uninstallation. It's
 * sole responsibility is to include and instatiate the helper class 
 * which will perform the uninstallation.
 * 
 * @access private
 * @return void
 * @since  v1.0 Dev
*/

	private function uninstallHandler() {
		require_once(FFI_AAM_REAL_ADDR . "includes/" . $this->uninstaller . ".php");
		new $this->uninstaller(); //Minor performance hit, but is only used in a few cases
	}
}
?>