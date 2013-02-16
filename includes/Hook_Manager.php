<?php
/**
 * Audio Archive Manager hook management class
 *
 * This class contains the action hook callbacks for setup and uninstall, which
 * will simply call helper classes to handle the details of the installation and 
 * uninstallation for this plugin.
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @package   includes
 * @since     v1.0 Dev
*/

namespace FFI\AAM;

class Hook_Manager {
/**
 * CONSTRUCTOR
 *
 * This class has an empty constructor.
 * 
 * @access public
 * @return void
 * @since  v1.0 Dev
*/

	public function __construct() {
		//Nothing to do!
	}
	
/**
 * This method will be called by Wordpress on plugin activation. It's
 * sole responsibility is to include and instatiate the helper class 
 * which will perform the installation.
 * 
 * @access public
 * @return void
 * @since  v1.0 Dev
*/

	public function activationHandler() {
		require_once(PATH . "includes/Installer.php");
		new Installer();
	}
	
/**
 * This method will be called by Wordpress on plugin uninstallation. It's
 * sole responsibility is to include and instatiate the helper class 
 * which will perform the uninstallation.
 * 
 * @access public
 * @return void
 * @since  v1.0 Dev
*/

	public function uninstallHandler() {
		require_once(PATH . "includes/Uninstaller.php");
		new Uninstaller();
	}
}
?>