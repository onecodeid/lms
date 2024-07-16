<?php

class M_bankaccount extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_bankaccount";
        $this->table_key = "M_BankAccountID";
    }

    function search( $d )
    {
        $limit = isset($d['limit']) ? $d['limit'] : 10;
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $r = $this->db->query(
                "SELECT M_BankAccountID account_id, concat(M_BankName, ' No ', M_BankAccountNumber, ' a/n ', M_BankAccountName) account_name,
                    M_BankLogo bank_logo, M_BankAccountNumber account_number, M_BankAccountName account_name_only,
                    M_BankName bank_name, M_BankID bank_id
                FROM `{$this->table_name}`
                JOIN m_bank ON M_BankAccountM_BankID = M_BankID
                WHERE `M_BankAccountName` LIKE ?
                AND `M_BankAccountIsActive` = 'Y'
                LIMIT {$limit} OFFSET {$offset}", [$d['account_name']]);
        if ($r)
        {
            $l['records'] = $r->result_array();
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            JOIN m_bank ON M_BankAccountM_BankID = M_BankID
            WHERE `M_BankAccountName` LIKE ?
            AND `M_BankAccountIsActive` = 'Y'", [$d['account_name']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    function save ( $d )
    {
        $r = $this->db->set('M_BankAccountName', $d['account_name'])
                    ->set('M_BankAccountNumber', $d['account_number'])
                    ->set('M_BankAccountM_BankID', $d['account_bank_id']);
                    // ->set('M_BankAccountUserID', $d['user_id']);
        if (isset($d['account_id']))
        {
            $this->db->where('M_BankAccountID', $d['account_id'])
                ->update( $this->table_name );
            $id = $d['account_id'];
        }
        else
        {
            $this->db->insert( $this->table_name );
            $id = $this->db->insert_id();
        }

        if ($r)
        {
            return (object) ["status"=>"OK", "data"=>$id];
        }

        return (object) ["status"=>"ERR"];
    }

    function del ($id)
    {
        $this->db->set('M_BankAccountIsActive', 'N')
                ->where('M_BankAccountID', $this->sys_input['id'])
                ->update($this->table_name);

        return true;
    }
}

?>