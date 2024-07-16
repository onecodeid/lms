<?php

class P_purchase extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = 'p_purchase';
        $this->table_key = 'P_PurchaseID';
    }

    function save($inp)
    {
        if (!isset($inp['order_id']))
            $inp['order_id'] = 0;
        $r = $this->db->query("CALL sp_purchase_save(?, ?, ?)", [$inp['order_id'], $inp['hdata'], $inp['jdata']])
                    ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        $r->msg = $this->db->last_query();
        return $r;
    }

    function search( $d )
    {
        $limit = $d['limit'];
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $r = $this->db->query(
                "SELECT P_PurchaseID, P_PurchaseNumber, P_PurchaseDate, P_PurchaseM_VendorID,
                    P_PurchaseTotal,
                    M_VendorCode, M_VendorName
                FROM `{$this->table_name}`
                JOIN m_vendor ON P_PurchaseM_VendorID = M_VendorID
                WHERE (`P_PurchaseNumber` LIKE ? OR `M_VendorName` LIKE ?)
                AND P_PurchaseDate BETWEEN ? AND ?
                AND `P_PurchaseIsActive` = 'Y'
                ORDER BY P_PurchaseNumber DESC
                LIMIT {$limit} OFFSET {$offset}", 
                    [$d['search'], $d['search'], $d['sdate'], $d['edate']]);
        if ($r)
        {
            $r = $r->result_array();    
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
                FROM `{$this->table_name}`
                JOIN m_vendor ON P_PurchaseM_VendorID = M_VendorID
                WHERE (`P_PurchaseNumber` LIKE ? OR `M_VendorName` LIKE ?)
                AND P_PurchaseDate BETWEEN ? AND ?
                AND `P_PurchaseIsActive` = 'Y'", 
                [$d['search'], $d['search'], $d['sdate'], $d['edate']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }
}
?>