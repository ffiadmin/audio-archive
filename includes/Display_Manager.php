<?php
/**
 * Audio Archive Display Manager 
 *
 * This class is used to fetch detaand constrcut .
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @namespace FFI\AAM
 * @package   includes
 * @since     v1.0 Dev
*/

namespace FFI\AAM;

class Display_Manager {
	private $archive;
	private $archiveItemIndex = -1;
	private $colors = array("blue", "red", "black", "green", "orange", "lime", "purple", "aqua", "white", "pink", "light-blue", "yellow");
	private $currentMonthIndex = -1;
	private $currentMonthName = "";
	private $currentYearIndex = -1;
	private $currentYearValue = "";
	private $dateFormatter;
	public $first = 0;
	private $monthList;
	private $monthTotal = 0;
	public $total = 0;
	public $year = 0;
	public $years = array();
	private $yearTotal = 0;
	
	public function __construct($year = 0) {
		$this->dateFormatter = new \DateTime();
		$this->year = $year == 0 ? $this->dateFormatter->format("Y") : $year;
		
		$this->stats();
		$this->fetchArchive();
		$this->avaliableYears();
	}
	
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
		
		$this->archive = $wpdb->get_results($wpdb->prepare("SELECT `ffi_aam_audiocache`.*, DATE_FORMAT(`Date`, '%%M') AS `Month`, DATE_FORMAT(`Date`, '%%W, %%M %%D, %%Y') AS `FormattedDate` FROM `ffi_aam_audiocache` WHERE `Date` >= %s AND `Date` <= %s ORDER BY YEAR(`Date`) DESC, MONTH(`Date`) DESC, `Date` ASC", $this->year . "-01-01", $this->year . "-12-31"));
		$this->monthList = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT DATE_FORMAT(`Date`, '%%M') AS `Month` FROM `ffi_aam_audiocache` WHERE `Date` >= %s AND `Date` <= %s ORDER BY MONTH(`Date`) DESC", $this->year . "-01-01", $this->year . "-12-31"));
		$this->monthTotal = count($this->monthList);
	}
	
	private function avaliableYears() {
		global $wpdb;
		$this->years = $wpdb->get_results("SELECT DATE_FORMAT(`Date`, '%Y') AS `Year`, COUNT(Year(`Date`)) AS `Total` FROM `ffi_aam_audiocache` GROUP BY `Year` ORDER BY `Year` DESC");
		$this->yearTotal = count($this->years);
	}
	
	public function monthAvaliable() {
		if (++$this->currentMonthIndex < $this->monthTotal) {
			$this->currentMonthName = $this->monthList[$this->currentMonthIndex]->Month;
			return true;
		} else {
			return false;
		}
	}
	
	public function getMonth() {
		return $this->currentMonthName . " " . $this->year;
	}
	
	public function getColor() {
		return $this->colors[$this->currentMonthIndex];
	}
	
	public function fileAvaliable() {
		++$this->archiveItemIndex;
		
		if (isset($this->archive[$this->archiveItemIndex])) {
			if ($this->currentMonthName == $this->archive[$this->archiveItemIndex]->Month) {
				return true;
			}
			
			--$this->archiveItemIndex;
			return false;
		}
		
		return false;
	}
	
	public function renderItem() {
		$data = $this->archive[$this->archiveItemIndex];
		
		return "
<li>
<a href=\"" . FAKE_ADDR . "stream/" . $data->ID . "\">
<h3>" . $data->Title . "</h3>
<p class=\"preacher\">" . $data->Artist . "</p>
<time datetime=\"" . $data->Date . "\">" . $data->FormattedDate . "</time>
<p class=\"length\">" . $data->Length . "</p>
<p class=\"size\">" . $data->FileSize . " MB</p>
<a class=\"link stream\" href=\"" . FAKE_ADDR . "stream/" . $data->ID . "\"><span>Stream</span></a>
<a class=\"link download\" href=\"" . FAKE_ADDR . "download/" . $data->ID . "\"><span>Download</span></a>
</a>
</li>
";
	}
	
	public function yearAvaliable() {
		return ++$this->currentYearIndex < $this->yearTotal;
	}
	
	public function renderYear() {
		$data = $this->years[$this->currentYearIndex];
		
		return "
<li" . ($this->year == $data->Year ? " class=\"active\"" : "") . ">
<time datetime=\"" . $data->Year . "\">" . $data->Year . "</time>
<p>" . $data->Total . ($data->Total == 1 ? " sermon" : " sermons") . " avaliable</p>
</li>
";
	}
}
?>