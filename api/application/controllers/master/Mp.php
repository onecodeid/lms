<?php

class Mp extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_mp');
    }

    function search()
    {
        $r = $this->m_mp->search(['mp_name'=>'%']);
        $this->sys_ok($r);
    }
}

?>