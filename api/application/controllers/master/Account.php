<?php

class Account extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_account');
    }

    function search()
    {
        $r = $this->m_account->search(['account_name'=>$this->sys_input['search'].'%', 'page'=>$this->sys_input['page']]);
        $this->sys_ok($r);
    }

    function save()
    {
        $this->sys_input['user_id'] = $this->sys_user['user_id'];
        if (isset($this->sys_input['account_id']))
            $r = $this->m_account->save( $this->sys_input, $this->sys_input['account_id'] );
        else
            $r = $this->m_account->save( $this->sys_input );
        
        if ($r->status == "OK")
            $this->sys_ok($r->data);
        else
            $this->sys_error('ERROR');
    }

    function del()
    {
        $r = $this->m_account->del( $this->sys_input );
        $this->sys_ok($r);
    }
}

?>