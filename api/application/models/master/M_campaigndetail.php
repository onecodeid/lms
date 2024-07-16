<?php

class M_Campaigndetail extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_campaigndetail";
        $this->table_key = "M_CampaignDetailID";
    }

    function search( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT M_CampaignDetailID campaign_d_id, IFNULL(M_ItemID, M_PacketID) packet_id,
                    IFNULL(M_ItemCode, M_PacketCode) packet_code,
                    IFNULL(M_ItemName, M_PacketName) packet_name, M_CampaignDetailIsPacket is_packet
                FROM `{$this->table_name}`
                LEFT JOIN m_packet ON M_CampaignDetailM_PacketID = M_PacketID AND M_CampaignDetailIsPacket = 'Y'
                LEFT JOIN m_item ON M_CampaignDetailM_ItemID = M_ItemID AND M_CampaignDetailIsPacket = 'N'
                WHERE `M_CampaignDetailM_CampaignID` = ?
                AND `M_CampaignDetailIsActive` = 'Y'", [$d['campaign_id']]);
        if ($r)
        {
            $r = $r->result_array();
            foreach ($r as $k => $v)
            {
                $x = $this->db->query("SELECT fn_campaign_price(?,?) price", [$v['packet_id'], $v['is_packet']])->row();
                $r[$k]['item_price'] = $x->price;
            }
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            LEFT JOIN m_packet ON M_CampaignDetailM_PacketID = M_PacketID AND M_CampaignDetailIsPacket = 'Y'
            LEFT JOIN m_item ON M_CampaignDetailM_ItemID = M_ItemID AND M_CampaignDetailIsPacket = 'N'
            WHERE `M_CampaignDetailM_CampaignID` = ?
            AND `M_CampaignDetailIsActive` = 'Y'", [$d['campaign_id']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }

    function search_by_campaign_code ($d)
    {
        $r = $this->db->where("M_CampaignCode", $d['campaign_code'])
                        ->where("M_CampaignIsActive", "Y")
                        ->get('m_campaign');
        if ($r->num_rows() > 0)
        {
            $r = $r->row();
            return $this->search(['campaign_id'=>$r->M_CampaignID]);
        }

        return false;
    }

    function del ($id)
    {
        $this->db->set('M_CampaignDetailIsActive', 'N')
                ->where('M_CampaignDetailID', $this->sys_input['id'])
                ->update($this->table_name);

        return true;
    }

    function add ($d)
    {
        $r = $this->db->query("SELECT COUNT(*) n
                                FROM m_campaigndetail
                                WHERE M_CampaignDetailM_CampaignID = ?
                                AND M_CampaignDetailM_ItemID = ?
                                AND M_CampaignDetailM_PacketID = ?
                                AND M_CampaignDetailIsPacket = ?
                                AND M_CampaignDetailIsactive = 'Y'", [$d['campaign_id'], $d['item_id'], $d['packet_id'], $d['is_packet']])
                    ->row();
        if ($r->n == 0)
        {
            $this->db->insert($this->table_name, ['M_CampaignDetailM_PacketID'=>$d['packet_id'], 
                                                'M_CampaignDetailM_ItemID'=>$d['item_id'],
                                                'M_CampaignDetailIsPacket'=>$d['is_packet'],
                                                'M_CampaignDetailM_CampaignID'=>$d['campaign_id']]);
            return $this->db->insert_id();
        }

        return false;
    }
}

?>