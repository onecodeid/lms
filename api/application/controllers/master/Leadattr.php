<?php

class Leadattr extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_leadattr');
    }

    function search()
    {
        $r = $this->m_leadattr->search([
            'search'=>'%'.(isset($this->sys_input['search'])?$this->sys_input['search']:'').'%',
            'page' => isset($this->sys_input['page'])?$this->sys_input['page']:1,
            'code'=>isset($this->sys_input['code'])?$this->sys_input['code']:""]);
        $this->sys_ok($r);
    }

    function save()
    {
        $this->sys_input['user_id'] = $this->sys_user['user_id'];
        if (isset($this->sys_input['attr_id']))
            $r = $this->m_leadattr->save( $this->sys_input, $this->sys_input['attr_id'] );
        else
            $r = $this->m_leadattr->save( $this->sys_input );
        
        if ($r->status == "OK")
            $this->sys_ok($r->data);
        else
            $this->sys_error('ERROR');
    }

    function del()
    {
        $r = $this->m_leadattr->del( $this->sys_input['id'] );
        $this->sys_ok($r);
    }
}

?>