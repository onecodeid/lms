<?php

class S_privilege extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "s_privilege";
        $this->table_key = "S_PrivilegeID";
    }

    function save($gid, $privs)
    {
        $r = $this->db->query("CALL sp_system_privilege_save(?, ?)", [$gid, $privs])
                        ->row();
        return $r;
    }
}

?>