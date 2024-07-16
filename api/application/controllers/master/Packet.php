<?php

class Packet extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_packet');
    }

    function search()
    {
        $inp = $this->sys_input;
        $search = isset($inp['packet_name'])?'%'.$inp['packet_name'].'%':'%';
        $page = isset($inp['page'])?$inp['page']:1;
        $limit = isset($inp['limit'])?$inp['limit']:10;

        $r = $this->m_packet->search(['packet_name'=>$search, 'page'=>$page, 'limit'=>$limit]);
        $this->sys_ok($r);
    }

    function search_w_price()
    {
        $cust_level = isset($this->sys_input['customer_level']) ? $this->sys_input['customer_level'] : 1;
        $search = isset($this->sys_input['search'])?'%'.$this->sys_input['search'].'%':'%';
        $r = $this->m_packet->search_w_price(['item_name'=>$search, 'customer_level'=>$cust_level]);
        $this->sys_ok($r);
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
        $r = $this->m_packet->del( $this->sys_input );
        $this->sys_ok($r);
    }

    function publish()
    {
        $r = $this->m_packet->publish( $this->sys_input );
        $this->sys_ok($r['data']);
    }
}

?>