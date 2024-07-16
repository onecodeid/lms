<?php

class Omzet_pivot extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('finance/rec_omzet_pivot');
    }

    function search()
    {
        $q = [
            'search' => '%'.$this->sys_input['search'].'%',
            'level_id' => $this->sys_input['level_id'],
            'page' => isset($this->sys_input['page'])? $this->sys_input['page'] : 1
        ];
        $r = $this->rec_omzet_pivot->search($q);

        $this->sys_ok($r);
    }
}

?>