<?php

class M_packetfee extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_packetfee";
        $this->table_key = "M_PacketFeeID";
    }

    function search_by_packet( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT m_customerlevel.*, 
                    IFNULL(M_PacketFeeAmount, 0) M_PacketFeeAmount,
                    IFNULL(M_PacketFeeRewardAmount, 0) M_PacketFeeRewardAmount,
                    IFNULL(M_PacketFeePointAmount, 0) M_PacketFeePointAmount
                FROM `m_customerlevel`
                LEFT JOIN `{$this->table_name}` on M_CustomerLevelID = M_PacketFeeM_CustomerLevelID AND M_PacketFeeIsActive = 'Y'
                    AND M_PacketFeeM_PacketID = ?
                WHERE `M_CustomerLevelIsActive` = 'Y'", [$d['packet_id']]);
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

    function save_fee( $d )
    {
        $r = $this->db->query("CALL sp_master_packetfee_save(?, ?)", [$d['packet_id'], $d['jdata']])
                        ->row();
        return $r;
    }
}

?>