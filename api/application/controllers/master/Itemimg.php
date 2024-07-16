<?php

class Itemimg extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_itemimg');
    }

    function index()
    {
        return;
    }

    function get_one($id = false)
    {
        if (!$id)
            $id = $this->sys_input['id'];

        $r = $this->m_itemimg->get($id);
        $this->sys_ok($r);
    }

    function show()
    {
        $r = $this->m_itemimg->get($this->sys_input['id']);
        if ($r)
        {
            $photo = explode(',', $r->M_ItemImgUri);
        }
        

        header("Content-type: image/jpg");
        $data = $photo[1];
        echo base64_decode($data);
    }

    function show_tmb()
    {
        $r = $this->m_itemimg->get($this->sys_input['id']);
        if ($r)
        {
            $photo = explode(',', $r->M_ItemImgTmbUri);
        }
        

        header("Content-type: image/jpg");
        $data = $photo[1];
        echo base64_decode($data);
    }
}

?>