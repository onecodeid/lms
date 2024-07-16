<?php

class Salesdur extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('crm/crm_salesdur');
    }

    function search()
    {
        $r = $this->crm_salesdur->search(['search'=>'%']);
        $this->sys_ok($r);
    }

    function save()
    {
        $this->sys_input['user_id'] = $this->sys_user['user_id'];
        if (isset($this->sys_input['salesdur_id']))
            $r = $this->crm_salesdur->save( $this->sys_input, $this->sys_input['salesdur_id'] );
        else
            $r = $this->crm_salesdur->save( $this->sys_input );
        
        if ($r->status == "OK")
            $this->sys_ok($r->data);
        else
            $this->sys_error('ERROR');
    }

    function del()
    {
        $r = $this->crm_salesdur->del( $this->sys_input );
        $this->sys_ok($r);
    }
}

?>