<?php

class C_Invoice extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = 'c_invoice';
        $this->table_key = 'C_InvoiceID';
    }

    function get($invoice_id)
    {
        $r = $this->db->join('c_so', 'C_InvoiceC_SoID = C_SoID')
                ->join('m_customer', 'C_SoM_CustomerID = M_CustomerID')
                ->join('m_paymenttype', 'C_SoM_PaymentID = M_PaymentTypeID')
                ->where('C_InvoiceID', $invoice_id)
                ->get($this->table_name)
                ->row();
        return $r;
    }
}
?>