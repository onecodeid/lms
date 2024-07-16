<?php

class Point extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('finance/f_point');
    }

    function search()
    {
        $q = [
            'uid' => isset($this->sys_input['uid'])?$this->sys_input['uid']:0,
            'sdate' => date('Y-m-d 00:00:00', strtotime($this->sys_input['sdate'])),
            'edate' => date('Y-m-d 23:59:59', strtotime($this->sys_input['edate'])),
            'page' => $this->sys_input['page']
        ];
        
        $r = $this->f_fee->search($q);

        $this->sys_ok($r);
    }

    function redeem_reward()
    {
        $r = $this->f_point->redeem_reward($this->sys_input['customer_id'], $this->sys_input['reward_id'], (isset($this->sys_input['note'])?$this->sys_input['note']:''));

        echo json_encode($r);
    }
}

?>