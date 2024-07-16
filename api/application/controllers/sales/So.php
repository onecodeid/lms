<?php

class So extends WATZAP_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('sales/l_so');
    }

    function save()
    {
        $r = $this->l_so->save($this->sys_input);

        $x = json_decode($r->data);
        // AUTO APPROVE
        if (isset($x->invoice_id))
        {
            $this->payment_cs($x->so_id);
            // $this->send_email($x->invoice_id);
            $this->report($x->so_id);
        }

        $this->sys_ok($r);
    }

    function save_qo()
    {
        $inp = $this->sys_input;
        $inp['cust_phone'] = $this->phone_format(trim($inp['cust_phone']), "62");
        $r = $this->l_so->save_qo($inp, $this->sys_user['user_id']);

        if ($r->status == "OK")
        {
            $d = json_decode($r->data);
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => base_url()."systm/ipaymu/req_payment_cs",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => ['id'=>$d->so_id]
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $r->response = $response;

            $this->sys_ok(json_decode($r->data));
        }
            
        else
            $this->sys_error($r->message);
    }

    function search()
    { 
        $q = [
            'search' => '%'.$this->sys_input['search'].'%',
            'sdate' => date('Y-m-d 00:00:00', strtotime($this->sys_input['sdate'])),
            'edate' => date('Y-m-d 23:59:59', strtotime($this->sys_input['edate'])),
            'user_id' => $this->sys_user['user_id'],
            'group_code' => $this->sys_user['group_code'],
            'page' => isset($this->sys_input['page'])?$this->sys_input['page']:1,
            'limit' => isset($this->sys_input['limit'])?$this->sys_input['limit']:10
        ];
        $r = $this->l_so->search($q);

        $this->sys_ok($r);
    }

    function approve()
    {
        $d = ['json_data'=>$this->sys_input['json_data'], 
                'header_data'=>
                    [
                        'weight_add'=>$this->sys_input['weight_add'],
                        'service'=>isset($this->sys_input['service'])?$this->sys_input['service']:'',
                        'cost'=>$this->sys_input['cost'],
                        'expedition_id'=>$this->sys_input['expedition_id'],
                        'coupon_id'=>$this->sys_input['coupon_id'],
                        'coupon_amount'=>$this->sys_input['coupon_amount'],
                        'coupon_note' =>$this->sys_input['coupon_note']
                    ],
                'uid'=>$this->sys_user['user_id']];
        $r = $this->l_so->approve($d);

        if ($r->status == "OK")
        {
            
            $d = json_decode($r->data);

            $this->load->model('sales/l_so');
            $so = $this->l_so->get_one($d->so_id);
            if (!$so)
                die();
        
            $response = '';
            if ($so->payment_code == 'IPAYMU.CS')
                $response = $this->payment_cs($d->so_id);
            if ($so->payment_code == 'IPAYMU.QRIS')
                $response = $this->payment_qris($d->so_id);

            // $curl = curl_init();

            // curl_setopt_array($curl, array(
            // CURLOPT_URL => base_url()."systm/ipaymu/req_payment_cs",
            // CURLOPT_RETURNTRANSFER => true,
            // CURLOPT_ENCODING => "",
            // CURLOPT_MAXREDIRS => 10,
            // CURLOPT_TIMEOUT => 0,
            // CURLOPT_FOLLOWLOCATION => true,
            // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            // CURLOPT_CUSTOMREQUEST => "POST",
            // CURLOPT_POSTFIELDS => ['id'=>$d->so_id]
            // ));

            // $response = curl_exec($curl);
            // curl_close($curl);
//             echo base_url()."finance/ipaymu/req_payment_cs";
// print_r($response);
            // print_r($r);
            $r->response = $response;
            $this->sys_ok($r);
        }
        else
            $this->sys_error($r->message);
    }

    function payment_cs($so_id)
    {  
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => base_url()."systm/ipaymu/req_payment_cs",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => ['id'=>$so_id]
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    function payment_qris($so_id)
    {  
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => base_url()."systm/ipaymu/req_payment_qris",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => ['id'=>$so_id]
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    function send_email($invoice_id = 0)
    {
        if ($invoice_id == 0)
            $invoice_id = $this->sys_input['invoice_id'];
        $this->l_so->send_email($invoice_id);
        // $x = $this->l_so->send_email($invoice_id);
        // $x->customer_phone = $this->phone_format($x->customer_phone, '62');

        // return $this->send_watzap($x->customer_phone);

        // CURL
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => base_url()."/systm/mailer/invoice?id=".$invoice_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($invoice_id == 0)
        {
            if ($err) {
                $this->sys_error("cURL Error #:" . $err);
            } else {
                $this->sys_ok($response);
            }
        }
        else
        {
            return $response;
        }
    }

    function send_watzap($destination)
    {
        $this->load->model(['crm/crm_watemplate', 'crm/crm_watzapid']);
        $wa_text = $this->crm_watemplate->get_special('WA.INVOICE');
        $wa_number = $this->crm_watzapid->get();
        
        if (!!$wa_text && $wa_number->invoice_number)
        {
            $url = $this->wat_url_send_message;
            $params = [];
            $params["number_key"] = $wa_number->invoice_number->key; //$this->wat_num_keys[0];
            $params["phone_no"] = $destination;
            $params["message"] = $wa_text->Crm_WaTemplateContent;
    
            // if (isset($this->sys_input['img_url']))
            // {
            //     $params["url"] = $this->sys_input['img_url'];
            //     $params["separate_caption"] = 1;
            //     $url = $this->wat_url_send_image;
            // }
    
            $r = json_decode($this->wat_curl($url, $params));
            if ($r->status == "200")
                $this->sys_ok($r->message);
            else
                $this->sys_error($r);
        }
    }

    function report($so_id)
    {
        // CURL
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => base_url()."/report/one_iv_001?soid=".$so_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        return $response;
    }

    function cancel()
    {        
        $r = $this->l_so->cancel($this->sys_input['so_id']);
        
        if ($r->status == "OK")
            $this->sys_ok(json_decode($r->data));
        else
            $this->sys_error($r->message);
    }

    function get()
    {
        $r = $this->l_so->get($this->sys_input['so_id']);
        echo json_encode($r);
    }

    function save_mp()
    {
        $r = $this->l_so->save_mp($this->sys_input, $this->sys_input['so_id']);

        if ($r->status=='OK')
            $this->sys_ok($r->data);
        else
            $this->sys_error($r->message);
    }
}

?>