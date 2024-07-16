<?php

class C_So extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = 'c_so';
        $this->table_key = 'C_SoID';
    }

    function get_one($id)
    {
        $r = $this->db->select('c_so.*, m_customer.*, fn_so_get_items(L_SoID) items, M_PaymentTypeCode payment_code', false)
                    ->join('m_customer', 'L_SoM_CustomerID = M_CustomerID')
                    ->join('m_paymenttype', 'L_SoM_PaymentID = M_PaymentTypeID')
                    ->where('L_SoID', $id)
                    ->get($this->table_name);
        if ($r->num_rows() > 0)
        {
            $r = $r->row();
            $r->items = json_decode($r->items);
            return $r;
        }

        return false;
    }
}
?>