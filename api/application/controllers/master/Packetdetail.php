<?php

class Packetdetail extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_packetdetail');
    }

    function search()
    {
        $r = $this->m_packetdetail->search(['item_name'=>'%', 'packet_id'=>$this->sys_input['packet_id']]);
        $this->sys_ok($r);
    }

    function search_price_by_item()
    {
        $this->load->model('master/m_packetprice');
        $r = $this->m_packetprice->search_by_item(['item_id'=>$this->sys_input['item_id'], 'packet_id'=>$this->sys_input['packet_id']]);
        $this->sys_ok($r['records']);
    }

    function search_av_item()
    {
        $r = $this->m_packetdetail->search_av_item(['item_name'=>'%', 'packet_id'=>$this->sys_input['packet_id'], 'page'=>$this->sys_input['page']]);
        $this->sys_ok($r);
    }

    function search_header_price()
    {
        $r = $this->m_packetdetail->search_header_price();
        $this->sys_ok($r);
    }

    function add ()
    {
        $r = $this->m_packetdetail->add($this->sys_input);
        if ($r)
            $this->sys_ok($r);
        else
            $this->sys_error('X');
    }

    function save()
    {
        $id = 0;
        if (isset($this->sys_input['packet_id']))
            $id = $this->sys_input['packet_id'];

        $r = $this->m_packet->save( $this->sys_input, $id );
        echo json_encode($r);
    }

    function del()
    {
        $r = $this->m_packetdetail->del( $this->sys_input );
        $this->sys_ok($r);
    }

    function save_price()
    {
        $this->load->model('master/m_packetprice');
        $r = $this->m_packetprice->save_price( $this->sys_input );
        if ($r->status == "OK")
            $this->sys_ok($r);
        else
            $this->sys_error();
    }

    function save_fee()
    {
        $this->load->model('master/m_packetfee');
        $r = $this->m_packetfee->save_fee( $this->sys_input );
        if ($r->status == "OK")
            $this->sys_ok($r);
        else
            $this->sys_error($r->message);
    }
}

?>