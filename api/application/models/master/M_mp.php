<?php

class M_mp extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_mp";
        $this->table_key = "M_MpID";
    }

    function search( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT *
                FROM `{$this->table_name}`
                WHERE `M_MpName` LIKE ?
                AND `M_MpIsActive` = 'Y'", [$d['mp_name']]);
        if ($r)
        {
            $l['records'] = $r->result_array();
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            WHERE `M_MpName` LIKE ?
            AND `M_MpIsActive` = 'Y'", [$d['mp_name']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }
}

?>