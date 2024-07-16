<?php

/**
 * undocumented class
 */
class W_courier extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "w_courier";
        $this->table_key = "W_CourierID";
    }

    function search( $d )
    {
        $limit = isset($d['limit']) ? $d['limit'] : 10;
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $r = $this->db->query(
                "SELECT L_SoExpService, L_SoTotalQty, L_SoTotalWeight, L_SoIsDS, L_SoID, L_SoNumber, L_SoTotalWeight,
                    W_CourierID, W_CourierDate,
                    M_ExpeditionName, M_ExpeditionLogo,
                    W_CourierSent, W_CourierSentDate, 
                    W_CourierProcessing, W_CourierProcessingDate,
                    S_UserID, S_UserUsername,
                    W_CourierProcessing, W_CourierSent, IFNULL(W_CourierReceiptNo, '') W_CourierReceiptNo, 
                    IFNULL(W_CourierNote, '') W_CourierNote, M_OrderStatusCode, M_OrderStatusName, 
                    ca.M_CustomerName M_CustomerName, ca.M_CustomerAddress M_CustomerAddress, ca.M_CustomerM_KelurahanID M_CustomerM_KelurahanID,
                    IFNULL(cb.M_CustomerName, '') ds_customer_name, IFNULL(cb.M_CustomerAddress, '') ds_customer_address, IFNULL(cb.M_CustomerM_KelurahanID, 0) ds_kelurahan_id,
                    L_SoNote order_note, IFNULL(L_SoExpOther, '') ex_other, IFNULL(L_SoExpNote, '') ex_note, M_ExpeditionCode ex_code,
                    M_CustomerLevelCode level_code, M_CustomerLevelName level_name
                FROM `{$this->table_name}`
                JOIN l_so ON W_CourierL_SoID = L_SoID
                JOIN m_expedition ON L_SoM_ExpeditionID = M_ExpeditionID
                    AND ((M_ExpeditionID = ? AND ? <> 0) OR ? = 0)
                JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID
                JOIN m_customer ca ON L_SoM_CustomerID = ca.M_CustomerID
                JOIN m_customerlevel ON ca.M_CustomerM_CustomerLevelID = M_CustomerLevelID
                LEFT JOIN m_customer cb ON L_SoDSM_CustomerID = cb.M_CustomerID
                LEFT JOIN s_user ON W_CourierS_UserID = S_UserID
                WHERE W_CourierIsActive = 'Y'
                AND (ca.M_CustomerName LIKE ? OR L_SoNumber LIKE ?)
                AND W_CourierDate BETWEEN ? AND ?
                AND (
                    (W_CourierProcessing = 'N' AND ? = 'W') OR
                    (W_CourierProcessing = 'Y' AND W_CourierSent = 'N' AND ? = 'P') OR
                    (W_CourierSent = 'Y' AND (W_CourierReceiptNo IS NULL OR W_CourierReceiptNo = '') AND ? = 'C') OR 
                    (W_CourierSent = 'Y' AND W_CourierReceiptNo IS NOT NULL AND W_CourierReceiptNo <> '' AND ? = 'S') OR (? = 'A')
                    )
                ORDER BY W_CourierDate DESC
                LIMIT {$limit} OFFSET {$offset}", [$d['expedition_id'], $d['expedition_id'], $d['expedition_id'], $d['search'], $d['search'], $d['sdate'], $d['edate'], 
                        $d['status'], $d['status'], $d['status'], $d['status'], $d['status']]);
        if ($r)
        { 
            $r = $r->result_array(); 
            foreach ($r as $k => $v)
            {
                $x = $this->db->query("SELECT fn_so_get_items(?) x", [$v['L_SoID']])->row();
                
                $x = json_decode($x->x);
                $y = [];
                $r[$k]['items'] = $y;
                if ($x)
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
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            JOIN l_so ON W_CourierL_SoID = L_SoID
            JOIN m_expedition ON L_SoM_ExpeditionID = M_ExpeditionID
                AND ((M_ExpeditionID = ? AND ? <> 0) OR ? = 0)
            JOIN m_customer ON L_SoM_CustomerID = M_CustomerID
            WHERE W_CourierIsActive = 'Y'
                AND (M_CustomerName LIKE ? OR L_SoNumber LIKE ?)
                AND W_CourierDate BETWEEN ? AND ?
                AND (
                    (W_CourierProcessing = 'N' AND ? = 'W') OR
                    (W_CourierProcessing = 'Y' AND W_CourierSent = 'N' AND ? = 'P') OR
                    (W_CourierSent = 'Y' AND (W_CourierReceiptNo IS NULL OR W_CourierReceiptNo = '') AND ? = 'C') OR 
                    (W_CourierSent = 'Y' AND W_CourierReceiptNo IS NOT NULL AND W_CourierReceiptNo <> '' AND ? = 'S') OR (? = 'A')
                    )", [$d['expedition_id'], $d['expedition_id'], $d['expedition_id'], $d['search'], $d['search'], $d['sdate'], $d['edate'], 
                        $d['status'], $d['status'], $d['status'], $d['status'], $d['status']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    function process ($d)
    {
        $r = $this->db->query("CALL sp_wh_processing(?,  ?)", [$d['warehouse_id'], $d['uid']])
                    ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        return $r;
    }

    function send ($d)
    {
        $jdata = json_encode([
            'courier_name' => $d['courier_name'],
            'courier_note' => $d['courier_note'],
            'receipt_no' => $d['receipt_no']
        ]);
        $r = $this->db->query("CALL sp_wh_send(?, ?, ?)", [$d['warehouse_id'], $jdata, $d['uid']])
                    ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        return $r;
    }

    function receipt ($d)
    {
        $jdata = json_encode([
            'courier_note' => $d['courier_note'],
            'receipt_no' => $d['receipt_no']
        ]);
        $r = $this->db->query("CALL sp_wh_receipt(?, ?, ?)", [$d['warehouse_id'], $jdata, $d['uid']])
                    ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        return $r;
    }

    function get_one ($id)
    {
        $r = $this->db->where($this->table_key, $id)
                        ->get($this->table_name)
                        ->row();
        if ($r)
            return $r;
        return false;
    }
}

?>