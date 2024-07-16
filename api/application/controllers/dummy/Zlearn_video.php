<?php
class Zlearn_video extends CI_Controller {
	function __construct() {
		return parent::__construct();
	}

	function index() {
        $this->load->database();
        $r = $this->db->query("SELECT Z_GalleryType g_type, Z_GalleryTitle g_title,
                                    Z_GalleryTmb g_tmb, Z_GalleryUrl g_url,
                                    Z_GalleryVideoID g_video_id,
                                    IFNULL(Z_GalleryDuration, '') g_duration
                                FROM z_gallery
                                WHERE (Z_GalleryType = 'Z-YOUTUBE')
                                AND Z_GalleryIsActive = 'Y'")
                    ->result_array();
                    
        foreach ($r as $k => $v) {
            $r[$k]['g_tmb'] = str_replace("data:image/jpeg;base64,", "", $v['g_tmb']);
        }
        echo json_encode($r);
	}
}
