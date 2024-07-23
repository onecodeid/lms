<?php

class M_item extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_item";
        $this->table_key = "M_ItemID";
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
                "SELECT m_item.*, IFNULL(I_StockQty, 0) I_StockQty, M_UnitName, M_ItemImgTmbUri img_uri,
                    IFNULL(M_CategoryName, '') category_name
                FROM `{$this->table_name}`
                JOIN m_unit ON M_ItemM_UnitID = M_UnitID
                LEFT JOIN m_category ON M_ItemM_CategoryID = M_CategoryID
                LEFT JOIN i_stock ON I_StockM_ItemID = M_ItemID
                LEFT JOIN m_itemimg ON M_ItemImgM_ItemID = M_ItemID AND M_ItemImgIsActive = 'Y'
                WHERE `M_ItemName` LIKE ?
                AND `M_ItemIsActive` = 'Y'
                GROUP BY M_ITemID
                LIMIT {$limit} OFFSET {$offset}", [$d['item_name']]);
        if ($r)
        {
            $r = $r->result_array();
            // PRICES
            $this->load->model('master/m_price');
            $this->load->model('master/m_fee');
            foreach ($r as $k => $v)
            {
                $x = $this->m_price->search_by_item(['item_id'=>$v['M_ItemID']]);
                $r[$k]['prices'] = $x['records'];

                $x = $this->m_fee->search_by_item(['item_id'=>$v['M_ItemID']]);
                $r[$k]['fees'] = $x['records'];
            }

            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            WHERE `M_ItemName` LIKE ?
            AND `M_ItemIsActive` = 'Y'", [$d['item_name']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    function search_w_price( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT *, 
                    IF(M_PriceSaleEndDate IS NOT NULL 
                    AND M_PriceSaleStartDate IS NOT NULL
                    AND M_PriceSaleStartDate <= date(now())
                    AND M_PriceSaleEndDate >= date(now()), M_PriceSale, 0) price_sale,
                    IFNULL(I_StockQty, 0) stock_qty
                FROM `{$this->table_name}`
                JOIN m_price ON M_PriceM_ItemID = M_ItemID AND M_PriceIsActive = 'Y'
                    AND M_PriceM_CustomerLevelID = {$d['customer_level']}
                JOIN m_category ON M_ItemM_CategoryID = M_CategoryID
                LEFT JOIN i_stock ON M_ItemID = I_StockM_ItemID AND I_StockIsActive = 'Y'
                LEFT JOIN m_warehouse ON I_StockM_WarehouseID = M_WarehouseID AND M_WarehouseCode = 'WAREHOUSE.SALES'
                WHERE `M_ItemName` LIKE ?
                AND `M_ItemIsActive` = 'Y' AND ((M_ItemID = ? AND ? <> 0) OR ? = 0)
                AND ((M_ItemM_CategoryID = ? AND ? <> 0) OR ? = 0)
                GROUP BY M_ItemID", [$d['item_name'], $d['item_id'], $d['item_id'], $d['item_id'],
                    $d['category'], $d['category'], $d['category']]);
        if ($r)
        {
            $r = $r->result_array();
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(distinct `{$this->table_key}`) n
            FROM `{$this->table_name}`
            JOIN m_price ON M_PriceM_ItemID = M_ItemID AND M_PriceIsActive = 'Y'
                AND M_PriceM_CustomerLevelID = {$d['customer_level']}
            LEFT JOIN i_stock ON M_ItemID = I_StockM_ItemID AND I_StockIsActive = 'Y'
            LEFT JOIN m_warehouse ON I_StockM_WarehouseID = M_WarehouseID AND M_WarehouseCode = 'WAREHOUSE.SALES'
            WHERE `M_ItemName` LIKE ?
            AND `M_ItemIsActive` = 'Y' AND ((M_ItemID = ? AND ? <> 0) OR ? = 0)
            AND ((M_ItemM_CategoryID = ? AND ? <> 0) OR ? = 0)", [$d['item_name'], $d['item_id'], $d['item_id'], $d['item_id'],
                    $d['category'], $d['category'], $d['category']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }

    function save ( $d, $id = 0 )
    {
        $x = [  'M_ItemName' => $d['item_name'],
                'M_ItemCode' => $d['item_code'],
                'M_ItemM_UnitID' => $d['item_unit_id'],
                'M_ItemM_CategoryID' => $d['item_category_id'],
                'M_ItemWeight' => $d['item_weight'],
                'M_ItemMinStock' => $d['item_min'],
                'M_ItemHPP' => $d['item_hpp'],
                'M_ItemIsPublished' => isset($d['item_publish'])?$d['item_publish']:'N'];

        if ($id == 0)
        {
            $this->db->insert( $this->table_name, $x );
            $id = $this->db->insert_id();
        }
        else
        {
            $this->db->set($x)
                    ->where('M_ItemID', $id)
                    ->update( $this->table_name );
        }

        // UPDATE IMAGE
        $this->db->set('M_ItemImgUri', $d['item_img'])
                ->where('M_ItemImgM_ItemID', $id)
                ->where('M_ItemImgIsActive', 'Y')
                ->update('m_itemimg');
                
        // UPDATE PRICES
        $this->db->query("CALL sp_master_price_save(?, ?)", [$id, $d['prices']]);
        $this->clean_mysqli_connection($this->db->conn_id);

        // UPDATE FEES
        $this->db->query("CALL sp_master_fee_save(?, ?)", [$id, $d['fees']]);
        $this->clean_mysqli_connection($this->db->conn_id);

        return ["status"=>"OK", "data"=>$id, "q"=>$this->db->last_query()];
    }

    function del ($id)
    {
        $this->db->set('M_ItemIsActive', 'N')
                ->where('M_ItemID', $this->sys_input['id'])
                ->update($this->table_name);

        // DEL PRICES
        $this->db->set('M_PriceIsActive', 'N')
                ->where('M_PriceM_ItemID', $this->sys_input['id'])
                ->update('m_price');

        return true;
    }
}

?>