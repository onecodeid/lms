<?php

class Nt_read extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = 'nt_read';
        $this->primary_key = 'Nt_ReadID';
    }

    function set_read($msgid, $uid, $status = 'Y', $seller = 'N')
    {
        $q = "CALL sp_notice_read(?, ?, ?, ?)";

        $r = $this->db->query($q, [$msgid, $status, $uid, $seller])
                        ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        return json_decode($r->data);
    }

    function get_who_read($msgid)
    {
        $r = $this->db->query("SELECT Nt_ReadNt_MessageID message_id, S_UserID user_id, S_UserFullName user_full_name
                FROM nt_read 
                JOIN s_user ON Nt_ReadS_UserID = S_UserID
                WHERE Nt_ReadIsActive = 'Y'
                AND Nt_ReadNt_MessageID = ?
                AND (Nt_ReadStatus = 'Y' OR Nt_ReadStatus = 'S')", [$msgid])->result_array();

        if ($r) return $r;
        return [];
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
