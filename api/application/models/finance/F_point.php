<?php

class F_point extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = 'f_point';
        $this->table_key = 'F_PointID';
    }

    function search( $d )
    {
        $limit = 10;
        $page = isset($d['page'])?$d['page']:1;
        $offset = ($page - 1) * 10;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $r = $this->db->query(
                "SELECT *
                FROM `f_point`
                JOIN m_customer ON F_PointM_CustomerID = M_CustomerID
                JOIN m_customerlevel ON F_PointM_CustomerLevelID = M_CustomerLevelID
                JOIN s_user ON F_PointS_UserID = S_UserID
                JOIN s_usergroup ON S_UserS_UserGroupID = S_UserGroupID
                JOIN l_so ON F_PointL_SoID = L_SoID
                LEFT JOIN s_userprofile ON S_UserProfileS_UserID = S_UserID
                LEFT JOIN m_item ON F_PointM_ItemID = M_ItemID
                LEFT JOIN m_packet ON F_PointM_PacketID = M_PacketID
                WHERE F_PointIsActive = 'Y' 
                AND ((F_PointS_UserID = ? AND S_UserGroupCode <> 'Z.GROUP.01' AND S_UserGroupCode <> 'Z.GROUP.02')
                        OR (S_UserGroupCode = 'Z.GROUP.01' AND ? = 0) OR (S_UserGroupCode = 'Z.GROUP.02' AND ? = 0))
                AND F_PointL_SoDate BETWEEN ? AND ?
                AND `F_PointIsActive` = 'Y'
                LIMIT ? OFFSET ?", [$d['uid'], $d['uid'], $d['uid'], $d['sdate'], $d['edate'], $limit, $offset]);
        if ($r)
        {
            $r = $r->result_array();
            foreach ($r as $k => $v)
            {
                $ct = $this->db->query("SELECT fn_address_city_name(?) c", [$v['M_CustomerM_KelurahanID']])
                                ->row();
                $r[$k]['city_name'] = $ct->c;
            }
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`F_PointID`) n
            FROM `f_point`
            JOIN m_customer ON F_PointM_CustomerID = M_CustomerID
            JOIN m_customerlevel ON F_PointM_CustomerLevelID = M_CustomerLevelID
            JOIN s_user ON F_PointS_UserID = S_UserID
                JOIN s_usergroup ON S_UserS_UserGroupID = S_UserGroupID
            WHERE F_PointIsActive = 'Y' 
            AND ((F_PointS_UserID = ? AND S_UserGroupCode <> 'Z.GROUP.01' AND S_UserGroupCode <> 'Z.GROUP.02')
                        OR (S_UserGroupCode = 'Z.GROUP.01' AND ? = 0) OR (S_UserGroupCode = 'Z.GROUP.02' AND ? = 0))
                AND F_PointL_SoDate BETWEEN ? AND ?
                AND `F_PointIsActive` = 'Y'", [$d['uid'], $d['uid'], $d['uid'], $d['sdate'], $d['edate']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    function get_point_by_user($uid, $sdate, $edate)
    {
        $r = $this->db->query("SELECT fn_finance_point_per_user(?,?,?) point", [$uid, $sdate, $edate])
                    ->row();
        return $r->point;
    }

    function redeem_reward($customerid, $rewardid, $note)
    {
        $r = $this->db->query("CALL sp_finance_redeem_reward(?, ?, ?)", [$customerid, $rewardid, $note])
                    ->row();
        $this->clean_mysqli_connection($this->db->conn_id);
        return $r;
    }
}
?>