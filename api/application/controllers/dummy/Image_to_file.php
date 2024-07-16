<?php
class Image_to_file extends CI_Controller {
	function __construct() {
		return parent::__construct();
	}

	function index() {
        $this->load->database();
        $r = $this->db->query("SELECT M_ItemCode item_code, M_ItemImgUri img_uri
                                FROM m_item
                                JOIN m_itemimg on m_itemid = m_itemimgm_itemid
                                WHERE M_ItemIsActive = 'Y'")
                    ->result_array();
                    
        foreach ($r as $k => $v) {
            $this->base64_to_jpeg($v['img_uri'], '/home/one/Web/one-member/images/'.$v['item_code'].'.jpg');
        }
        // echo json_encode($r);
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

    function base64_to_jpeg($base64_string, $output_file) {
        // open the output file for writing
        $ifp = fopen( $output_file, 'wb' ); 
    
        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode( ',', $base64_string );
    
        // we could add validation here with ensuring count( $data ) > 1
        fwrite( $ifp, base64_decode( $data[ 1 ] ) );
    
        // clean up the file resource
        fclose( $ifp ); 
    
        return $output_file; 
    }
}
