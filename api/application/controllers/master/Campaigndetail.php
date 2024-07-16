<?php

class Campaigndetail extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_campaigndetail');
    }

    function search()
    {
        if (!isset($this->sys_input['campaign_id']))
            die();
        $r = $this->m_campaigndetail->search(['campaign_id'=>$this->sys_input['campaign_id']]);
        $this->sys_ok($r);
    }

    function search_by_campaign_code()
    {
        if (!isset($this->sys_input['campaign_code']))
            die();
        $r = $this->m_campaigndetail->search_by_campaign_code(['campaign_code'=>$this->sys_input['campaign_code']]);
        if ($r)
            $this->sys_ok($r);
        else
            $this->sys_error('-');
    }

    function del()
    {
        $r = $this->m_campaigndetail->del( $this->sys_input );
        $this->sys_ok($r);
    }

    function add ()
    {
        $r = $this->m_campaigndetail->add($this->sys_input);
        if ($r)
            $this->sys_ok($r);
        else
            $this->sys_error('X');
    }
}

?>