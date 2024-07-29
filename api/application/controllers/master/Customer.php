<?php

class Customer extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_customer');
    }

    function index()
    {
        return;
    }

    function search()
    {
        $r = $this->m_customer->search(['customer_name'=>'%'.(isset($this->sys_input['search'])?$this->sys_input['search']:'').'%', 
                                    'page'=>$this->sys_input['page'],
                                    'level'=>$this->sys_input['level'],
                                    'item'=>isset($this->sys_input['item'])?$this->sys_input['item']:0,
                                    'city'=>$this->sys_input['city'],
                                    'province'=>$this->sys_input['province'],
                                    'user_id'=>isset($this->sys_input['user_id'])?$this->sys_input['user_id']:$this->sys_user['user_id']]);
        $r['user'] = $this->sys_user;
        $this->sys_ok($r);
    }

    function search_autocomplete()
    {
        $search = (isset($this->sys_input['search'])?$this->sys_input['search']:'');
        $pattern = '/^(0|62|\+62)/';
        $search = preg_replace($pattern, '', $search);

        unset($this->sys_input['parent']);
        $r = $this->m_customer->search_autocomplete([
            'customer_name'=>'%'.$search.'%', 
            'customer_id'=>isset($this->sys_input['id'])?$this->sys_input['id']:0,
            'user_id'=>$this->sys_user['user_id'],
            'parent_id'=>isset($this->sys_input['parent'])?$this->sys_input['parent']:0]);
        $r['user'] = $this->sys_user;

        $this->load->model('sales/l_so');
        foreach ($r['records'] as $k => $v)
        {
            $x = $this->l_so->search_top_by_customer(['search'=>'%','customer_id'=>$v['M_CustomerID']]);
            $r['records'][$k]['orders'] = $x['records'];
        }

        $this->sys_ok($r);
    }

    function save()
    {
        $this->sys_input['user_id'] = $this->sys_user['user_id'];
        $this->sys_input['customer_phone'] = $this->phone_format(trim($this->sys_input['customer_phone']), "62");
        if (isset($this->sys_input['customer_id']))
            $r = $this->m_customer->edit( $this->sys_input );
        else
            $r = $this->m_customer->save( $this->sys_input );

        // GENERATE MEMBER CARD
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->MEMBER_URL."card_id.php?id=".$r['data'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        // END OF GENERATE MEMBER CARD
// echo $response;
        echo json_encode($r);
    }

    function del()
    {
        $r = $this->m_customer->del( $this->sys_input['id'] );
        $this->sys_ok($r);
    }

    function revoke_user()
    {
        $r = $this->m_customer->revoke_user( $this->sys_input['customer_id'] );
        $this->sys_ok($r);
    }

    function grant_user()
    {
        $r = $this->m_customer->grant_user( $this->sys_input['customer_id'] );
        $this->sys_ok($r);
    }

    function get_one()
    {
        $r = $this->m_customer->get_one($this->sys_input['customer_id']);
        $this->sys_ok($r);
    }

    function customer_non_user()
    {
        $r = $this->m_customer->customer_non_user();
        print_r($r);
    }

    function search_single()
    {
        $r = $this->m_customer->search_single();
        echo json_encode($r);
    }

    function degrade()
    {
        $r = $this->m_customer->degrade($this->sys_input['customer_id'], $this->sys_user['user_id']);
        echo json_encode($r);
    }

    function transfer()
    {
        $r = $this->m_customer->transfer($this->sys_input);
        echo json_encode($r);
    }
}

?>