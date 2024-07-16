<?php

/**
 * undocumented class
 */
class Z_order extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "z_order";
        $this->table_key = "Z_OrderID";
    }

    function myorder($customerid, $start, $token)
    {
        $r = $this->db->query("CALL sp_z_myorder(?,?,?)", [$customerid, $token, $start])
                ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        return $r;
    }
}

?>