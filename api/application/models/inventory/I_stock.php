<?php

/**
 * undocumented class
 */
class I_stock extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "i_stock";
        $this->table_key = "I_StockID";
    }

    function search_item_w_stock( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT *
                FROM m_item
                LEFT JOIN `{$this->table_name}` ON M_ItemID = I_StockM_ItemID AND I_StockIsActive = 'Y' AND I_StockM_WarehouseID = ?
                WHERE (`M_ItemName` LIKE ?)
                AND `M_ItemIsActive` = 'Y'", [$d['warehouse_id'], $d['search']]);
        if ($r)
        {
            $r = $r->result_array();
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`M_ItemID`) n
                FROM m_item
                LEFT JOIN `{$this->table_name}` ON M_ItemID = I_StockM_ItemID AND I_StockIsActive = 'Y' AND I_StockM_WarehouseID = ?
                WHERE (`M_ItemName` LIKE ?)
                AND `M_ItemIsActive` = 'Y'", [$d['warehouse_id'], $d['search']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }
}

?>