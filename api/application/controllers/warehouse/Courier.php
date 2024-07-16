
<?php

class Courier extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('warehouse/w_courier');
    }

    function index()
    {
        return;
    }

    function search()
    {
        $r = $this->w_courier->search(
            ['search'=>'%'.$this->sys_input['search'].'%',
            'sdate'=>date('Y-m-d 00:00:00', strtotime($this->sys_input['sdate'])),
            'edate'=>date('Y-m-d 23:59:59', strtotime($this->sys_input['edate'])),
            'expedition_id'=>isset($this->sys_input['expedition_id'])?$this->sys_input['expedition_id']:0,
            'page'=>isset($this->sys_input['page'])?$this->sys_input['page']:1,
            'status'=>$this->sys_input['status']]);
        $this->sys_ok($r);
    }

    function process()
    {
        $d = ['warehouse_id'=>$this->sys_input['warehouse_id'], 'uid'=>$this->sys_user['user_id']];
        $r = $this->w_courier->process($d);
        if ($r->status == "OK")
            $this->sys_ok(json_decode($r->data));
        else
            $this->sys_error($r->message);
    }

    function send()
    {
        $d = $this->sys_input;
        $d['uid'] = $this->sys_user['user_id'];

        $r = $this->w_courier->send($d);
        if ($r->status == "OK")
            $this->sys_ok(json_decode($r->data));
        else
            $this->sys_error($r->message);
    }

    function receipt()
    {
        $d = $this->sys_input;
        $d['uid'] = $this->sys_user['user_id'];

        $r = $this->w_courier->receipt($d);
        if ($r->status == "OK")
            $this->sys_ok(json_decode($r->data));
        else
            $this->sys_error($r->message);
    }
}

?>