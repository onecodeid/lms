<?php

class M_campaign extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_campaign";
        $this->table_key = "M_CampaignID";
    }
    
    function search_by_code($d)
    {
        $r = $this->db->select("M_CampaignID campaign_id,
                            M_CampaignCode campaign_code,
                            M_CampaignName campaign_name,
                            M_ExpeditionID exp_id,
                            M_ExpeditionROCode exp_code,
                            M_CampaignService service_name,
                            M_CampaignIsCOD is_cod,
                            M_CampaignCODCost cod_cost", false)
                        ->join('m_expedition', 'M_ExpeditionID = M_CampaignM_ExpeditionID')
                        ->where("M_CampaignCode", $d['campaign_code'])
                        ->where("M_CampaignIsActive", "Y")
                        ->get($this->table_name);
        if ($r->num_rows() > 0)
        {
            $r = (array) $r->row();

            // detail
            $this->load->model('master/m_campaigndetail');
            $r['detail'] = $this->m_campaigndetail->search(['campaign_id'=>$r['campaign_id']]);

            unset($r['campaign_id']);
            return $r;
        }

        return false;
    }

    function search( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT M_CampaignID campaign_id,
                    M_CampaignCode campaign_code,
                    M_CampaignName campaign_name,
                    M_ExpeditionID exp_id,
                    M_ExpeditionName exp_name,
                    M_ExpeditionROCode exp_code,
                    M_CampaignService service_name,
                    M_CampaignIsCOD is_cod,
                    M_CampaignCODCost cod_cost
                FROM `{$this->table_name}`
                JOIN m_expedition ON M_CampaignM_ExpeditionID = M_ExpeditionID
                WHERE `M_CampaignName` LIKE ?
                AND `M_CampaignIsActive` = 'Y'", [$d['campaign_name']]);
        if ($r)
        {
            $r = $r->result_array();
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            JOIN m_expedition ON M_CampaignM_ExpeditionID = M_ExpeditionID
            WHERE `M_CampaignName` LIKE ?
            AND `M_CampaignIsActive` = 'Y'", [$d['campaign_name']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }

    function save ( $d, $id = 0 )
    {
        $x = [  'M_CampaignName' => $d['campaign_name'],
                'M_CampaignM_ExpeditionID' => $d['exp_id'],
                'M_CampaignService' => $d['service_name']];

        if ($id == 0)
        {
            $this->db->insert( $this->table_name, $x );
            $id = $this->db->insert_id();
        }
        else
        {
            $this->db->set($x)
                    ->where('M_CampaignID', $id)
                    ->update( $this->table_name );
        }

        return ["status"=>"OK", "data"=>$id, "q"=>$this->db->last_query()];
    }

    // function del ($id)
    // {
    //     $this->db->set('M_CampaignIsActive', 'N')
    //             ->where('M_CampaignID', $this->sys_input['id'])
    //             ->update($this->table_name);

    //     // DEL DETAIL
    //     $this->db->set('M_CampaignDetailIsActive', 'N')
    //             ->where('M_CampaignDetailM_CampaignID', $this->sys_input['id'])
    //             ->update('m_packetdetail');

    //     // DEL PRICE
    //     $this->db->set('M_CampaignPriceIsActive', 'N')
    //             ->where('M_CampaignPriceM_CampaignID', $this->sys_input['id'])
    //             ->update('m_packetprice');

    //     return true;
    // }

    // function search_w_price( $d )
    // {
    //     $l = ['records'=>[], 'total'=>0];

    //     $r = $this->db->query(
    //             "SELECT *, IFNULL(I_StockPacketQty, 0) stock_qty
    //             FROM `{$this->table_name}`
    //             JOIN (
    //                 SELECT M_CampaignPriceM_CampaignID packet_id, SUM(M_CampaignPriceNormal) price_normal,
    //                     SUM(M_CampaignPriceSale) price_sale, GROUP_CONCAT(M_ItemName SEPARATOR ', ') item_names
    //                 FROM m_packetprice
    //                 JOIN m_item ON M_PAcketPriceM_ItemID = M_ItemID
    //                 WHERE M_CampaignPriceIsActive = 'Y' AND M_CampaignPriceM_CustomerLevelID = ?
    //                 GROUP BY M_CampaignPriceM_CampaignID
    //             ) packet_price ON packet_id = M_CampaignID
    //             LEFT JOIN i_stockpacket ON I_StockPacketM_CampaignID = M_CampaignID AND I_StockPacketIsActive = 'Y'
    //             WHERE `M_CampaignName` LIKE ?
    //             AND `M_CampaignIsActive` = 'Y'", [$d['customer_level'], $d['item_name']]);
    //     if ($r)
    //     {
    //         $r = $r->result_array();
    //         $l['records'] = $r;
    //     }

    //     $r = $this->db->query(
    //         "SELECT count(`{$this->table_key}`) n
    //         FROM `{$this->table_name}`
    //         JOIN (
    //                 SELECT M_CampaignPriceM_CampaignID packet_id, SUM(M_CampaignPriceNormal) price_normal,
    //                     SUM(M_CampaignPriceSale) price_sale, GROUP_CONCAT(M_ItemName SEPARATOR ', ') item_names
    //                 FROM m_packetprice
    //                 JOIN m_item ON M_PAcketPriceM_ItemID = M_ItemID
    //                 WHERE M_CampaignPriceIsActive = 'Y' AND M_CampaignPriceM_CustomerLevelID = ?
    //                 GROUP BY M_CampaignPriceM_CampaignID
    //             ) packet_price ON packet_id = M_CampaignID
    //         WHERE `M_CampaignName` LIKE ?
    //         AND `M_CampaignIsActive` = 'Y'", [$d['customer_level'], $d['item_name']]);
    //     if ($r)
    //     {
    //         $l['total'] = $r->row()->n;
    //     }
            
    //     return $l;
    // }
}

?>