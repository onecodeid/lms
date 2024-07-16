<?php

/**
 * undocumented class
 */
class Z_token extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "z_token";
        $this->table_key = "Z_TokenID";
    }

    function get($customerid)
    {
        $r = $this->db->query("CALL sp_z_token_get(?)", [$customerid])
                ->result_array();
        $this->clean_mysqli_connection($this->db->conn_id);

        $token = [];
        foreach ($r as $k => $v)
            $token[] = $v['token_code'];

        return $token;
    }
}

?>