<?php

class Day extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_day');
    }

    function search()
    {
        $r = $this->m_day->search(['search'=>'%']);
        $this->sys_ok($r);
    }
}

?>