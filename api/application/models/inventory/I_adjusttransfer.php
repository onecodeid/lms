<?php

/**
 * undocumented class
 */
class I_adjusttransfer extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "i_adjusttransfer";
        $this->table_key = "I_AdjustTransferID";
    }

    function search( $d )
    {
        $limit = 10;
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        if ($d['page'] == -1)
        {
            $limit = 999;
            $offset = 0;
        }

        $r = $this->db->query(
                "SELECT M_ITemCode item_code, M_ItemID item_id, M_ItemName item_name, M_CategoryName category_name,
                    CONCAT('[', GROUP_CONCAT(JSON_OBJECT('warehouse_id', M_WarehouseID, 'warehouse_name', M_WarehouseName,
                        'stock_qty', IFNULL(I_StockQty, 0), 'unit_name', M_UnitName) ORDER BY M_WarehouseID), ']') as stocks, M_ItemImgTmbUri img_uri,
                        M_UnitName unit_name
                FROM `m_item`
                JOIN m_unit ON M_ItemM_UnitID = M_UnitID
                JOIN m_warehouse ON M_WarehouseIsActive = 'Y'
                LEFT JOIN m_category ON M_ItemM_CategoryID = M_CategoryID
                LEFT JOIN i_stock ON I_StockM_ItemID = M_ItemID AND I_StockIsActive = 'Y' AND I_StockM_WarehouseID = M_WarehouseID
                LEFT JOIN m_itemimg ON M_ItemImgM_ItemID = M_ItemID AND M_ItemImgIsActive = 'Y'
                WHERE `M_ItemName` LIKE ?
                AND `M_ItemIsActive` = 'Y'
                GROUP BY M_ItemID
                LIMIT {$limit} OFFSET {$offset}", [$d['item_name']]);
        if ($r)
        {
            $r = $r->result_array();
            foreach ($r as $k => $v) $r[$k]['stocks'] = json_decode($v['stocks']);
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`M_ItemID`) n
            FROM `m_item`
            WHERE `M_ItemName` LIKE ?
            AND `M_ItemIsActive` = 'Y'", [$d['item_name']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    function save( $d )
    {
        $uid = $d['uid'];
        $r = $this->db->query("CALL sp_inventory_adjusttransfer_save(?, ?, ?)", [$d['type'], json_encode($d), $uid])
                    ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        return $r;
    } 
}
