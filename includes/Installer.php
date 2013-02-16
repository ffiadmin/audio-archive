<?php
/**
 * Audio Archive Manager installer class
 *
 * This class is used to install this plugin by:
 *  - creating the necessary database tables
 *  - creating the upload directory
 *  - creating a .htaccess file to protect the directory's contents from
 *    being viewed
 *  - scanning the directory, and caching any new files into the database 
 *    (in the case where this plugin was uninstalled and reinstalled, but 
 *    audio files still remained in the upload directory)
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @package   includes
 * @since     v1.0 Dev
*/

namespace FFI\AAM;

require_once(PATH . "/includes/Audio_Caching_Manager.php");

class Installer {
	private $rootDirectory;
	
	public function __construct() {
	//Get the location of the upload directory
		$directory = wp_upload_dir();
		$this->rootDirectory = $directory['basedir'] . "/audio-archive/";
		
		$this->setupDB();
		$this->createDirectory();
		$this->protectDirectory();
		$this->buildCache();
	}
	
	private function setupDB() {
		global $wpdb;
		
	//Create the three tables for this plugin
		$wpdb->query("CREATE TABLE IF NOT EXISTS `ffi_aam_audiocache` (
					  `ID` INT(11) NOT NULL AUTO_INCREMENT,
					  `Visible` BIT(1) NOT NULL DEFAULT b'1',
					  `Title` VARCHAR(512) NOT NULL,
					  `Author` VARCHAR(128) NOT NULL,
					  `Date` DATE NOT NULL,
					  `Length` VARCHAR(16) NOT NULL,
					  `FileSize` INT(11) NOT NULL,
					  `FileName` VARCHAR(128) NOT NULL,
					  PRIMARY KEY (`ID`),
					  UNIQUE (`FileName`)
					);");
					
		$wpdb->query("CREATE TABLE IF NOT EXISTS `ffi_aam_dailystats` (
					  `Date` date NOT NULL,
					  `Downloads` INT(11) DEFAULT '0',
					  `Streams` INT(11) DEFAULT '0',
					  PRIMARY KEY (`Date`)
					);");
					
		$wpdb->query("CREATE TABLE IF NOT EXISTS `ffi_aam_filestats` (
					  `AudioID` INT(11) NOT NULL,
					  `Downloads` INT(11) DEFAULT '0',
					  `Streams` INT(11) DEFAULT '0',
					  PRIMARY KEY (`AudioID`)
					);");
					
	//Add the foreign key relationship between ffi_aam_filestats and ffi_aam_audiocache
		$wpdb->query("ALTER TABLE `ffi_aam_filestats` ADD CONSTRAINT `FFI_AAM_FILE_STATS_REFERENCES_CACHE` FOREIGN KEY (`AudioID`) REFERENCES `ffi_aam_audiocache` (`id`) ON DELETE CASCADE;");
	}
	
	private function createDirectory() {
		if (!is_dir($this->rootDirectory)) {
			mkdir($this->rootDirectory);
		}
	}
	
	private function protectDirectory() {
		$file = $this->rootDirectory . ".htaccess";
		
		if (!file_exists($file)) {		
			$handle = fopen($file, "w");
			fwrite($handle, "order deny, allow\r\ndeny from all");
			fclose($handle);
			
			$handle = NULL;
		}
	}
	
	private function buildCache() {
		new Audio_Caching_Manager();
	}
}
?>