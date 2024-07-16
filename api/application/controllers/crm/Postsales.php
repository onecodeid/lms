<?php

class Postsales extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('crm/crm_postsales');
    }

    function search()
    {
        $r = $this->crm_postsales->search($this->sys_input);
        foreach ($r['records'] as $k => $v) 
        {
            $r['records'][$k]['customer_phone'] = $this->phone_format($v['customer_phone'], "62");
            $r['records'][$k]['admin_phone'] = $this->phone_format($v['admin_phone'], "0");
        }

        $this->sys_ok($r);
    }

    function save_template()
    {
        $this->sys_input['user_id'] = $this->sys_user['user_id'];
        if (isset($this->sys_input['id']))
            $r = $this->crm_postsales->save_template( $this->sys_input, $this->sys_input['id'] );
        else
            $r = $this->crm_postsales->save_template( $this->sys_input );
        
        $this->sys_ok($r);
        // if ($r->status == "OK")
        //     $this->sys_ok($r->data);
        // else
        //     $this->sys_error('ERROR');
    }

    /**
     * Profiles
     */
    function search_profile()
    {
        $prm = ['search'=>$this->sys_input['search'].'%', 'page'=>$this->sys_input['page']];
        $r = $this->crm_postsales->search_profile($prm);

        $this->sys_ok($r);
    }

    function delete_profile()
    {
        $r = $this->crm_postsales->delete_profile($this->sys_input);
        $this->sys_ok($r);
    }
}

?>