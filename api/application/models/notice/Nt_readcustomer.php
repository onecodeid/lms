<?php

class Nt_readcustomer extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = 'nt_readcustomer';
        $this->primary_key = 'Nt_ReadCustomerID';
    }

    function set_read($msgid, $uid, $status = 'Y', $seller = 'Y')
    {
        $q = "CALL sp_notice_read(?, ?, ?, ?)";

        $r = $this->db->query($q, [$msgid, $status, $uid, $seller])
                        ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        return json_decode($r->data);
    }

    function get_who_read($msgid)
    {
        $r = $this->db->query("SELECT Nt_ReadCustomerNt_MessageID message_id, M_CustomerID customer_id, M_CustomerName customer_name
                FROM {$this->table_name} 
                JOIN m_customer ON Nt_ReadCustomerM_CustomerID = M_CustomerID
                WHERE Nt_ReadCustomerIsActive = 'Y'
                AND Nt_ReadCustomerNt_MessageID = ?
                AND (Nt_ReadCustomerStatus = 'Y' OR Nt_ReadCustomerStatus = 'S')", [$msgid])->result_array();

        if ($r) return $r;
        return [];
    }

    function get_cnt_read($msgid)
    {
        $r = $this->db->query("SELECT COUNT(M_CustomerID) n
                FROM {$this->table_name} 
                JOIN m_customer ON Nt_ReadCustomerM_CustomerID = M_CustomerID
                WHERE Nt_ReadCustomerIsActive = 'Y'
                AND Nt_ReadCustomerNt_MessageID = ?
                AND (Nt_ReadCustomerStatus = 'Y' OR Nt_ReadCustomerStatus = 'S')", [$msgid])->row();

        if ($r) return $r->n;
        return 0;
    }

    // function get_unread_popup($uid, $seller = "N")
    // {
    //     $r = $this->db->query("CALL sp_system_notif_unread_popup(?, ?)", [$uid, $seller])
    //                     ->row();
    //     $this->clean_mysqli_connection($this->db->conn_id);        

    //     return json_decode($r->data);
    // }

    // function set_read($uid, $seller = "N")
    // {
    //     $r = $this->db->query("CALL sp_system_notif_read(?, ?)", [$uid, $seller])
    //                     ->row();
    //     $this->clean_mysqli_connection($this->db->conn_id);        

    //     return json_decode($r->data);
    // }

    // function set_read_id($uid, $id)
    // {
    //     $r = $this->db->query("CALL sp_system_notif_read_id(?, ?)", [$uid, $id])
    //                     ->row();
    //     $this->clean_mysqli_connection($this->db->conn_id);        

    //     return json_decode($r->data);
    // }
}
?>
