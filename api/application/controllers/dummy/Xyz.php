<?php

class Xyz extends CI_Controller {
	function __construct() {
		return parent::__construct();
	}

	function index() {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"https://maxbuzz.co/giveaway");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            "giveaway_id=5f94f34c04d363574105ad22&slug=maulidnabi&name=SembarangX&email=sembarangx@gmail.com&phone=089634590946&subdistrict_id=5512&province_id=10&province=Jawa Tengah&city_id=399&city=Semarang&type=Kota&subdistrict_name=Tembalang&jenis_kelamin=Laki-Laki");

// In real life you should use something like:
// curl_setopt($ch, CURLOPT_POSTFIELDS, 
//          http_build_query(array('postvar1' => 'value1')));

// Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close ($ch);

        var_dump($server_output);
    
    }

    function xyz()
    {
        var_dump($_POST);
    }
}
?>
