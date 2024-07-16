<?php

class Token extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->load->model('z/z_token');
    }

    function get()
    {
        $u = $this->sys_user;
        $t = $this->z_token->get($u['customer_id']);

        ob_clean();
        $this->sys_ok($t);
    }
}
?>