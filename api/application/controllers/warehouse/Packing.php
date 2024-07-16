
<?php

class Packing extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('warehouse/w_packing');
    }

    function index()
    {
        return;
    }

    function search()
    {
        $r = $this->w_packing->search(
            ['search'=>'%'.$this->sys_input['search'].'%',
            'sdate'=>date('Y-m-d 00:00:00', strtotime($this->sys_input['sdate'])),
            'edate'=>date('Y-m-d 23:59:59', strtotime($this->sys_input['edate'])),
            'expedition_id'=>isset($this->sys_input['expedition_id'])?$this->sys_input['expedition_id']:0,
            'status'=>$this->sys_input['status']]);
        $this->sys_ok($r);
    }

    function packing()
    {
        $d = ['warehouse_id'=>$this->sys_input['warehouse_id'], 'uid'=>$this->sys_input['user_id'],
                'packing_id'=>isset($this->sys_input['packing_id'])?$this->sys_input['packing_id']:0];
        
        $r = $this->w_packing->packing($d);
        if ($r->status == "OK")
            $this->sys_ok(json_decode($r->data));
        else
            $this->sys_error($r->message);
    }

}

?>