<?php

class Packetsale extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_packetsale');
    }

    function search()
    {
        $r = $this->m_packetsale->search_by_packet(['packet_id'=>$this->sys_input['packet_id']]);
        $this->sys_ok($r);
    }

    function save()
    {
        $r = $this->m_packetsale->save( $this->sys_input );
        if ($r->status == "OK")
            $this->sys_ok(json_decode($r->data));
        else
            $this->sys_error($r->message);
    }
}

?>