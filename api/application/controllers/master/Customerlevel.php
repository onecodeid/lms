<?php

class Customerlevel extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_customerlevel');
    }

    function index()
    {
        echo "Customer Level API";
        return;
    }

    function search()
    {
        $p = ['customer_level_name'=>'%'.(isset($this->sys_input['search'])?$this->sys_input['search']:'').'%', 
            'user_id'=>$this->sys_user['user_id']];
        if (isset($this->sys_input['include_inactive']))
            $p['include_inactive'] = $this->sys_input['include_inactive'];

        $r = $this->m_customerlevel->search( $p );
        $this->sys_ok($r);
    }

    function save()
    {
        $this->sys_input['user_id'] = $this->sys_user['user_id'];
        if (isset($this->sys_input['level_id']))
            $r = $this->m_customerlevel->save( $this->sys_input, $this->sys_input['level_id'] );
        else
            $r = $this->m_customerlevel->save( $this->sys_input );
        
        if ($r->status == "OK")
            $this->sys_ok($r->data);
        else
            $this->sys_error('ERROR');
    }

    function del()
    {
        $r = $this->m_customerlevel->del( $this->sys_input );
        $this->sys_ok($r);
    }

    function restore()
    {
        $r = $this->m_customerlevel->restore( $this->sys_input );
        $this->sys_ok($r);
    }
}

?>