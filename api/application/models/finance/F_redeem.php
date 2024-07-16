<?php

class F_redeem extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = 'f_redeem';
        $this->table_key = 'F_RedeemID';
    }

    function search( $d )
    {
        $limit = 10;
        $page = isset($d['page'])?$d['page']:1;
        $offset = ($page - 1) * 10;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $r = $this->db->query(
                "SELECT F_RedeemID redeem_id, F_RedeemDate redeem_date, M_CustomerName customer_name, M_RewardName reward_name, F_RedeemPoint redeem_point, F_RedeemPointBefore point_before, F_RedeemPointAfter point_after, F_RedeemSent status,
                DATE_FORMAT(F_RedeemSentDate, '%d-%m-%Y') sent_date,
                F_RedeemRequest request, F_RedeemRequestPickupDate request_pickup_date
                FROM `f_redeem`
                JOIN m_customer ON F_RedeemM_CustomerID = M_CustomerID
                JOIN m_reward ON F_RedeemM_RewardID = M_RewardID
                WHERE F_RedeemIsActive = 'Y' 
                AND F_RedeemDate BETWEEN ? AND ?
                AND M_CustomerName LIKE ?
                AND `F_RedeemIsActive` = 'Y'
                LIMIT ? OFFSET ?", [$d['sdate'], $d['edate'], $d['search'], $limit, $offset]);
        if ($r)
        {
            $r = $r->result_array();
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`F_RedeemID`) n
            FROM `f_redeem`
                JOIN m_customer ON F_RedeemM_CustomerID = M_CustomerID
                JOIN m_reward ON F_RedeemM_RewardID = M_RewardID
                WHERE F_RedeemIsActive = 'Y' 
                AND F_RedeemDate BETWEEN ? AND ?
                AND M_CustomerName LIKE ?
                AND `F_RedeemIsActive` = 'Y'", [$d['sdate'], $d['edate'], $d['search']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    function get_redeem_by_user($uid, $sdate, $edate)
    {
        $r = $this->db->query("SELECT fn_finance_redeem_per_user(?,?,?) redeem", [$uid, $sdate, $edate])
                    ->row();
        return $r->redeem;
    }

    function redeem_cancel($id, $note = "")
    {
        $r = $this->db->query("CALL sp_finance_redeem_cancel(?)", [$id])->row();
        $this->clean_mysqli_connection($this->db->conn_id);
        return $r;
    }

    function redeem_send($id, $type, $date, $uid, $note = "")
    {
        $r = $this->db->query("CALL sp_finance_redeem_send(?,?,?,?,?)", [$id, $type, $date, $note, $uid])->row();
        $this->clean_mysqli_connection($this->db->conn_id);
        
        return $r;
    }
}
?>