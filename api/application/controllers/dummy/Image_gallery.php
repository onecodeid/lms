<?php
class Image_gallery extends CI_Controller {
	function __construct() {
		return parent::__construct();
	}

	function index() {
        $this->load->database();
        $r = $this->db->query("SELECT Z_GalleryTitle item_code, Z_GalleryTitle item_name,
                                    Z_GalleryTmb img_uri, Z_GalleryID item_id, Z_GalleryDesc item_desc,
                                    Z_GalleryUrl img_url
                                FROM z_gallery
                                WHERE Z_GalleryType = 'P-IMAGE' AND Z_GalleryIsActive = 'Y'")
                    ->result_array();
                    
        foreach ($r as $k => $v) {
            $r[$k]['img_uri'] = str_replace("data:image/jpeg;base64,", "", $v['img_uri']);
        }
        echo json_encode($r);
    }
    
    // function detail($id)
    // {
    //     $this->load->database();
    //     $r = $this->db->query("SELECT Z_GalleryTitle item_code, Z_GalleryTitle item_name,
    //                                 Z_GalleryTmb img_uri, Z_GalleryDesc item_desc,
    //                                 Z_GalleryUrl img_url
    //                             FROM z_gallery
    //                             WHERE Z_GalleryID = {$id}")
    //                 ->row();
    //     $r = (array) $r;           
        
    //         $r['img_uri'] = str_replace("data:image/jpeg;base64,", "", $v['img_uri']);
        
    //     echo json_encode($r);
    // }
}
