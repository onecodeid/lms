<?php

class Purchasedetail extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('purchase/p_purchasedetail');
    }

    function search()
    { 
        $r = $this->p_purchasedetail->search([
            'search' => '%',
            'purchase_id' => $this->sys_input['purchase_id']
        ]);

        $this->sys_ok($r);
    }
}

?>