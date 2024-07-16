
<?php

class Adjusttransfer extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('inventory/i_adjusttransfer');
    }

    function index()
    {
        return;
    }

    function search()
    {
        $r = $this->i_adjusttransfer->search(
            ['item_name'=>'%'.$this->sys_input['search'].'%', 
            'page'=>$this->sys_input['page']]);
        foreach ($r['records'] as $k => $v)
            $r['records'][$k]['img_url'] = base_url() . 'master/itemimg/show_tmb?id=' . $v['item_id'].'&t='.strtotime(date('Y-m-d H:i:s'));
        $this->sys_ok($r);
    }

    function transfer()
    {
        $this->sys_input['type'] = 'TRANSFER';
        $this->sys_input['uid'] = $this->sys_user['user_id'];
        $r = $this->i_adjusttransfer->save( $this->sys_input );

        if ($r->status == "OK")
            $this->sys_ok($r);
        else
            $this->sys_error($r);
    }

    function adjust()
    {
        $this->sys_input['type'] = 'ADJUST';
        $this->sys_input['uid'] = $this->sys_user['user_id'];
        $r = $this->i_adjusttransfer->save( $this->sys_input );

        if ($r->status == "OK")
            $this->sys_ok($r);
        else
            $this->sys_error($r);
    }
}

?>