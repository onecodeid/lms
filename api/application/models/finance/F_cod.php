<?php

class F_cod extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = 'f_cod';
        $this->table_key = 'F_CodID';
    }

    function search( $d )
    {
        $this->load->model('finance/f_coddetail');

        $limit = 10;
        $page = isset($d['page'])?$d['page']:1;
        $offset = ($page - 1) * 10;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $exp = isset($d['expedition_id'])?$d['expedition_id']:0;

        $r = $this->db->query(
                "SELECT *
                FROM `f_cod`
                JOIN m_expedition ON F_CodM_ExpeditionID = M_ExpeditionID
                WHERE F_CodIsActive = 'Y' 
                AND F_CodDate BETWEEN ? AND ?
                AND ((F_CodM_ExpeditionID = ? AND ? <> 0) OR ? = 0)
                LIMIT ? OFFSET ?", [$d['sdate'], $d['edate'], $exp, $exp, $exp, $limit, $offset]);
        if ($r)
        {
            $r = $r->result_array();
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`F_CodID`) n
            FROM `f_cod`
                JOIN m_expedition ON F_CodM_ExpeditionID = M_ExpeditionID
            WHERE F_CodIsActive = 'Y' 
            AND ((F_CodM_ExpeditionID = ? AND ? <> 0) OR ? = 0)
            AND F_CodDate BETWEEN ? AND ?", [$d['sdate'], $d['edate'], $exp, $exp, $exp]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    function search_pending( $d )
    {
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $r = $this->db->query(
                "SELECT L_SoID, date(L_SoDate) L_SoDate, L_SoNumber, L_SoTotalQty, 
                    (L_SoSubTotal - L_SoCouponAmount) as L_SoTotal,
                    L_SoExpCost,
                    M_CustomerName, M_CustomerAddress, M_CustomerM_KelurahanID,
                    F_PaymentID, F_PaymentDate, F_PaymentAmount,
                    L_SoNote order_note,
                    IFNULL(M_ExpeditionName, '') expedition_name,
                    IFNULL(M_ExpeditionLogo, '') expedition_logo,
                    L_SoExpService expedition_service,
                    F_PaymentConfirmed payment_confirmed,
                    IFNULL(M_CustomerLevelName, '-') level_name,
                    IFNULL(M_CustomerPhone, '') customer_phone
                FROM `f_payment`
                JOIN l_so ON L_SoID = F_PaymentL_SoID
                JOIN m_customer ON L_SoM_CustomerID = M_CustomerID
                LEFT JOIN m_customerlevel ON M_CustomerM_CustomerLevelID = M_CustomerLevelID
                JOIN m_expedition ON L_SoM_ExpeditionID = M_ExpeditionID AND M_ExpeditionID = ?
                WHERE (`L_SoNumber` LIKE ? OR `M_CustomerName` LIKE ?)
                AND `F_PaymentIsActive` = 'Y'
                AND F_PaymentIsCOD = 'Y'
                AND F_PaymentConfirmed = 'N'
                ORDER BY L_SoNumber DESC", [$d['expedition_id'], $d['search'], $d['search']]);
        if ($r)
        {
            $r = $r->result_array();
            foreach ($r as $k => $v)
            {
                $ct = $this->db->query("SELECT fn_address_city(?) x", [$v['M_CustomerM_KelurahanID']])->row()->x;
                $ct = json_decode($ct);
                $r[$k]['city_name'] = $ct->city_name;
                $r[$k]['city_id'] = $ct->city_id;

                $itms = $this->db->query("SELECT fn_so_get_items(?) x", [$v['L_SoID']])->row()->x;
                $r[$k]['items'] = json_decode($itms);
            }
                
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`F_PaymentID`) n
            FROM `f_payment`
                JOIN l_so ON L_SoID = F_PaymentL_SoID
                JOIN m_customer ON L_SoM_CustomerID = M_CustomerID
                JOIN m_expedition ON L_SoM_ExpeditionID = M_ExpeditionID AND M_ExpeditionID = ?
                WHERE (`L_SoNumber` LIKE ? OR `M_CustomerName` LIKE ?)
                AND `F_PaymentIsActive` = 'Y'
                AND F_PaymentIsCOD = 'Y'
                AND F_PaymentConfirmed = 'N'", [$d['expedition_id'], $d['search'], $d['search']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = 1;
        }
            
        return $l;
    }

    function accept( $d, $uid )
    {
        $r = $this->db->query("CALL sp_finance_cod_accept(?,?,?)", [$d['hdata'], $d['jdata'], $uid])
                    ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        return $r;
    }

    function reject( $d, $uid )
    {
        $r = $this->db->query("CALL sp_finance_cod_reject(?,?,?)", [$d['hdata'], $d['jdata'], $uid])
                    ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        return $r;
    }
}
?>