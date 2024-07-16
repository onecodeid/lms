<?php

class M_customeraccount extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_customeraccount";
        $this->table_key = "M_CustomerAccountID";
    }

    function search( $d )
    {
        $limit = isset($d['limit']) ? $d['limit'] : 10;
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $r = $this->db->query(
                "SELECT M_CustomerAccountID account_id, concat(M_BankName, ' No ', M_CustomerAccountNumber, ' a/n ', M_CustomerAccountName) account_name,
                    M_BankLogo bank_logo, M_CustomerAccountNumber account_number, M_CustomerAccountName account_name_only,
                    M_BankName bank_name, M_BankID bank_id
                FROM `{$this->table_name}`
                JOIN m_bank ON M_CustomerAccountM_BankID = M_BankID
                WHERE `M_CustomerAccountName` LIKE ?
                AND `M_CustomerAccountIsActive` = 'Y'
                AND M_CustomerAccountM_CustomerID = ?
                LIMIT {$limit} OFFSET {$offset}", [$d['account_name'], $d['customer_id']]);
        if ($r)
        {
            $l['records'] = $r->result_array();
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            JOIN m_bank ON M_CustomerAccountM_BankID = M_BankID
            WHERE `M_CustomerAccountName` LIKE ?
            AND `M_CustomerAccountIsActive` = 'Y'
            AND M_CustomerAccountM_CustomerID = ?", [$d['account_name'], $d['customer_id']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }
}

?>