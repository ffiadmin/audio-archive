<?php
namespace FFI\AAM;

class Audio_Caching_Manager {
	private $cache;
	private $files;
	private $rootDirectory;
	
	public function __construct() {
	//Get the location of the upload directory
		$directory = wp_upload_dir();
		$this->rootDirectory = $directory['basedir'] . "/audio-archive/";
		
		$this->diff();
	}
	
	public function diff() {
		$this->getAll();
		$this->getCached();
		$memCache = $this->cache;
		
		$this->cache = array_diff($this->cache, $this->files);
		$this->files = array_diff($this->files, $memCache);
		
		print_r($this->cache);
		print_r($this->files);
	}
	
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
	
	private function getCached() {
		global $wpdb;
		
		$this->cache = $wpdb->get_col($wpdb->prepare("SELECT File FROM ffi_aam_audiocache ORDER BY File ASC"));
	}
}
?>