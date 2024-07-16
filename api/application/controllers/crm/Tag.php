<?php

class Tag extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('crm/crm_tag');
    }


    // function save()
    // {
    //     $this->sys_input['user_id'] = $this->sys_user['user_id'];
    //     if (isset($this->sys_input['salesdur_id']))
    //         $r = $this->crm_salesdur->save( $this->sys_input, $this->sys_input['salesdur_id'] );
    //     else
    //         $r = $this->crm_salesdur->save( $this->sys_input );
        
    //     if ($r->status == "OK")
    //         $this->sys_ok($r->data);
    //     else
    //         $this->sys_error('ERROR');
    // }

    // function del()
    // {
    //     $r = $this->crm_salesdur->del( $this->sys_input );
    //     $this->sys_ok($r);
    // }

    function search()
    {
        $r = $this->crm_tag->search(['search'=>'%'.(isset($this->sys_input['search'])?$this->sys_input['search']:'').'%', 
                                    'page'=>$this->sys_input['page'],
                                    'limit'=>$this->sys_input['limit']?$this->sys_input['limit']:10]);
        $this->sys_ok($r);
    }
}

?>