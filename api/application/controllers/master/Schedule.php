<?php

class Schedule extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_schedule');
    }

    function search()
    {
        $r = $this->m_schedule->search($this->sys_input);
        $this->sys_ok($r);
    }
}

?>