<?php

class Youtube_view extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {
        // echo $_GET['token'];
        $this->load->model('z/z_gallery');
        $d['data'] = $this->get_youtube();
        $d["video_id"] = $this->input->get('video_id');
        $this->load->view("youtube_view", $d);
    }

    function get_youtube()
    {
        $usr = $this->sys_user;
		$this->load->model("master/m_customer");
        $usr = $this->m_customer->get_one($usr["customer_id"]);
        
        $r = $this->z_gallery->get_youtube($this->input->get('video_id'));
        
        if (preg_match("/^0/", $usr->customer_hp))
            $usr->customer_hp = "62".substr($usr->customer_hp, 1);
        if (preg_match("/^\+/", $usr->customer_hp))
            $usr->customer_hp = substr($usr->customer_hp, 1);
            
        $r->description = preg_replace(["/\%(name)\%/","/\%(wa)\%/","/\%(url)\%/"], 
                        [$usr->customer_name, "https://wa.me/".$usr->customer_hp, $r->url], $r->description);
        return $r;	    
    }
}
?>