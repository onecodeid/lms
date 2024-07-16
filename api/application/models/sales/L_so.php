<?php

class L_So extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = 'l_so';
        $this->table_key = 'L_SoID';
    }

    function save($inp)
    {
        $hdata = json_encode([
            'expedition_id' => $inp['expedition_id'],
            'service' => isset($inp['service']) ? $inp['service'] : '',
            'courier_cost' => $inp['courier_cost'],
            'ex_other' => isset($inp['ex_other']) ? $inp['ex_other'] : '',
            'ex_note' => isset($inp['ex_note']) ? $inp['ex_note'] : '',
            'order_note' => isset($inp['order_note']) ? $inp['order_note'] : '',
            'payment_id' => $inp['payment_id'],
            'channel' => isset($inp['channel']) ? $inp['channel'] : '',
            'is_dropship' => 'N', //$inp['is_dropship'],
            'ds_customer_id' => 0, //$inp['ds_customer_id'],
            'coupon_code' => '', //$inp['coupon_code'],
            'coupon_type' => '', //$inp['coupon_type'],
            'coupon_amount' => 0, //$inp['coupon_amount'],
            'coupon_id' => 0, //$inp['coupon_id'],
            'coupon_note' => '', //$inp['coupon_note'],
            'lead_id' => $inp['lead_id'],
            'leadsource' => isset($inp['leadsource'])?$inp['leadsource']:null,
            'sales_ads' => isset($inp['sales_ads'])?$inp['sales_ads']:'',
            'sales_mp' => isset($inp['sales_mp'])?$inp['sales_mp']:'',
        ]);

        $r = $this->db->query("CALL sp_so_save(?, ?, ?, ?, 'N')", [$inp['order_id'], $inp['customer_id'], $hdata, json_encode($inp['json_data'])])
                    ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        $r->msg = $this->db->last_query();
        return $r;
    }

    function save_qo($inp, $uid)
    {
        $hdata = json_encode([
            'expedition_id' => $inp['expedition_id'],
            'service' => isset($inp['service']) ? $inp['service'] : '',
            'courier_cost' => $inp['courier_cost'],
            'ex_other' => isset($inp['ex_other']) ? $inp['ex_other'] : '',
            'ex_note' => isset($inp['ex_note']) ? $inp['ex_note'] : '',
            'order_note' => isset($inp['order_note']) ? $inp['order_note'] : '',
            'payment_id' => $inp['payment_id'],
            'channel' => isset($inp['channel']) ? $inp['channel'] : '',
            'is_dropship' => 'N', //$inp['is_dropship'],
            'ds_customer_id' => 0, //$inp['ds_customer_id'],
            'coupon_code' => '', //$inp['coupon_code'],
            'coupon_type' => '', //$inp['coupon_type'],
            'coupon_amount' => 0, //$inp['coupon_amount'],
            'coupon_amount_percent' => 0, //isset($inp['coupon_amount_percent'])?$inp['coupon_amount_percent']:0,
            'coupon_id' => 0, //$inp['coupon_id'],
            'coupon_note' => '', //$inp['coupon_note'],
            'leadsource' => isset($inp['leadsource'])?$inp['leadsource']:null,
            'sales_ads' => isset($inp['sales_ads'])?$inp['sales_ads']:'',
            'sales_mp' => isset($inp['sales_mp'])?$inp['sales_mp']:'',
        ]);

        $cdata = json_encode([
            'cust_id' => $inp['cust_id'],
            'cust_name' => $inp['cust_name'],
            'cust_address' => $inp['cust_address'],
            'cust_postcode' => $inp['cust_postcode'],
            'cust_phone' => $inp['cust_phone'],
            'cust_kelurahan_id' => isset($inp['cust_kelurahan_id']) ? $inp['cust_kelurahan_id'] : 0
        ]);

        $r = $this->db->query("CALL sp_so_qo_save(?, ?, ?, ?, ?)", [$inp['order_id'], $cdata, $hdata, json_encode($inp['json_data']), $uid])
                    ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        $r->msg = $this->db->last_query();
        return $r;
    }

    function search( $d )
    {
        $limit = $d['limit'];
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

//         is_packet:"N"
// item_disc:0
// item_discrp:"0"
// item_id:"19"
// item_name:"Lippiematte Intense Lip Cream Nudetella"
// item_price:"107000"
// item_qty:1
// item_subtotal:"107000"
// item_weight:"40"

        $r = $this->db->query(
                "SELECT L_SoID, L_SoNumber, L_SoDate, L_SoIsDS, L_SoTotalUniqueQty, L_SoTotalQty,
                    L_SoSubTotalWeight, L_SoAddWeight, L_SoTotalWeight, L_SoM_ExpeditionID,
                    L_SoExpService, L_SoExpCost, L_SoTotal, L_SoApproved, L_SoCouponAmount,
                    IFNULL(L_CouponCode, '') coupon_code, IFNULL(L_CouponType, '') coupon_type,
                    IFNULL(L_CouponM_CouponID, 0) coupon_id, 0 coupon_courier_id, '[]' coupon_item_id,
                    L_SoIsCOD, L_SoCODCost, L_SoCODItemOnly cod_item_only, L_SoExpNote exp_note, L_SoExpOther exp_other,
                    IFNULL(L_SoAdsNumber, '') so_ads_number, IFNULL(L_SoMpNumber, '') so_mp_number,
                    a.M_CustomerM_KelurahanID M_CustomerM_KelurahanID,
                    a.M_CustomerName M_CustomerName,
                    a.M_CustomerAddress M_CustomerAddress,
                    a.M_CustomerPhone M_CustomerPhone,
                    M_OrderStatusID, M_OrderStatusCode, M_OrderStatusName,
                    b.M_CustomerName ds_customer_name,
                    b.M_CustomerAddress ds_customer_address,
                    b.M_CustomerM_KelurahanID ds_customer_kelurahan_id,
                    b.M_CustomerPhone ds_customer_phone,
                    IFNULL(L_InvoiceID, 0) L_InvoiceID,
                    M_PaymentTypeID payment_id,
                    M_PaymentTypeCode payment_code,
                    IFNULL(F_IpaymuVia, '') ipaymu_via,
                    IFNULL(F_IpaymuChannel, '') ipaymu_channel,
                    IFNULL(F_IpaymuTrxCode, '') ipaymu_trx_code,
                    IFNULL(F_IpaymuNote, '') ipaymu_note,
                    IFNULL(S_UserProfileFullName, '') referrer_name,
                    IFNULL(M_CustomerLevelName, '') level_name,
                    IFNULL(M_LeadSourceName, '') leadsource_name,
                    IFNULL(M_LeadSourceColor, 'grey') leadsource_color
                FROM `{$this->table_name}`
                JOIN m_customer a ON L_SoM_CustomerID = a.M_CustomerID
                LEFT JOIN m_customerlevel ON a.M_CustomerM_CustomerLevelID = M_CustomerLevelID
                JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID
                LEFT JOIN m_expedition ON L_SoM_ExpeditionID = M_ExpeditionID
                LEFT JOIN m_kelurahan ON M_KelurahanID = M_CustomerM_KelurahanID
                LEFT JOIN m_customer b ON L_SoDSM_CustomerID = b.M_CustomerID
                LEFT JOIN l_invoice ON L_InvoiceL_SoID = L_SoID AND L_InvoiceIsActive = 'Y'
                LEFT JOIN m_paymenttype ON L_SoM_PaymentID = M_PaymentTypeID
                LEFT JOIN f_ipaymu ON L_SoID = F_IpaymuL_SoID AND F_IpaymuIsActive = 'Y'
                LEFT JOIN s_user ON L_SoUserID = S_UserID
                LEFT JOIN s_userprofile ON S_UserProfileS_UserID = S_UserID AND S_UserProfileIsActive = 'Y'
                LEFT JOIN l_coupon ON L_CouponL_SoID = L_SoID ANd L_CouponIsActive = 'Y'
                LEFT JOIN m_leadsource on L_SoM_LeadSourceID = M_LeadSourceID
                WHERE (`L_SoNumber` LIKE ? OR a.`M_CustomerName` LIKE ? OR a.M_CustomerPhone LIKE ?)
                AND L_SoDate BETWEEN ? AND ?
                AND (`L_SoIsActive` = 'Y' OR `L_SoIsActive` = 'X')
                AND ((L_SoUserID = ? AND ? <> 'Z.GROUP.01' AND ? <> 'Z.GROUP.02') OR ? = 'Z.GROUP.01' OR ? = 'Z.GROUP.02')
                
                ORDER BY L_SoNumber DESC
                LIMIT {$limit} OFFSET {$offset}", 
                    [$d['search'], $d['search'], $d['search'], $d['sdate'], $d['edate'], $d['user_id'], 
                    $d['group_code'], $d['group_code'], $d['group_code'], $d['group_code']]);
        if ($r)
        {
            $r = $r->result_array();
            foreach ($r as $k => $v)
            {
                $ct = $this->db->query("SELECT fn_address_city(?) x", [$v['M_CustomerM_KelurahanID']])->row()->x;
                $ct = json_decode($ct);
                $r[$k]['city_name'] = $ct->city_name;
                $r[$k]['city_id'] = $ct->city_id;
                $r[$k]['district_name'] = $ct->district_name;
                $r[$k]['district_id'] = $ct->district_id;

                if ($v['L_SoIsDS'] == 'Y')
                {
                    $ct = $this->db->query("SELECT fn_address_city(?) x", [$v['ds_customer_kelurahan_id']])->row()->x;
                    $ct = json_decode($ct);
                    $r[$k]['ds_city_name'] = $ct->city_name;
                    $r[$k]['ds_city_id'] = $ct->city_id;
                    $r[$k]['ds_district_name'] = $ct->district_name;
                    $r[$k]['ds_district_id'] = $ct->district_id;
                }
            }
                
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            JOIN m_customer ON L_SoM_CustomerID = M_CustomerID
            JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID
            WHERE (`L_SoNumber` LIKE ? OR `M_CustomerName` LIKE ? OR M_CustomerPhone LIKE ?)
            AND L_SoDate BETWEEN ? AND ?
                AND (`L_SoIsActive` = 'Y' OR `L_SoIsActive` = 'X')
            AND ((L_SoUserID = ? AND ? <> 'Z.GROUP.01' AND ? <> 'Z.GROUP.02') OR ? = 'Z.GROUP.01' OR ? = 'Z.GROUP.02')", 
                [$d['search'], $d['search'], $d['search'], $d['sdate'], $d['edate'], $d['user_id'], 
                $d['group_code'], $d['group_code'], $d['group_code'], $d['group_code']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    function approve($inp)
    {
        $r = $this->db->query("CALL sp_so_approve(?, ?, ?)", [json_encode($inp['header_data']), $inp['json_data'], $inp['uid']])
                    ->row();
        $this->clean_mysqli_connection($this->db->conn_id);
        return $r;
    }

    function search_top_by_customer($d)
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT L_SoID, L_SoNumber, L_SoDate, L_SoIsDS, L_SoTotal
                FROM `{$this->table_name}`
                JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID
                    AND M_OrderStatusCode <> 'SO.NEW'
                    AND M_OrderStatusCode <> 'SO.DRAFT'
                    AND M_OrderStatusCode <> 'SO.Approved'
                WHERE (`L_SoNumber` LIKE ?)
                AND L_SoM_CustomerID = ?
                AND `L_SoIsActive` = 'Y'
                LIMIT 10", [$d['search'], $d['customer_id']]);
        if ($r)
        {
            $r = $r->result_array();                
            $l['records'] = $r;
        }

        return $l;
    }

    function cancel($id)
    {
        $r = $this->db->query("CALL sp_so_cancel(?)", $id)
                        ->row();
        $this->clean_mysqli_connection($this->db->conn_id);
        return $r;
    }

    function send_email($id)
    {
        // SEND TO Email Schedule
	    $this->db->query("INSERT INTO s_email_schedule(S_EmailScheduleType, S_EmailScheduleAddress, S_EmailScheduleSubject, S_EmailScheduleReffID)
            SELECT 'INVOICE', M_CustomerEmail, CONCAT('Invoice Customer untuk ', M_CustomerName), L_InvoiceID
            FROM l_invoice 
            JOIN l_so ON L_InvoiceL_SoID = L_SoID
            JOIN m_customer ON L_SoM_CustomerID = M_CustomerID  AND M_CustomerEmail IS NOT NuLL AND M_CustomerEmail <> ''
            WHERE L_InvoiceID = ?", [$id]);

        return $this->db->select('L_SoID so_id, L_InvoiceID invoice_id, M_CustomerID customer_id, M_CustomerPhone customer_phone')
                    ->join('l_so', 'L_InvoiceL_SoID = L_SoID')
                    ->join('m_customer', 'L_SoM_CustomerID = M_CustomerID')
                    ->where('L_InvoiceID', $id)
                    ->get('l_invoice')->row();
        // return true;
    }

    function get_one($id)
    {
        $r = $this->db->select('l_so.*, m_customer.*, IFNULL(L_SoAdsNumber, "") so_ads_number, IFNULL(L_SoMpNumber, "") so_mp_number,
                        fn_so_get_items(L_SoID) items, M_PaymentTypeCode payment_code', false)
                    ->join('m_customer', 'L_SoM_CustomerID = M_CustomerID')
                    ->join('m_paymenttype', 'L_SoM_PaymentID = M_PaymentTypeID')
                    ->where('L_SoID', $id)
                    ->get($this->table_name);
        if ($r->num_rows() > 0)
        {
            $r = $r->row();
            $r->items = json_decode($r->items);
            return $r;
        }

        return false;
    }

    function save_mp($inp, $id)
    {
        // Start the transaction
        $this->db->trans_start();

        try {
            // Your database operations here
            $this->db->set("L_SoAdsNumber", $inp['so_ads_number'])
                    ->set("L_SoMpNumber", $inp['so_mp_number'])
                    ->set("L_SoMpCost", $inp['so_mp_cost'])
                    ->where("L_SoID", $id)
                    ->update("l_so");

            // If all operations are successful, commit the transaction
            $this->db->trans_complete();

            // Check if the transaction was successful
            if ($this->db->trans_status() === FALSE) {
                // Something went wrong, roll back the transaction
                $this->db->trans_rollback();
                return json_decode(json_encode(
                    ["status"=>"ERR", "message"=>"Something went wrong"]));
            } else {
                // All operations were successful, commit the transaction
                $this->db->trans_commit();
                return json_decode(json_encode(
                    ["status"=>"OK", "data"=>["so_id"=>$id] ]));
            }
        } catch (Exception $e) {
            // An exception occurred, roll back the transaction
            $this->db->trans_rollback();
            return json_decode(json_encode(
                ["status"=>"ERR", "message"=>'Exception: ' . $e->getMessage()]));
        }
    }
}
?>