<?php

class Expense extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_expense');
    }

    function search()
    {
        $r = $this->m_expense->search(['expense_name'=>$this->sys_input['search'].'%', 'page'=>$this->sys_input['page']]);
        $this->sys_ok($r);
    }
}

?>