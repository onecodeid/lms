<?php

class M_packet extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_packet";
        $this->table_key = "M_PacketID";
    }

    function search( $d )
    {
        $limit = isset($d['limit']) ? $d['limit'] : 10;
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $r = $this->db->query(
                "SELECT m_packet.*, M_PacketImgTmbUri packet_img
                FROM `{$this->table_name}`
                LEFT JOIN m_packetimg ON M_PacketImgM_PacketID = M_PacketID
                WHERE `M_PacketName` LIKE ?
                AND `M_PacketIsActive` = 'Y'
                LIMIT {$limit} OFFSET {$offset}", [$d['packet_name']]);
        if ($r)
        {
            $r = $r->result_array();
            // PRICES
            // $this->load->model('master/m_price');
            // foreach ($r as $k => $v)
            // {
            //     $x = $this->m_price->search_by_packet(['packet_id'=>$v['M_PacketID']]);
            //     $r[$k]['prices'] = $x['records'];
            // }

            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            WHERE `M_PacketName` LIKE ?
            AND `M_PacketIsActive` = 'Y'", [$d['packet_name']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    function save ( $d, $id = 0 )
    {
        $x = [  'M_PacketName' => $d['packet_name'],
                'M_PacketCode' => $d['packet_code'],
                'M_PacketIsPublished' => $d['packet_publish']];

        if ($id == 0)
        {
            $this->db->insert( $this->table_name, $x );
            $id = $this->db->insert_id();
        }
        else
        {
            $this->db->set($x)
                    ->where('M_PacketID', $id)
                    ->update( $this->table_name );
        }

        // UPDATE IMAGE
        // THUMBNAIL
        $e = explode(',', $d['packet_img']);
        $x = $e[1];
        $image = imagecreatefromstring(base64_decode($x));
        $image = imagescale($image, 256, 256);

        ob_start (); 
        imagejpeg($image);
        $image_data = ob_get_contents ();
        ob_end_clean ();
        $image_data_base64 = base64_encode ($image_data);
        $tmb = 'data:image/jpeg;base64,'.$image_data_base64;
        // END OF THUMBNAIL

        $this->db->set('M_PacketImgUri', $d['packet_img'])
                ->set('M_PacketImgTmbUri', $tmb)
                ->where('M_PacketImgM_PacketID', $id)
                ->where('M_PacketImgIsActive', 'Y')
                ->update('m_packetimg');

        return ["status"=>"OK", "data"=>$id, "q"=>$this->db->last_query()];
    }

    function del ($id)
    {
        $this->db->set('M_PacketIsActive', 'N')
                ->where('M_PacketID', $this->sys_input['id'])
                ->update($this->table_name);

        // DEL DETAIL
        $this->db->set('M_PacketDetailIsActive', 'N')
                ->where('M_PacketDetailM_PacketID', $this->sys_input['id'])
                ->update('m_packetdetail');

        // DEL PRICE
        $this->db->set('M_PacketPriceIsActive', 'N')
                ->where('M_PacketPriceM_PacketID', $this->sys_input['id'])
                ->update('m_packetprice');

        return true;
    }

    function search_w_price( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT m_packet.*, packet_price.*, M_PacketImgTmbUri packet_img,
                    IF(M_PacketSaleStartDate IS NOT NULL
                        AND M_PacketSaleEndDate IS NOT NULL
                        AND M_PacketSaleStartDate <= date(now())
                        AND M_PacketSaleEndDate >= date(now()), M_PacketSaleAmount, 0) packet_sale_price
                FROM `{$this->table_name}`
                JOIN (
                    SELECT M_PacketPriceM_PacketID packet_id, SUM(M_PacketPriceNormal) price_normal,
                        SUM(M_PacketPriceSale) price_sale, GROUP_CONCAT(M_ItemName SEPARATOR ', ') item_names
                    FROM m_packetprice
                    JOIN m_item ON M_PAcketPriceM_ItemID = M_ItemID
                    WHERE M_PacketPriceIsActive = 'Y' AND M_PacketPriceM_CustomerLevelID = ?
                    GROUP BY M_PacketPriceM_PacketID
                ) packet_price ON packet_id = M_PacketID
                JOIN i_stockpacket ON I_StockPacketM_PacketID = M_PacketID and I_StockPacketIsActive= 'Y' AND I_StockPacketQty > 0
                JOIN m_warehouse ON I_StockPacketM_WarehouseID = M_WarehouseID AND M_WarehouseCode = 'WAREHOUSE.SALES'
                LEFT JOIN m_packetimg ON M_PacketImgM_PacketID = M_PacketID
                LEFT JOIN m_packetsale ON M_PacketSaleM_PacketID = M_PacketID AND M_PacketSaleM_CustomerLEvelID = ?
                    AND M_PacketSaleIsActive = 'Y'
                WHERE `M_PacketName` LIKE ?
                AND `M_PacketIsActive` = 'Y'", [$d['customer_level'], $d['customer_level'], $d['item_name']]);
        if ($r)
        {
            $r = $r->result_array();
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            JOIN (
                    SELECT M_PacketPriceM_PacketID packet_id, SUM(M_PacketPriceNormal) price_normal,
                        SUM(M_PacketPriceSale) price_sale, GROUP_CONCAT(M_ItemName SEPARATOR ', ') item_names
                    FROM m_packetprice
                    JOIN m_item ON M_PAcketPriceM_ItemID = M_ItemID
                    WHERE M_PacketPriceIsActive = 'Y' AND M_PacketPriceM_CustomerLevelID = ?
                    GROUP BY M_PacketPriceM_PacketID
                ) packet_price ON packet_id = M_PacketID
            JOIN i_stockpacket ON I_StockPacketM_PacketID = M_PacketID and I_StockPacketIsActive= 'Y' AND I_StockPacketQty > 0
            JOIN m_warehouse ON I_StockPacketM_WarehouseID = M_WarehouseID AND M_WarehouseCode = 'WAREHOUSE.SALES'
            WHERE `M_PacketName` LIKE ?
            AND `M_PacketIsActive` = 'Y'", [$d['customer_level'], $d['item_name']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }

    function publish ($d)
    {
        $this->db->set("M_PacketIsPublished", $d['publish'])
                ->where('M_PacketID', $d['packet_id'])
                ->update($this->table_name);
        return ['status'=>'OK', 'data'=>$d];
    }
}

?>