<?php

/**
 * undocumented class
 */
class W_packing extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "w_packing";
        $this->table_key = "W_PackingID";
    }

    function search( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT L_SoExpService, L_SoTotalQty, L_SoTotalWeight, L_SoIsDS, L_SoID, L_SoNumber, L_SoTotalWeight,
                    W_CourierID, W_CourierDate,
                    M_ExpeditionName, M_ExpeditionLogo,
                    W_CourierSent, W_CourierSentDate, 
                    W_CourierProcessing, W_CourierProcessingDate,
                    IFNULL(W_PackingID, 0) W_PackingID,
                    S_UserID, S_UserUsername,
                    W_CourierProcessing, W_CourierSent, IFNULL(W_CourierReceiptNo, '') W_CourierReceiptNo, 
                    IFNULL(W_CourierNote, '') W_CourierNote, M_OrderStatusCode, M_OrderStatusName, 
                    ca.M_CustomerName M_CustomerName, ca.M_CustomerAddress M_CustomerAddress, ca.M_CustomerM_KelurahanID M_CustomerM_KelurahanID,
                    IFNULL(cb.M_CustomerName, '') ds_customer_name, IFNULL(cb.M_CustomerAddress, '') ds_customer_address, IFNULL(cb.M_CustomerM_KelurahanID, 0) ds_kelurahan_id,
                    L_SoNote order_note, IFNULL(L_SoExpOther, '') ex_other, IFNULL(L_SoExpNote, '') ex_note, M_ExpeditionCode ex_code
                FROM `w_courier`
                JOIN l_so ON W_CourierL_SoID = L_SoID
                JOIN m_expedition ON L_SoM_ExpeditionID = M_ExpeditionID
                    AND ((M_ExpeditionID = ? AND ? <> 0) OR ? = 0)
                JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID
                JOIN m_customer ca ON L_SoM_CustomerID = ca.M_CustomerID
                LEFT JOIN m_customer cb ON L_SoDSM_CustomerID = cb.M_CustomerID
                LEFT JOIN w_packing ON L_SoID = W_PackingL_SoID AND W_PackingIsActive = 'Y'
                LEFT JOIN s_user ON W_PackingS_UserID = S_UserID
                WHERE W_CourierIsActive = 'Y'
                AND W_CourierProcessing = 'Y'
                AND (ca.M_CustomerName LIKE ? OR L_SoNumber LIKE ?)
                AND W_CourierDate BETWEEN ? AND ?
                AND ((W_CourierSent = ? AND ? <> 'A') OR (? = 'A'))", [$d['expedition_id'], $d['expedition_id'], $d['expedition_id'], $d['search'], $d['search'], $d['sdate'], $d['edate'], $d['status'], $d['status'], $d['status']]);
        if ($r)
        {
            $r = $r->result_array();
            foreach ($r as $k => $v)
            {
                $x = $this->db->query("SELECT fn_so_get_items(?) x", [$v['L_SoID']])->row();
                $x = json_decode($x->x);
                $y = [];
                foreach ($x as $kk => $vv) $y[] = $vv->item_name;
                    $r[$k]['items'] = $y;

                $x = $this->db->query("SELECT fn_address_json(?) x", [$v['M_CustomerM_KelurahanID']])->row();
                $r[$k]['customer_full_address'] = json_decode($x->x);
                $r[$k]['ds_customer_full_address'] = [];
                if ($v['L_SoIsDS'] == 'Y')
                {
                    $x = $this->db->query("SELECT fn_address_json(?) x", [$v['ds_kelurahan_id']])->row();
                    $r[$k]['ds_customer_full_address'] = json_decode($x->x);
                }   
            }
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`W_CourierID`) n
            FROM `w_courier`
            JOIN l_so ON W_CourierL_SoID = L_SoID
            JOIN m_expedition ON L_SoM_ExpeditionID = M_ExpeditionID
                AND ((M_ExpeditionID = ? AND ? <> 0) OR ? = 0)
            JOIN m_customer ON L_SoM_CustomerID = M_CustomerID
            JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID
            WHERE W_CourierIsActive = 'Y'
                AND W_CourierProcessing = 'Y'
                AND (M_CustomerName LIKE ? OR L_SoNumber LIKE ?)
                AND W_CourierDate BETWEEN ? AND ?
                AND ((W_CourierSent = ? AND ? <> 'A') OR (? = 'A'))", [$d['expedition_id'], $d['expedition_id'], $d['expedition_id'], $d['search'], $d['search'], $d['sdate'], $d['edate'], $d['status'], $d['status'], $d['status']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }

    function packing ($d)
    {
        $r = $this->db->query("CALL sp_wh_packing_set(?, ?)", [$d['warehouse_id'], $d['uid']])
                    ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        return $r;
    }
}

?>