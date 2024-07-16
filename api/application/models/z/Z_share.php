<?php

/**
 * undocumented class
 */
class Z_share extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "z_share";
        $this->table_key = "Z_ShareID";
    }

    function share($customerid, $galleryid, $media, $token)
    {
        $r = $this->db->query("CALL sp_z_share(?,?,?,?)", [$customerid, $galleryid, $media, $token])
                ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        return $r;
    }
}

?>