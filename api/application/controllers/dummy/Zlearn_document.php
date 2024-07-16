<?php
class Zlearn_document extends CI_Controller {
	function __construct() {
		return parent::__construct();
	}

	function index() {
        $this->load->database();
        $r = $this->db->query("SELECT Z_GalleryTitle g_title,
                                    Z_GalleryUrl g_url, Z_GallerySize g_size,
                                    Z_GalleryID g_document_id,
                                    Z_GalleryType g_type
                                FROM z_gallery
                                WHERE (Z_GalleryType LIKE 'Z-DOC-%')
                                AND Z_GalleryIsActive = 'Y'")
                    ->result_array();
                    
        // foreach ($r as $k => $v) {
        //     $r[$k]['g_tmb'] = str_replace("data:image/jpeg;base64,", "", $v['g_tmb']);
        // }
        echo json_encode($r);
	}
}
