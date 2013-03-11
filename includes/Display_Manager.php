<?php
namespace FFI\AAM;

class Display_Manager {
	private $archive;
	private $dateFormatter;
	private $monthTotal = 0;
	public $total = 0;
	public $first = 0;
	public $year = 0;
	public $years = array();
	
	public function __construct($year = 0) {
		$this->dateFormatter = new \DateTime();
		$this->year = $year == 0 ? $this->dateFormatter->format("Y") : $year;
		
		$this->stats();
		$this->fetchArchive();
		$this->avaliableYears();
	}
	
	//SELECT DATE_FORMAT(`Date`, '%Y') AS `Year`, COUNT(Year(`Date`)) AS Total FROM `ffi_aam_audiocache` GROUP BY `Year` ORDER BY `Year` DESC;
	public function background() {
	//The main page (the current year) will display a different set of images than previous years
		if ($this->year == $this->dateFormatter->format("Y")) {
			$background = array("sitting.jpg", "acoustic-guitar.jpg", "electric-guitar.jpg");
		} else {
			$background = array("earbuds.jpg", "sound-board.jpg", "slider-sound-board.jpg");
		}
		
		$rand = mt_rand(0, 2);		
		return $background[$rand];
	}
	
	private function stats() {
		global $wpdb;
		$total = $wpdb->get_results("SELECT COUNT(*) AS `Total`, MIN(`Date`) AS `First` FROM `ffi_aam_audiocache`");
		
		if (count($total)) {
			$date = new \DateTime($total[0]->First);
			$this->total = $total[0]->Total;
			$this->first = $date->format("F jS, Y");
		}
	}
	
	private function fetchArchive() {
		global $wpdb;
		
		$this->archive = $wpdb->query($wpdb->prepare("SELECT `ffi_aam_audiocache`.*, DATE_FORMAT(`Date`, '%M') AS `Month`, YEAR(`Date`) AS `Year` FROM `ffi_aam_audiocache` WHERE `Date` >= '%s' AND `Date` <= '%s' ORDER BY YEAR(`Date`) DESC, MONTH(`Date`) DESC, `Date` ASC", $this->year . "-01-01", $this->year . "-12-31"));
		$this->monthTotal = count($this->archive);
	}
	
	private function avaliableYears() {
		global $wpdb;
		$this->years = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT DATE_FORMAT(`Date`, '%Y') AS `Year` FROM `ffi_aam_audiocache`"));
	}
	
	public function monthAvaliable() {
		return --$this->monthTotal >= 1;
	}
}
?>