<?php
class Product_categories extends CI_Controller {
	function __construct() {
		return parent::__construct();
	}

	function index() {
		$x = [
			["code"=>"CA01",
			"name"=>"Pembersih Wajah"],
			["code"=>"CA02",
			"name"=>"Pembersih Badan"],
			["code"=>"CA03",
            "name"=>"Pembersih Kaki"],
            ["code"=>"CA01",
			"name"=>"Pembersih Punggung"]
		];
		ob_clean();
		echo json_encode($x);
		return;
	}
}
