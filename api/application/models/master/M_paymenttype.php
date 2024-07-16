<?php

class M_paymenttype extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_paymenttype";
        $this->table_key = "M_PaymentTypeID";
    }

    function search( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT *
                FROM `{$this->table_name}`
                WHERE `M_PaymentTypeName` LIKE ?
                AND `M_PaymentTypeIsActive` = 'Y'
                AND ((M_PaymentTypeCode <> 'COD' AND ? = 'N') OR ? = 'Y')
                AND ((M_PaymentTypeDue = 'N' AND ? = 'N') OR ? = 'Y')", [$d['payment_name'], $d['is_cod'], $d['is_cod'], $d['due'], $d['due']]);
        if ($r)
        {
            $r = $r->result_array();
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            WHERE `M_PaymentTypeName` LIKE ?
            AND `M_PaymentTypeIsActive` = 'Y'
            AND ((M_PaymentTypeCode <> 'COD' AND ? = 'N') OR ? = 'Y')
                AND ((M_PaymentTypeDue = 'N' AND ? = 'N') OR ? = 'Y')", [$d['payment_name'], $d['is_cod'], $d['is_cod'], $d['due'], $d['due']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }

    function search_expanded( $d )
    {
        $l = ['records'=>[]];

        $r = $this->db->query(
            "CALL sp_master_payment_list_all()");
        $this->clean_mysqli_connection($this->db->conn_id);
        
        if (isset($d['is_cod']))
        {
            if ($d['is_cod'] == 'Y')
            {
                $r = $this->db->query(
                    "CALL sp_master_payment_list_all_cod()");
                $this->clean_mysqli_connection($this->db->conn_id);
            }
        }
        
        if ($r)
        {
            $r = $r->result_array();
            $l['records'] = $r;
        }
            
        return $l;
    }

    
}

?>