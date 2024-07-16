
<?php

class Stock extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('inventory/i_stock');
    }

    function index()
    {
        return;
    }

    function search_item_w_stock()
    {
        $r = $this->i_stock->search_item_w_stock(['search'=>'%','warehouse_id'=>$this->sys_input['warehouse_id']]);
        $this->sys_ok($r);
    }

    // function save()
    // {
    //     $r = $this->m_customer->save( $this->sys_input );
    //     echo json_encode($r);
    // }

    // function del()
    // {
    //     $r = $this->m_customer->del( $this->sys_input );
    //     $this->sys_ok($r);
    // }
}

?>