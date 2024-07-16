<?php

class P_Purchasedetail extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = 'p_purchasedetail';
        $this->table_key = 'P_PurchaseDetailID';
    }

    function search( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT P_PurchaseDetailID detail_id, M_ItemID item_id, M_ItemCode item_code, M_ItemName item_name, P_PurchaseDetailQty item_qty, P_PurchaseDetailPrice item_price, P_PurchaseDetailTotal item_total
                FROM `{$this->table_name}`
                JOIN m_item ON P_PurchaseDetailM_ItemID = M_ItemID
                WHERE (`M_ItemName` LIKE ?)
                AND `P_PurchaseDetailIsActive` = 'Y'
                AND `P_PurchaseDetailP_PurchaseID` = ?", [$d['search'], $d['purchase_id']]);
        if ($r)
        {
            $r = $r->result_array();
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            JOIN m_item ON P_PurchaseDetailM_ItemID = M_ItemID
                WHERE (`M_ItemName` LIKE ?)
                AND `P_PurchaseDetailIsActive` = 'Y'
                AND `P_PurchaseDetailP_PurchaseID` = ?", [$d['search'], $d['purchase_id']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }
}
?>