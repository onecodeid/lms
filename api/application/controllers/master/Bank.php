<?php

class Bank extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_bank');
    }

    function search()
    {
        $r = $this->m_bank->search(['bank_name'=>'%']);
        $this->sys_ok($r);
    }
}

?>