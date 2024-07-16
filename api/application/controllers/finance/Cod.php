<?php

class Cod extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('finance/f_cod');
    }

    function search()
    {
        $q = [
            'uid' => isset($this->sys_input['uid'])?$this->sys_input['uid']:0,
            'sdate' => date('Y-m-d 00:00:00', strtotime($this->sys_input['sdate'])),
            'edate' => date('Y-m-d 23:59:59', strtotime($this->sys_input['edate'])),
            'page' => $this->sys_input['page'],
            'expedition_id' => isset($this->sys_input['expedition_id'])?$this->sys_input['expedition_id']:0
        ];
        
        $r = $this->f_cod->search($q);

        $this->sys_ok($r);
    }

    function search_pending()
    {
        $q = [
            'search' => '%'.$this->sys_input['search'].'%',
            'expedition_id' => isset($this->sys_input['expedition_id'])?$this->sys_input['expedition_id']:0,
            'except' => isset($this->sys_input['except'])?$this->sys_input['except']:json_encode([])
        ];
        $r = $this->f_cod->search_pending($q);

        $this->sys_ok($r);
    }

    function accept()
    {
        $r = $this->f_cod->accept($this->sys_input, $this->sys_user['user_id']);
        if ($r->status == "OK")
            $this->sys_ok(json_decode($r->data));
        else
            $this->sys_error($r->message);
    }

    function reject()
    {
        $r = $this->f_cod->reject($this->sys_input, $this->sys_user['user_id']);
        if ($r->status == "OK")
            $this->sys_ok(json_decode($r->data));
        else
            $this->sys_error($r->message);
    }
}

?>