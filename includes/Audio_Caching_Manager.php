<?php
/**
 * Audio Caching Manager class
 *
 * This class will scan the archive directory and update the cache
 * accordingly.
 * 
 * If a new file has been added to the archive directory, then the getID3
 * library is used to parse the MP3 metadata and extract relevant information
 * into cache.
 * 
 * If a file has been deleted from the archive directory, then the file
 * information is also removed from cache.
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @namespace FFI\AAM
 * @package   includes
 * @since     v1.0 Dev
*/

namespace FFI\AAM;

require_once(PATH . "/includes/getID3/getid3.php");

class Audio_Caching_Manager {
/**
 * Hold the list of items which are present in cache but are not on the file
 * system
 *
 * @access private
 * @type   array<string>
*/
	
	private $cache = array();
	
/**
 * Hold the list of items which are on the file system but are not present in 
 * cache
 *
 * @access private
 * @type   array<string>
*/
	
	private $files = array();
	
/**
 * Hold the address to the archive directory
 *
 * @access private
 * @type   string
*/
	
	private $rootDirectory;
	
/**
 * CONSTRUCTOR
 *
 * This constructor bootstraps the functionality of the class. It will generate
 * the address to the archive directory, cache new files from the file system, 
 * and purge old files from the cache which no longer exist on the file system.
 * 
 * @access public
 * @return void
 * @since  v1.0 Dev
*/
	
	public function __construct() {
	//Get the location of the upload directory
		$directory = wp_upload_dir();
		$this->rootDirectory = $directory['basedir'] . "/audio-archive/";
		
		$this->diff();
		$this->cacheNew();
		$this->purgeOld();
	}
	
/**
 * This method will iterate through the $this->files array and cache each
 * of the listed MP3s into the database using the getID3 library. The following
 * items from the metadata are cached into the ffi_aam_audiocache relation:
 *  - title
 *  - artist
 *  - date (custom metadata tag, not native to MP3, uses file last modified time
 *    if not avaliable)
 *  - audio play time
 *  - audio format
 *  - audio MIME time
 *  - file size
 *  - file name
 * 
 * @access private
 * @return void
 * @since  v1.0 Dev
*/
	
	private function cacheNew() {
		global $wpdb;
		
		if (count($this->files)) {
			$getID3 = new \getID3();
			$fileData = array();
			$size = 0;
		
			foreach($this->files as $file) {
			//Fetch the metadata
				$fileData = $getID3->analyze($this->rootDirectory . $file);
				$title = "";
				$artist = "";
				$date = "";
				
			//Generate the MP3 title
				if (isset($fileData['id3v2']['comments']['title'])) {
					$title = implode(", ", $fileData['id3v2']['comments']['title']);
				} else {
					$title = substr($fileData['filename'], 0, strlen($fileData['filename']) - 4); //Remove ".mp3"
				}
				
			//Grab the artist name
				if (isset($fileData['id3v2']['comments']['artist'])) {
					$artist = implode(", ", $fileData['id3v2']['comments']['artist']);
				}
				
			//Generate the recording date
			//This information may be located in the metadata...
				if (isset($fileData['tags']['id3v2']['text']) && is_array($fileData['tags']['id3v2']['text'])) {
					$loopDate = "";
					
				//Since there may be multiple pieces of infomation embedded in this array, iterate over it until we find a date
					for($i = 0; $i < count($fileData['tags']['id3v2']['text']); ++$i) {
						$loopDate = $fileData['tags']['id3v2']['text'][$i];
						
					//We cannot use the exception thrown by the DateTime constructor if the given value is not
					//a date, because of a bug in PHP with constructors throwing exceptions. Use the procedural
					//style strtotime() function to check if we have found a date, then use the DateTime class
					//to handle the rest, and exit the loop.
						if (@strtotime($loopDate) !== false) {
							$date = new \DateTime($loopDate); //Grab from the metadata
							break;
						}
					}
					
				//If we didn't encounter a date, use the last modified date from the file
					if ($date == "") {
						$date = new \DateTime();
						$date->setTimestamp(filectime($this->rootDirectory . $file)); //Read from the last modified date
					}
			//... or we may have to use the the last modified date from the file
				} else {
					$date = new \DateTime();
					$date->setTimestamp(filectime($this->rootDirectory . $file)); //Read from the last modified date
				}
				
				$date = $date->format("Y-m-d");
				
			//Converts something like 4578863 (bytes) to 4.6 (megabytes)
				$size = sprintf("%.1f", intval($fileData['filesize']) / 1000000);
				
			//Cache the results
				$wpdb->insert("ffi_aam_audiocache", array(
					"Visible" => 1,
					"Title" => $title,
					"Artist" => $artist,
					"Date" => $date,
					"Length" => $fileData['playtime_string'],
					"Format" => $fileData['fileformat'],
					"MIME" => $fileData['mime_type'],
					"FileSize" => $size,
					"FileName" => $fileData['filename']
				));
			}
		}
	}
	
/**
 * This method will iterate through the $this->cache array and remove all
 * cached entries which no longer exist on the file system.
 * 
 * @access private
 * @return void
 * @since  v1.0 Dev
*/
	
	private function purgeOld() {
		global $wpdb;
		
		if (count($this->cache)) {
		//Build the query to delete old cached entries
			$SQL = "DELETE FROM `ffi_aam_audiocache` WHERE";
			$values = array();
			
			foreach($this->cache as $cache) {
				$SQL .= " `FileName` = '%s' OR ";
				array_push($values, $cache);
			}
			
			$SQL = substr($SQL, 0, strlen($SQL) - 4);
			
		//Execute the query
			$wpdb->query($wpdb->prepare($SQL, $values));
		}
	}
	
/**
 * This method will find the differences between the files on the file
 * system and cache. Files not in the file system will be stored in the
 * $this->cache array, as a file listed in cache, but not in the file 
 * system. Files not in cache will be stored in the $this->files array, 
 * as the file located in the file system, but not listed in cache.
 * 
 * @access private
 * @return void
 * @since  v1.0 Dev
*/
	
	private function diff() {
	//Get all of the files in the file system and cache
		$this->getAll();
		$this->getCached();
		
	//Find any differences between the file system and cache
		$memCache = $this->cache;
		$this->cache = array_diff($this->cache, $this->files);
		$this->files = array_diff($this->files, $memCache);
	}
	
/**
 * This method will put a sorted listing of all files in the file system
 * in the $this->files array.
 * 
 * @access private
 * @return void
 * @since  v1.0 Dev
*/
	
	private function getAll() {
		$this->files = array();
		$handle = opendir($this->rootDirectory);
		
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != ".." && strtolower($entry) != ".htaccess") {
				array_push($this->files, $entry);
			}
		}
		
		closedir($handle);
		$handle = NULL;
		
		sort($this->files);
	}
	
/**
 * This method will put a sorted listing of all files in cache in the 
 * $this->cache array.
 * 
 * @access private
 * @return void
 * @since  v1.0 Dev
*/
	
	private function getCached() {
		global $wpdb;
		
		$this->cache = $wpdb->get_col($wpdb->prepare("SELECT `FileName` FROM `ffi_aam_audiocache` ORDER BY `FileName` ASC"));
	}
}
?>