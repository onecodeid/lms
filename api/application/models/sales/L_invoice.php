<?php

class L_Invoice extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = 'l_invoice';
        $this->table_key = 'L_InvoiceID';
    }

    function search( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT *
                FROM `{$this->table_name}`
                JOIN l_so ON L_SoID = L_InvoiceL_SoID
                JOIN m_customer ON L_SoM_CustomerID = M_CustomerID
                LEFT JOIN m_customerlevel ON M_CustomerM_CustomerLevelID = M_CustomerLevelID
                JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID 
                    AND M_OrderStatusCode = 'SO.Confirmed'
                LEFT JOIN m_kelurahan ON M_KelurahanID = M_CustomerM_KelurahanID
                WHERE (`L_SoNumber` LIKE ? OR `L_InvoiceNumber` LIKE ? OR `M_CustomerName` LIKE ?)
                AND L_SoDate BETWEEN ? AND ?
                AND `L_SoIsActive` = 'Y'", [$d['search'], $d['search'], $d['search'], $d['sdate'], $d['edate']]);
        if ($r)
        {
            $r = $r->result_array();
            foreach ($r as $k => $v)
            {
                $ct = $this->db->query("SELECT fn_address_city(?) x", [$v['M_CustomerM_KelurahanID']])->row()->x;
                $ct = json_decode($ct);
                $r[$k]['city_name'] = $ct->city_name;
                $r[$k]['city_id'] = $ct->city_id;
            }
                
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            JOIN l_so ON L_SoID = L_InvoiceL_SoID
            JOIN m_customer ON L_SoM_CustomerID = M_CustomerID
            LEFT JOIN m_customerlevel ON M_CustomerM_CustomerLevelID = M_CustomerLevelID
            JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID
                AND M_OrderStatusCode = 'SO.Confirmed'
            WHERE (`L_SoNumber` LIKE ? OR `L_InvoiceNumber` LIKE ? OR `M_CustomerName` LIKE ?)
            AND L_SoDate BETWEEN ? AND ?
                AND `L_SoIsActive` = 'Y'", [$d['search'], $d['search'], $d['search'], $d['sdate'], $d['edate']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }

    function get($invoice_id)
    {
        $r = $this->db->join('l_so', 'L_InvoiceL_SoID = L_SoID')
                ->join('m_customer', 'L_SoM_CustomerID = M_CustomerID')
                ->join('m_paymenttype', 'L_SoM_PaymentID = M_PaymentTypeID')
                ->where('L_InvoiceID', $invoice_id)
                ->get($this->table_name)
                ->row();
        return $r;
    }

    function get_orders( $cid, $limit = 5 )
    {
        $r = $this->db->select('L_SoID so_id, L_SoDate so_date, L_SoNumber so_number,
                    CONCAT("[", GROUP_CONCAT( 
                    JSON_OBJECT("item_code", L_SoDetailM_ItemCode, "item_name", L_SoDetailM_ItemName,
                    "item_qty", L_SoDetailApprovedQty) SEPARATOR ","), "]") as details
                    ', false)
                ->join('l_so', 'L_InvoiceL_SoID = L_SoID')
                ->join('l_sodetail', 'L_SoDetailL_SoID = L_SoID AND L_SoDetailIsActive = "Y" AND L_SoDetailApprovedQty > 0')
                ->join('m_customer', 'L_SoM_CustomerID = M_CustomerID')
                ->join('m_paymenttype', 'L_SoM_PaymentID = M_PaymentTypeID')
                ->where('L_SoM_CustomerID', $cid)
                ->group_by('L_SoID')
                ->order_by('L_InvoiceDate DESC')
                ->limit($limit)
                ->get($this->table_name);

        if ($r->num_rows() > 0) 
        {
            if ($limit == 1) {
                $rst = $r->row();
                $rst->details = json_decode($rst->details);

                return $rst;
            }

            $rst = $r->result_array();
            foreach ($rst as $k => $v)
                $rst[$k]['details'] = json_decode($v['details']);

            return $rst;
        }
                
        return false;
    }
}
?>