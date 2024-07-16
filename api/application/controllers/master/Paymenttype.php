<?php

class Paymenttype extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_paymenttype');
    }

    function search()
    {
        $d = $this->sys_input;

        $due = 'N';
        if (isset($d['customer_id'])) {
            $this->load->model('master/m_customer');
            $c = $this->m_customer->get_one($d['customer_id']);

            $due = $c->customer_due;
        }

        $r = $this->m_paymenttype->search([
                'payment_name'=>'%', 
                'is_cod'=>isset($d['is_cod'])?$d['is_cod']:'N',
                'due'=>$due
            ]);
        $r['user'] = $this->sys_user;
        $this->sys_ok($r);
    }

    function search_expanded()
    {
        $r = $this->m_paymenttype->search_expanded($this->sys_input);
        $this->sys_ok($r);
    }
}

?>