<?php

class Bankaccount extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_bankaccount');
    }

    function search()
    {
        $search = isset($this->sys_input['search']) ? $this->sys_input['search'] . '%' : '%';
        $page = isset($this->sys_input['page']) ? $this->sys_input['page'] : 1;
        $limit= isset($this->sys_input['limit']) ? $this->sys_input['limit'] : 10;

        $r = $this->m_bankaccount->search(['account_name'=>$search, 'page'=>$page, 'limit'=>$limit]);
        $this->sys_ok($r);
    }

    function save()
    {
        $this->sys_input['user_id'] = $this->sys_user['user_id'];
        if (isset($this->sys_input['account_id']))
            $r = $this->m_bankaccount->save( $this->sys_input, $this->sys_input['account_id'] );
        else
            $r = $this->m_bankaccount->save( $this->sys_input );
        
        if ($r->status == "OK")
            $this->sys_ok($r->data);
        else
            $this->sys_error('ERROR');
    }

    function del()
    {
        $r = $this->m_bankaccount->del( $this->sys_input );
        $this->sys_ok($r);
    }
}

?>