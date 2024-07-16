<?php

class Color extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_color');
    }

    function search()
    {
        $r = $this->m_color->search(['search'=>'%']);
        $this->sys_ok($r);
    }

    // function save()
    // {
    //     $this->sys_input['user_id'] = $this->sys_user['user_id'];
    //     if (isset($this->sys_input['category_id']))
    //         $r = $this->m_category->save( $this->sys_input, $this->sys_input['category_id'] );
    //     else
    //         $r = $this->m_category->save( $this->sys_input );
        
    //     if ($r->status == "OK")
    //         $this->sys_ok($r->data);
    //     else
    //         $this->sys_error('ERROR');
    // }

    // function del()
    // {
    //     $r = $this->m_category->del( $this->sys_input );
    //     $this->sys_ok($r);
    // }
}

?>