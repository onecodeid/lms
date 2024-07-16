<?php

class Price extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_price');
    }

    function index()
    {
        echo "Price API";
        return;
    }

    function search_by_item()
    {
        $id = -1;
        if (isset($this->sys_input['item_id']))
            $id = $this->sys_input['item_id'];

        $r = $this->m_price->search_by_item(['item_id'=>$id]);
        $this->sys_ok($r);
    }
}

?>