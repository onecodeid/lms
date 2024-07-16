<?php

class Coddetail extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('finance/f_coddetail');
    }

    function search()
    {
        $q = [
            'search' => isset($this->sys_input['search'])?'%'.$this->sys_input['search'].'%':'%',
            'cod_id' => $this->sys_input['cod_id']
        ];
        
        $r = $this->f_coddetail->search($q);

        $this->sys_ok($r);
    }
}

?>