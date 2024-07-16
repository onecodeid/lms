<?php
class Myorder extends CI_Controller {
	function __construct() {
		return parent::__construct();
	}

	function index() {
		$x = [
			["number"=>"SO-0109070001",
			"date"=>"30 Aug 2020",
			"courier"=>"JNE, OKE",
			"ds"=>"Y",
			"ds_note"=>"Intan Pariwara, Klaten",
			"status"=>"WH.Sent",
			"status_note"=>"Dalam Pengiriman",
			"amount"=>"345000"],
			["number"=>"SO-0109070021",
			"date"=>"23 Aug 2020",
			"courier"=>"JNE, OKE",
			"ds"=>"N",
			"ds_note"=>"Intan Pariwara, Klaten",
			"status"=>"WH.Received",
			"status_note"=>"Diterima",
			"amount"=>"645000"],
			["number"=>"SO-0109070041",
			"date"=>"01 Aug 2020",
			"courier"=>"JNE, OKE",
			"ds"=>"N",
			"ds_note"=>"",
			"status"=>"SO.Cancelled",
			"status_note"=>"Dibatalkan",
			"amount"=>"127000"]
		];
		ob_clean();
		echo json_encode($x);
		return;
	}
}
