<?php

class Fee extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('finance/f_fee');
    }

    function search()
    {
        $q = [
            'uid' => isset($this->sys_input['uid'])?$this->sys_input['uid']:0,
            'sdate' => date('Y-m-d 00:00:00', strtotime($this->sys_input['sdate'])),
            'edate' => date('Y-m-d 23:59:59', strtotime($this->sys_input['edate'])),
            'page' => $this->sys_input['page']
        ];
        
        $r = $this->f_fee->search($q);

        $this->sys_ok($r);
    }

    function search_admin()
    {
        $this->load->model('system/s_usergroup');
        $g = $this->s_usergroup->get_by_code('Z.GROUP.03');
        
        $this->load->model('system/s_user');
        $r = $this->s_user->search(['search'=>'%', 'gid'=>$g->S_UserGroupID]);

        
        foreach ($r['records'] as $k => $v)
        {
            $r['records'][$k]['fee'] = $this->f_fee->get_fee_by_user($v['user_id'], $this->sys_input['sdate'], $this->sys_input['edate']);
        }
        $this->sys_ok($r);
    }
}

?>