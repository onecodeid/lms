<?php

class Coupon extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_coupon');
    }

    function search()
    {
        $r = $this->m_coupon->search(['coupon_code'=>'%'.$this->sys_input['search'].'%', 'page'=>$this->sys_input['page']]);
        $this->sys_ok($r);
    }

    function save()
    {
        $this->sys_input['user_id'] = $this->sys_user['user_id'];
        if (isset($this->sys_input['coupon_id']))
            $r = $this->m_coupon->save( $this->sys_input, $this->sys_input['coupon_id'] );
        else
            $r = $this->m_coupon->save( $this->sys_input );
        
        if ($r->status == "OK")
            $this->sys_ok($r->data);
        else
            $this->sys_error('ERROR');
    }

    function del()
    {
        $r = $this->m_coupon->del( $this->sys_input );
        $this->sys_ok($r);
    }

    function check()
    {
        $i = $this->sys_input;
        $r = $this->m_coupon->check($i);
        if ($r->status == "OK")
        {
            $x = json_decode($r->data);
            if (isset($x->coupon_item_id))
                $x->coupon_item_id = json_decode($x->coupon_item_id);
            $this->sys_ok($x);
        }
            
        else
            $this->sys_error($r->message);
    }
}

?>