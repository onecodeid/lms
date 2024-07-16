<?php

class Crm_tag extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "crm_tag";
        $this->table_key = "Crm_TagID";
    }

    function search( $d )
    {
        $limit = isset($d['limit']) ? $d['limit'] : 10;
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $r = $this->db->query(
                "SELECT Crm_TagLabel tag_label, Crm_TagID tag_id
                FROM `{$this->table_name}`
                WHERE `Crm_TagLabel` LIKE ?
                AND `Crm_TagIsActive` = 'Y'
                LIMIT {$limit} OFFSET {$offset}", [$d['search']]);
        if ($r)
        {
            $l['records'] = $r->result_array();
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            WHERE `Crm_TagLabel` LIKE ?
            AND `Crm_TagIsActive` = 'Y'", [$d['search']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }
}

?>