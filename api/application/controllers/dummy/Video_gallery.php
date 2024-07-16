<?php
class Video_gallery extends MY_Controller {
	function __construct() {
		return parent::__construct();
	}

	function index() {
		$usr = $this->sys_user;
		$this->load->model("master/m_customer");
		$usr = $this->m_customer->get_one($usr["customer_id"]);
		
        $this->load->database();
        $r = $this->db->query("SELECT Z_GalleryType g_type, Z_GalleryTitle g_title,
                                    Z_GalleryTmb g_tmb, Z_GalleryUrl g_url,
                                    IF(Z_GalleryType = 'P-VIDEO', Z_GalleryID, Z_GalleryVideoID) g_id,
                                    Z_GalleryDescription g_desc
                                FROM z_gallery
                                WHERE (Z_GalleryType = 'P-VIDEO' OR Z_GalleryType = 'P-YOUTUBE')
                                AND Z_GalleryIsActive = 'Y'")
                    ->result_array();
                    
        foreach ($r as $k => $v) {
            if (preg_match("/^0/", $usr->customer_hp))
                $usr->customer_hp = "62".substr($usr->customer_hp, 1);
            if (preg_match("/^\+/", $usr->customer_hp))
                $usr->customer_hp = substr($usr->customer_hp, 1);
            $r[$k]['g_tmb'] = str_replace("data:image/jpeg;base64,", "", $v['g_tmb']);
            $r[$k]['g_desc'] = preg_replace(["/\%(name)\%/","/\%(wa)\%/","/\%(url)\%/"], 
                            [$usr->customer_name, "https://wa.me/".$usr->customer_hp, $v['g_url']], $v["g_desc"]);
	    }
        echo json_encode($r);
	}
}
