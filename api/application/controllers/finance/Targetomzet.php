<?php

class Targetomzet extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('finance/f_targetomzet');
    }

    function search()
    {
        $q = [
            'search' => '%'.$this->sys_input['search'].'%'
        ];
        
        $r = $this->f_targetomzet->search($q);

        $this->sys_ok($r);
    }

    function save()
    {
        $r = $this->f_targetomzet->save($this->sys_input['data']);

        $this->sys_ok($r);
    }
}

?>