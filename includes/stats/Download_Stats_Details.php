<?php
namespace FFI\AAM;

class Download_Stats_Details extends \WP_List_Table {
	public function __construct() {
		parent::__construct(array(
			"singular" => "wp_list_text_link",
			"plural" => "wp_list_test_links",
			"ajax"	=> false
		) );
	}
	
	private function get_columns() {
		return array (
			"Title" => "Title",
			"Downloads" => "Downloads",
			"Streams" => "Streams",
			"Total" => "Total"
		);
	}
	
	private function get_sortable_columns() {
		return array (
			"Title" => "Title",
			"Downloads" => "Downloads",
			"Streams" => "Streams",
			"Total" => "Total"
		);
	}
}
?>