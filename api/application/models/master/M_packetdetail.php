<?php

class M_packetdetail extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_packetdetail";
        $this->table_key = "M_PacketDetailID";
    }

    function search( $d )
    {
        $l = ['records'=>[], 'total'=>0, 'header'=>[]];

        $r = $this->db->query(
                "SELECT *
                FROM `{$this->table_name}`
                JOIN m_item ON M_PacketDetailM_ItemID = M_ItemID
                WHERE `M_ItemName` LIKE ?
                AND `M_PacketDetailIsActive` = 'Y'
                AND M_PacketDetailM_PacketID = ?", [$d['item_name'], $d['packet_id']]);
        if ($r)
        {
            $r = $r->result_array();
            // PRICES
            $this->load->model(['master/m_packetprice', 'master/m_packetfee']);
            foreach ($r as $k => $v)
            {
                $x = $this->m_packetprice->search_by_item(['item_id'=>$v['M_ItemID'], 'packet_id'=>$d['packet_id']]);
                $r[$k]['prices'] = $x['records'];
            }

            $x = $this->m_packetfee->search_by_packet(['packet_id'=>$d['packet_id']]);
            $l['fees'] = $x['records'];

            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            JOIN m_item ON M_PacketDetailM_ItemID = M_ItemID
                WHERE `M_ItemName` LIKE ?
                AND `M_PacketDetailIsActive` = 'Y'
                AND M_PacketDetailM_PacketID = ?", [$d['item_name'], $d['packet_id']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }

        // HEADERs
        $r = $this->db->select('*')
                    ->where('M_CustomerLevelIsActive', 'Y')
                    ->order_by('M_CustomerLevelID')
                    ->get('m_customerlevel')
                    ->result_array();
        if ($r)
        {
            $l['header'] = $r;
        }
            
        return $l;
    }

    function search_av_item( $d )
    {
        $limit = isset($d['limit']) ? $d['limit'] : 10;
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $r = $this->db->query(
                "SELECT *
                FROM m_item
                LEFT JOIN m_packetdetail ON M_PacketDetailM_ItemID = M_ItemID
                    AND M_PacketDetailISActive = 'Y'
                    AND M_PacketDetailM_PacketID = ?
                WHERE M_ItemIsActive = 'Y'
                    AND M_ItemName LIKE ?
                    AND M_PacketDetailID IS NULL
                LIMIT {$limit} OFFSET {$offset}", [$d['packet_id'], $d['item_name']]);
        if ($r)
        {
            $r = $r->result_array();

            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`M_ItemID`) n
                FROM m_item
                LEFT JOIN m_packetdetail ON M_PacketDetailM_ItemID = M_ItemID
                    AND M_PacketDetailISActive = 'Y'
                    AND M_PacketDetailM_PacketID = ?
                WHERE M_ItemIsActive = 'Y'
                    AND M_ItemName LIKE ?
                    AND M_PacketDetailID IS NULL", [$d['packet_id'], $d['item_name']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    function add ($d)
    {
        $r = $this->db->query("SELECT COUNT(*) n
                                FROM m_packetdetail
                                WHERE M_PacketDetailM_PacketID = ?
                                AND M_PacketDetailM_ItemID = ?
                                AND M_PAcketDetailIsactive = 'Y'", [$d['packet_id'], $d['item_id']])
                    ->row();
        if ($r->n == 0)
        {
            $this->db->insert($this->table_name, ['M_PacketDetailM_PacketID'=>$d['packet_id'], 'M_PacketDetailM_ItemID'=>$d['item_id']]);
            return $this->db->insert_id();
        }

        return false;
    }

    function save ( $d, $id = 0 )
    {
        $x = [  'M_PacketName' => $d['packet_name'],
                'M_PacketCode' => $d['packet_code']];

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

        return ["status"=>"OK", "data"=>$id, "q"=>$this->db->last_query()];
    }

    function del ($id)
    {
        $this->db->set('M_PacketDetailIsActive', 'N')
                ->where('M_PacketDetailID', $this->sys_input['id'])
                ->update($this->table_name);

        return true;
    }

    function search_header_price( )
    {
        $l = ['header'=>[]];

        // HEADERs
        $r = $this->db->select('*')
                    ->where('M_CustomerLevelIsActive', 'Y')
                    ->order_by('M_CustomerLevelID')
                    ->get('m_customerlevel')
                    ->result_array();
        if ($r)
        {
            $l['header'] = $r;
        }
            
        return $l;
    }
}

?>