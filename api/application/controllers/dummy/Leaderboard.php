<?php
class Leaderboard extends CI_Controller {
	function __construct() {
		return parent::__construct();
	}

	function index() {
        $ids = [6,32,4,181,38];
        
        $this->load->database();
        $x = $this->db->query("SELECT M_CustomerCode cust_code, M_CustomerName cust_name
                        FROM m_customer
                        WHERE FIND_IN_SET(M_CustomerID, '".join(',', $ids)."')")
                    ->result_array();

        foreach ($x as $k => $v)
        {
            $path = getcwd().'/../../one-member/photo/no-pict-tmb.jpg';
            if (file_exists(getcwd().'/../../one-member/photo/'.$v['cust_code'].'-tmb.jpg'))
                $path = getcwd().'/../../one-member/photo/'.$v['cust_code'].'-tmb.jpg';

            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = base64_encode($data);

            $x[$k]['score'] = rand(9000, 1000000);
            $x[$k]['cust_photo'] = $base64;
        }

		ob_clean();
		echo json_encode($x);
		return;
	}
}
