<?php

class Campaign extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_campaign');
    }

    function search_by_campaign_code()
    {
        if (!isset($this->sys_input['campaign_code']))
            die();
        $r = $this->m_campaign->search_by_code(['campaign_code'=>$this->sys_input['campaign_code']]);
        if ($r)
            $this->sys_ok($r);
        else
            $this->sys_error('-');
    }

    function search()
    {
        $r = $this->m_campaign->search(['campaign_name'=>$this->sys_input['search'].'%']);
        $this->sys_ok($r);
    }

    // function search_w_price()
    // {
    //     $r = $this->m_packet->search_w_price(['item_name'=>'%', 'customer_level'=>1]);
    //     $this->sys_ok($r);
    // }

    function save()
    {
        $id = 0;
        if (isset($this->sys_input['campaign_id']))
            $id = $this->sys_input['campaign_id'];

        $r = $this->m_campaign->save( $this->sys_input, $id );
        echo json_encode($r);
    }

    // function del()
    // {
    //     $r = $this->m_packet->del( $this->sys_input );
    //     $this->sys_ok($r);
    // }
}

?>