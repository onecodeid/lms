<?php
// fpdf -> mc_table -> fancyrow -> code128 -> htmltable -> rotate
require("fpdf.php");
require("fpdi/fpdi.php");
require("pdf_mc_table.php");
require("pdf_fancyrow.php");
require("pdf_code128.php");
require("pdf_htmltable.php");
require("pdf_rotate.php");
require("pdf_sector.php");
require("pdf_diag.php");


class Pdf extends PDF_Diag {
	var $header_func, $footer_func;
	var $rptclass;
	var $rptTitle;

	function __construct() {
		parent::__construct('P','mm','A4');
        $this->AliasNbPages();
    	$this->SetMargins(10,20,10);

	}
	function header() {
    if(is_callable(array($this->rptclass,$this->header_func),false,$fname)) {
			call_user_func($fname,$this->rptclass, $this->rptTitle);
		}

	}
	function footer() {
		if(is_callable(array($this->rptclass,$this->footer_func),false,$fname)) {
			call_user_func($fname,$this->rptclass);
		}
	}
}


?>
