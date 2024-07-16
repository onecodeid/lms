<?php

class Sodetail extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('sales/l_sodetail');
    }

    // function save()
    // {
    //     $r = $this->l_so->save($this->sys_input);

    //     $this->sys_ok($r);
    // }

    function search()
    {
        $q = [
            'search' => '%'.$this->sys_input['search'].'%',
            'order_id' => $this->sys_input['order_id']
        ];
        $r = $this->l_sodetail->search($q);

        foreach ($r['records'] as $k => $v)
        {
            if ($v['L_SoDetailIsPacket'] == 'N')
                $r['records'][$k]['img_url'] = base_url() . 'master/itemimg/show_tmb?id=' . $v['M_ItemID'];
            else
                $r['records'][$k]['img_url'] = base_url() . 'master/packetimg/show_tmb?id=' . $v['M_ItemID'];
        }
        $this->sys_ok($r);
    }

    function cancel()
    {
        $r = $this->l_sodetail->cancel($this->sys_input['id']);
        if ($r->status == "OK")
            $this->sys_ok(json_decode($r->data));
        else
            $this->sys_error($r->message);
    }
}

?>