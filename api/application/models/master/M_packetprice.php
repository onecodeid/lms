<?php

class M_packetprice extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_packetprice";
        $this->table_key = "M_PacketPriceID";
    }

    function search_by_item( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT m_customerlevel.*, 
                    IFNULL(M_PacketPriceNormal, IFNULL(M_PriceTotal, 0)) M_PacketPriceNormal, 
                    IFNULL(M_PacketPriceSale, 0) M_PacketPriceSale
                FROM `m_customerlevel`
                LEFT JOIN `{$this->table_name}` on M_CustomerLevelID = M_PacketPriceM_CustomerLevelID AND M_PacketPriceM_ItemID = ? AND M_PacketPriceIsActive = 'Y'
                    AND M_PacketPriceM_PacketID = ?
                LEFT JOIN `m_price` on M_CustomerLevelID = M_PriceM_CustomerLevelID AND M_PriceM_ItemID = ? AND M_PriceIsActive = 'Y' 
                WHERE `M_CustomerLevelIsActive` = 'Y'", [$d['item_id'], $d['packet_id'], $d['item_id']]);
        if ($r)
        {
            $r = $r->result_array();
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`M_CustomerLevelID`) n
                FROM `m_customerlevel`
                WHERE `M_CustomerLevelIsActive` = 'Y'", []);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }

    function save_price( $d )
    {
        $r = $this->db->query("CALL sp_master_packetprice_save(?, ?, ?)", [$d['packet_id'], $d['item_id'], $d['jdata']])
                        ->row();
        return $r;
    }
}

?>