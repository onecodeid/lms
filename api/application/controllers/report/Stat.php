<?php

class Stat extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('report/r_stat');
    }

    function Stat_sales_001()
    {
        $inp = $this->sys_input;
        $r = $this->r_stat->Stat_sales_001($this->sys_user['user_id'], $inp['sdate'], $inp['edate'], $inp['type']);
        
        $this->sys_ok(json_decode($r['data']));
    }

    function Stat_sales_002()
    {
        $inp = $this->sys_input;
        $level = implode(',',json_decode($inp['level']));
        $r = $this->r_stat->Stat_sales_002($inp['user'], $inp['sdate'], $inp['edate'], $inp['type'], $level, $inp['omzet_min'], $inp['omzet_max']);
        
        $this->sys_ok($r);
    }

    function Stat_sales_003()
    {
        $inp = $this->sys_input;
        $r = $this->r_stat->Stat_sales_003($this->sys_user['user_id'], $inp['sdate'], $inp['edate'], $inp['type']);
        
        $this->sys_ok($r);
    }

    function Stat_sales_005()
    {
        $inp = $this->sys_input;
        $r = $this->r_stat->Stat_sales_005($this->sys_user['user_id'], $inp['sdate'], $inp['edate'], $inp['item_id']);
        
        $this->sys_ok($r);
    }

    function Stat_sales_007()
    {
        $inp = $this->sys_input;
        $r = $this->r_stat->Stat_sales_007($inp['user_id'], $inp['order_by'], $inp['page']);
        
        $this->sys_ok($r);
    }

    function Stat_sales_008()
    {
        $inp = $this->sys_input;
        $r = $this->r_stat->Stat_sales_008($inp['level_id']);
        
        $this->sys_ok($r);
    }

    function Stat_sales_009()
    {
        $inp = $this->sys_input;
        $month = isset($inp['month'])?$inp['month']:date("Y-m");
        $r = $this->r_stat->Stat_sales_009($month);
        
        $this->sys_ok($r);
    }

    function Stat_pareto_per_cities()
    {
        $inp = $this->sys_input;
        $r = [];
        foreach ($inp['cities'] as $k => $v)
        {
            $r[$v] = $this->r_stat->Stat_pareto_per_city($v, $inp['sdate'], $inp['edate'], $inp['type']);
        }        

        $this->sys_ok($r);
    }
}

?>