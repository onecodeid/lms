<?php

class Redeem extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('finance/f_redeem');
    }

    function search()
    {
        $q = [
            'uid' => isset($this->sys_input['uid'])?$this->sys_input['uid']:0,
            'sdate' => date('Y-m-d 00:00:00', strtotime($this->sys_input['sdate'])),
            'edate' => date('Y-m-d 23:59:59', strtotime($this->sys_input['edate'])),
            'page' => $this->sys_input['page'],
            'search' => '%'.$this->sys_input['search'].'%'
        ];
        
        $r = $this->f_redeem->search($q);

        $this->sys_ok($r);
    }

    function redeem_cancel()
    {
        $r = $this->f_redeem->redeem_cancel($this->sys_input['redeem_id']);
        echo json_encode($r);
    }

    function redeem_send()
    {
        $r = $this->f_redeem->redeem_send($this->sys_input['redeem_id'],
                                        $this->sys_input['redeem_type'],
                                        $this->sys_input['redeem_date'],
                                        $this->sys_input['redeem_note'],
                                        $this->sys_user['user_id']);
        echo json_encode($r);
    }
}

?>