<?php

class Packetimg extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_packetimg');
    }

    function index()
    {
        return;
    }

    function get_one($id = false)
    {
        if (!$id)
            $id = $this->sys_input['id'];

        $r = $this->m_packetimg->get($id);
        $this->sys_ok($r);
    }

    function show()
    {
        $r = $this->m_packetimg->get($this->sys_input['id']);
        if ($r)
        {
            $photo = explode(',', $r->M_PacketImgUri);
        }
        

        header("Content-type: image/jpg");
        $data = $photo[1];
        echo base64_decode($data);
    }

    function show_tmb()
    {
        $r = $this->m_packetimg->get($this->sys_input['id']);
        if ($r)
        {
            if ($r->M_PacketImgTmbUri == null) {
                echo '';
                return;
            }
                

            $photo = explode(',', $r->M_PacketImgTmbUri);
        }
        

        header("Content-type: image/jpg");
        $data = $photo[1];
        echo base64_decode($data);
    }
}

?>