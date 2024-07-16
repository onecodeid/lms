<?php

class M_bank extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_bank";
        $this->table_key = "M_BankID";
    }

    function search( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT *
                FROM `{$this->table_name}`
                WHERE `M_BankName` LIKE ?
                AND `M_BankIsActive` = 'Y'", [$d['bank_name']]);
        if ($r)
        {
            $l['records'] = $r->result_array();
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            WHERE `M_BankName` LIKE ?
            AND `M_BankIsActive` = 'Y'", [$d['bank_name']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }
}

?>