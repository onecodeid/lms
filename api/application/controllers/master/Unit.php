<?php

class Unit extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_unit');
    }

    function search()
    {
        $r = $this->m_unit->search(['unit_name'=>'%']);
        $this->sys_ok($r);
    }
}

?>