<?php

class Video_view extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {
        $this->load->model('z/z_gallery');
        $d = [];
        $d['data'] = $this->get_video();
        $d["video_id"] = $this->input->get('video_id');
        $this->load->view("video_view", $d);
    }

    function get_video()
    {
        $usr = $this->sys_user;
		$this->load->model("master/m_customer");
        $usr = $this->m_customer->get_one($usr["customer_id"]);

        $r = $this->z_gallery->get_video($this->input->get('video_id'));
        if (preg_match("/^0/", $usr->customer_hp))
            $usr->customer_hp = "62".substr($usr->customer_hp, 1);
        if (preg_match("/^\+/", $usr->customer_hp))
            $usr->customer_hp = substr($usr->customer_hp, 1);
            
        $r->description = preg_replace(["/\%(name)\%/","/\%(wa)\%/","/\%(url)\%/"], 
                        [$usr->customer_name, "wa.me/".$usr->customer_hp, $r->url], $r->description);
        return $r;
    }
}
?>