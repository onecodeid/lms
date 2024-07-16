<?php

class M_unit extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_unit";
        $this->table_key = "M_UnitID";
    }

    function search( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT *
                FROM `{$this->table_name}`
                WHERE `M_UnitName` LIKE ?
                AND `M_UnitIsActive` = 'Y'", [$d['unit_name']]);
        if ($r)
        {
            $r = $r->result_array();
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            WHERE `M_UnitName` LIKE ?
            AND `M_UnitIsActive` = 'Y'", [$d['unit_name']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }
}

?>