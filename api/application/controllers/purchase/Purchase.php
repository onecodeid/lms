<?php

class Purchase extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('purchase/p_purchase');
    }

    function save()
    {
        $r = $this->p_purchase->save($this->sys_input);
        $this->sys_ok($r);
    }

    function search()
    { 
        $q = [
            'search' => '%'.$this->sys_input['search'].'%',
            'sdate' => date('Y-m-d 00:00:00', strtotime($this->sys_input['sdate'])),
            'edate' => date('Y-m-d 23:59:59', strtotime($this->sys_input['edate'])),
            'user_id' => $this->sys_user['user_id'],
            'page' => isset($this->sys_input['page'])?$this->sys_input['page']:1,
            'limit' => isset($this->sys_input['limit'])?$this->sys_input['limit']:10
        ];
        $r = $this->p_purchase->search($q);

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
        $r = $this->p_purchase->approve($d);

        if ($r->status == "OK")
        {
            
            $d = json_decode($r->data);

            $this->load->model('sales/p_purchase');
            $so = $this->p_purchase->get_one($d->so_id);
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
        $this->p_purchase->send_email($invoice_id);

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
        $r = $this->p_purchase->cancel($this->sys_input['so_id']);
        
        if ($r->status == "OK")
            $this->sys_ok(json_decode($r->data));
        else
            $this->sys_error($r->message);
    }
}

?>