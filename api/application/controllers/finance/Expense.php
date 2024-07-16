<?php

class Expense extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('finance/f_expense');
    }

    function search()
    {
        $q = [
            'search' => '%'.$this->sys_input['search'].'%',
            'sdate' => date('Y-m-d 00:00:00', strtotime($this->sys_input['sdate'])),
            'edate' => date('Y-m-d 23:59:59', strtotime($this->sys_input['edate']))
        ];
        
        $r = $this->f_expense->search($q);

        $this->sys_ok($r);
    }

    function save()
    {
        $r = $this->f_expense->save( $this->sys_input, isset($this->sys_input['expense_id'])?$this->sys_input['expense_id']:0 );
        $this->sys_ok($r);
    }

    function del()
    {
        $r = $this->f_expense->del( $this->sys_input['expense_id'] );
        $this->sys_ok($r);
    }
}

?>