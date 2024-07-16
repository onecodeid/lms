<?php

class Payment extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('finance/f_payment');
    }

    function search()
    {
        $q = [
            'search' => '%'.$this->sys_input['search'].'%',
            'status' => $this->sys_input['status'],
            'sdate' => date('Y-m-d 00:00:00', strtotime($this->sys_input['sdate'])),
            'edate' => date('Y-m-d 23:59:59', strtotime($this->sys_input['edate'])),
            'page' => isset($this->sys_input['page'])? $this->sys_input['page'] : 1
        ];
        $r = $this->f_payment->search($q);

        $this->sys_ok($r);
    }

    function search_cod()
    {
        $q = [
            'search' => '%'.$this->sys_input['search'].'%',
            'status' => $this->sys_input['status'],
            'sdate' => date('Y-m-d 00:00:00', strtotime($this->sys_input['sdate'])),
            'edate' => date('Y-m-d 23:59:59', strtotime($this->sys_input['edate'])),
            'page' => isset($this->sys_input['page'])? $this->sys_input['page'] : 1
        ];
        $r = $this->f_payment->search_cod($q);

        $this->sys_ok($r);
    }

    function confirm()
    {
        $r = $this->f_payment->confirm($this->sys_input['payment_id'], $this->sys_user['user_id']);
        if ($r->status == "OK")
            $this->sys_ok($r->data);
        else
            $this->sys_error($r->message);
    }

    function save()
    {
        $r = $this->f_payment->save( $this->sys_input );
        if ($r->status == "OK") {
            $x = json_decode($r->data);

            // Upload Receipt
            if ($this->sys_input['receipt_img'] != '')
                $this->upload_receipt($x->order_id, $this->sys_input['receipt_img']);

            // Confirm
            // $this->f_payment->confirm($x->order_id, $this->sys_user['user_id']);

            $this->sys_ok(json_decode($r->data));
        }
            
        else
            $this->sys_error($r->message);
    }

    function upload_receipt($p_id, $r_img)
    {
        $ts = 'r_'.strtotime(date('Y-m-d H:i:s')).'.jpg';
        $this->base64_to_jpeg($r_img, "/var/www/html/platform-zalfa/uploads/receipts/".$ts);

        $this->f_payment->upload_receipt($p_id, $ts);
    }
}

?>