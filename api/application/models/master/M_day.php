<?php

class M_day extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_day";
        $this->table_key = "M_DayID";
    }

    function search( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT M_DayID day_id, M_DayNameLocalized day_name
                FROM `{$this->table_name}`
                WHERE `M_DayName` LIKE ?
                AND `M_DayIsActive` = 'Y'", [$d['search']]);
        if ($r)
        {
            $l['records'] = $r->result_array();
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            WHERE `M_DayName` LIKE ?
            AND `M_DayIsActive` = 'Y'", [$d['search']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }
}

?>