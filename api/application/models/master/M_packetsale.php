<?php

class M_packetsale extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_packetsale";
        $this->table_key = "M_PacketSaleID";
    }

    function search_by_packet( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT m_customerlevel.*, 
                    IFNULL(M_PacketSaleAmount, 0) M_PacketSaleAmount, 
                    IFNULL(M_PacketSaleStartDate, date(now())) M_PacketSaleStartDate,
                    IFNULL(M_PacketSaleEndDate, date(now())) M_PacketSaleEndDate
                FROM `m_customerlevel`
                LEFT JOIN `{$this->table_name}` on M_CustomerLevelID = M_PacketSaleM_CustomerLevelID AND M_PacketSaleIsActive = 'Y'
                    AND M_PacketSaleM_PacketID = ?
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

    function save( $d )
    {
        $r = $this->db->query("CALL sp_master_packet_sale_save(?, ?)", [$d['packet_id'], $d['jdata']])
                        ->row();
        $this->clean_mysqli_connection($this->db->conn_id);
        return $r;
    }
}

?>