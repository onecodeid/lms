<?php

class F_coddetail extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = 'f_coddetail';
        $this->table_key = 'F_CodDetailID';
    }

    function search( $d )
    {
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $r = $this->db->query(
                "SELECT L_SoID, date(L_SoDate) L_SoDate, L_SoNumber, L_SoTotalQty, 
                    (L_SoSubTotal - L_SoCouponAmount) as L_SoTotal,
                    L_SoExpCost,
                    M_CustomerName, M_CustomerAddress, M_CustomerM_KelurahanID,
                    F_PaymentID, F_PaymentDate, F_PaymentAmount,
                    L_SoNote order_note,
                    L_SoExpService expedition_service,
                    F_PaymentConfirmed payment_confirmed,
                    IFNULL(M_CustomerLevelName, '-') level_name,
                    IFNULL(M_CustomerPhone, '') customer_phone
                FROM f_coddetail
                JOIN `f_payment` ON F_CodDetailF_PaymentID = F_PaymentID
                JOIN l_so ON L_SoID = F_PaymentL_SoID
                JOIN m_customer ON L_SoM_CustomerID = M_CustomerID
                LEFT JOIN m_customerlevel ON M_CustomerM_CustomerLevelID = M_CustomerLevelID
                WHERE F_CodDetailF_CodID = ?
                AND (`L_SoNumber` LIKE ? OR `M_CustomerName` LIKE ?)
                AND `F_CodDetailIsActive` = 'Y'
                ORDER BY L_SoNumber DESC", [$d['cod_id'], $d['search'], $d['search']]);
        if ($r)
        {
            $r = $r->result_array();
            foreach ($r as $k => $v)
            {
                $ct = $this->db->query("SELECT fn_address_city(?) x", [$v['M_CustomerM_KelurahanID']])->row()->x;
                $ct = json_decode($ct);
                $r[$k]['city_name'] = $ct->city_name;
                $r[$k]['city_id'] = $ct->city_id;

                // $itms = $this->db->query("SELECT fn_so_get_items(?) x", [$v['L_SoID']])->row()->x;
                // $r[$k]['items'] = json_decode($itms);
            }
                
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`F_CodDetailID`) n
            FROM `f_coddetail`
            JOIN `f_payment` ON F_CodDetailF_PaymentID
                JOIN l_so ON L_SoID = F_PaymentL_SoID
                JOIN m_customer ON L_SoM_CustomerID = M_CustomerID
                WHERE F_CodDetailF_CodID = ?
                AND (`L_SoNumber` LIKE ? OR `M_CustomerName` LIKE ?)
                AND `F_CodDetailIsActive` = 'Y'", [$d['cod_id'], $d['search'], $d['search']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = 1;
        }
            
        return $l;
    }
}
?>