<?php

class Invoice extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('sales/l_invoice');
    }

    function search()
    {
        $q = [
            'search' => '%'.$this->sys_input['search'].'%',
            'sdate' => date('Y-m-d 00:00:00', strtotime($this->sys_input['sdate'])),
            'edate' => date('Y-m-d 23:59:59', strtotime($this->sys_input['edate']))
        ];
        $r = $this->l_invoice->search($q);

        $this->sys_ok($r);
    }

    function get_last_order()
    {
        $r = $this->l_invoice->get_orders($this->sys_input['customer_id'], 1);

        if ($r)
            $this->sys_ok($r);
        else
            $this->sys_error("Belum ada order");
    }
}

?>