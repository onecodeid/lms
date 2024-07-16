<?php

class Walast extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('crm/crm_walast');
    }

    function log()
    {
        $r = $this->crm_walast->log($this->sys_input);

        if (!!$r)
        {
            $this->sys_ok($r);
        }
        else
        {
            $this->sys_error($r);
        }
    }
}
?>