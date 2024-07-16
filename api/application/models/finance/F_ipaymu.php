<?php

class F_ipaymu extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = 'f_ipaymu';
        $this->table_key = 'F_IpaymuID';
    }

    function create($soid, $jdata)
    {
        $r = $this->db->query("CALL sp_finance_ipaymu_create(?, ?)", [$soid, json_encode($jdata)])
                    ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        if ($r->status == "OK") $r->data = json_decode($r->data);
        return $r;
    }

    function notify($d)
    {
        $this->db->set('txt', json_encode($d))
                ->insert('tmp_notify');

        // return true;
        $r = $this->db->query("CALL sp_finance_ipaymu_notify(?, ?, ?, ?)", [$d['trx_id'], $d['sid'], $d['status'], $d['via']])
                    ->row();
        $this->clean_mysqli_connection($this->db->conn_id);
        return $r;
    }

    function cancel($d)
    {
        $this->db->set('txt', json_encode($d))
                ->set('type', 'C')
                ->insert('tmp_notify');

        $r = $this->db->query("CALL sp_finance_ipaymu_notify(?, ?, ?, ?)", [$d['trx_id'], $d['sid'], $d['status'], $d['via']])
                ->row();
        $this->clean_mysqli_connection($this->db->conn_id);
        return $r;
    }

    function notify_qris($d)
    {
        return $this->notify($d);
    }

    function get ($so_id)
    {
        $r = $this->db->where('F_IpaymuL_SoID', $so_id)
                    ->where('F_IpaymuIsActive', 'Y')
                    ->order_by('F_IpaymuCreated', 'DESC')
                    ->limit(1)
                    ->get($this->table_name)
                    ->row();
        return $r;
    }
}
?>