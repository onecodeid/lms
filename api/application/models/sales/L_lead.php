<?php

class L_Lead extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = 'l_lead';
        $this->table_key = 'L_LeadID';
    }

    function save($inp)
    {

        $r = $this->db->query("CALL sp_lead_save(?, ?, ?, ?)", [$inp['lead_id'], json_encode($inp), json_encode($inp['json_data']), $inp['uid']])
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
            'lead_note' => isset($inp['lead_note']) ? $inp['lead_note'] : '',
            'payment_id' => $inp['payment_id'],
            'channel' => isset($inp['channel']) ? $inp['channel'] : '',
            'is_dropship' => $inp['is_dropship'],
            'ds_customer_id' => $inp['ds_customer_id'],
            'coupon_code' => $inp['coupon_code'],
            'coupon_type' => $inp['coupon_type'],
            'coupon_amount' => $inp['coupon_amount'],
            'coupon_amount_percent' => isset($inp['coupon_amount_percent'])?$inp['coupon_amount_percent']:0,
            'coupon_id' => $inp['coupon_id'],
            'coupon_note' => $inp['coupon_note']
        ]);

        $cdata = json_encode([
            'cust_id' => $inp['cust_id'],
            'cust_name' => $inp['cust_name'],
            'cust_address' => $inp['cust_address'],
            'cust_postcode' => $inp['cust_postcode'],
            'cust_phone' => $inp['cust_phone'],
            'cust_kelurahan_id' => isset($inp['cust_kelurahan_id']) ? $inp['cust_kelurahan_id'] : 0
        ]);

        $r = $this->db->query("CALL sp_so_qo_save(?, ?, ?, ?, ?)", [$inp['lead_id'], $cdata, $hdata, json_encode($inp['json_data']), $uid])
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

        $r = $this->db->query(
                "SELECT L_LeadID lead_id, L_LeadNumber lead_number, L_LeadDate lead_date, L_LeadM_CustomerID lead_customer,
                    IFNULL(M_CustomerName, L_LeadName) lead_name, L_LeadAddress lead_address, L_LeadPhone lead_phone,
                    M_LeadSourceID leadsource_id, M_LeadSourceName leadsource_name,
                    M_LeadTypeID leadtype_id, 
                    M_LeadTypeName leadtype_name,
                    IFNULL(L_LeadAdsNumber, '') lead_adsnumber,

                    L_LeadGreetingM_LeadAttrID lead_greeting,
                    L_LeadPrecloseM_LeadAttrID lead_preclose,
                    IFNULL(L_LeadProblems, '[]') lead_problem,

                    L_LeadClose lead_close,

                    IFNULL(kb.M_KelurahanID, IFNULL(ka.M_KelurahanID, 0)) kelurahan_id,
                    IFNULL(kb.M_KelurahanName, IFNULL(ka.M_KelurahanName, '')) kelurahan_name,
                    IFNULL(db.M_DistrictID, IFNULL(da.M_DistrictID, 0)) district_id,
                    IFNULL(db.M_DistrictName, IFNULL(da.M_DistrictName, '')) district_name,
                    IFNULL(cb.M_CityID, IFNULL(ca.M_CityID, 0)) city_id,
                    IFNULL(cb.M_CityName, IFNULL(ca.M_CityName, '')) city_name,
                    IFNULL(pb.M_ProvinceID, IFNULL(pa.M_ProvinceID, 0)) province_id,
                    IFNULL(pb.M_ProvinceID, IFNULL(pa.M_ProvinceName, '')) province_name,

                    -- CONCAT('[', 
                    --     GROUP_CONCAT(JSON_OBJECT('item_id', IFNULL(M_ItemID, 0), 'item_name', IFNULL(L_LeadDetailM_ItemName, ''),
                    --         'item_price', IFNULL(L_LeadDetailM_ItemPrice, 0), 'is_packet', IFNULL(L_LeadDetailIsPacket, 'N') )), 
                    --     ']') details,

                    CONCAT('[', 
                        GROUP_CONCAT(JSON_OBJECT('item_id', CAST( IFNULL(M_ItemID, M_PacketID) as VARCHAR(32)), 'item_name', IFNULL(M_ItemName, M_PacketName),
                            'item_qty', 1, 'item_disc', 0, 'item_discrp', 0, 'item_subtotal', 0, 'item_weight', 0,
                            'item_price', IFNULL(L_LeadDetailM_ItemPrice, 0), 'is_packet', IFNULL(L_LeadDetailIsPacket, 'N') )), 
                        ']') details,

                    IFNULL(L_SoID, 0) sales_id,
                    IFNULL(L_SoNumber, '') sales_number,
                    IF(L_SoDate IS NULL, '', date(L_SoDate)) sales_date

                FROM `{$this->table_name}`
                JOIN m_leadsource ON L_LEadM_LeadSourceID = M_LeadSourceID
                JOIN m_leadtype ON L_LeadM_LeadTypeID = M_LeadTypeID

                LEFT JOIN m_kelurahan ka ON ka.M_KelurahanID = L_LeadM_KelurahanID
                LEFT JOIN m_district da ON da.M_DistrictID = L_LeadM_DistrictID
                LEFT JOIN m_city ca ON ca.M_CityID = L_LeadM_CityID
                LEFT JOIN m_province pa ON ca.M_CityM_ProvinceID = pa.M_ProvinceID

                LEFT JOIN s_user ON L_LeadUserID = S_UserID
                LEFT JOIN s_userprofile ON S_UserProfileS_UserID = S_UserID AND S_UserProfileIsActive = 'Y'

                LEFT JOIN l_leaddetail ON L_LeadDetailL_LeadID = L_LEadID
                LEFT JOIN m_item ON L_LeadDetailM_ItemID = M_ItemID AND L_LeadDetailIsPacket = 'N'
                LEFT JOIN m_packet ON L_LeadDetailM_PacketID = M_PacketID AND L_LeadDetailIsPacket = 'Y'

                LEFT JOIN l_so ON L_LeadL_SoID = L_SoID

                LEFT JOIN m_customer ON L_LeadM_CustomerID = M_CustomerID
                LEFT JOIN m_kelurahan kb ON kb.M_KelurahanID = M_CustomerM_KelurahanID
                LEFT JOIN m_district db ON db.M_DistrictID = kb.M_KelurahanM_DistrictID
                LEFT JOIN m_city cb ON cb.M_CityID = db.M_DistrictM_CityID
                LEFT JOIN m_province pb ON cb.M_CityM_ProvinceID = pb.M_ProvinceID

                WHERE (`L_LeadNumber` LIKE ? OR `L_LeadName` LIKE ? OR L_LeadPhone LIKE ?)
                AND L_LeadDate BETWEEN ? AND ?
                AND (`L_LeadIsActive` = 'Y')
                AND ((L_LeadUserID = ? AND ? <> 'Z.GROUP.01' AND ? <> 'Z.GROUP.02') OR ? = 'Z.GROUP.01' OR ? = 'Z.GROUP.02')

                AND ((L_LeadClose = ? AND ? <> \"\") OR ? = \"\")
                AND ((L_LeadGreetingM_LeadAttrID = ? AND ? <> 0) OR ? = 0)
                AND ((FIND_IN_SET(L_LeadM_LeadSourceID, ?) AND ? <> \"\") OR ? = \"\")

                GROUP BY L_LeadID
                ORDER BY L_LeadNumber DESC
                LIMIT {$limit} OFFSET {$offset}", 
                    [$d['search'], $d['search'], $d['search'], $d['sdate'], $d['edate'], $d['user_id'], 
                    $d['group_code'], $d['group_code'], $d['group_code'], $d['group_code'],
                    $d['fclose'], $d['fclose'], $d['fclose'],
                    $d['fgreeting'], $d['fgreeting'], $d['fgreeting'],
                    $d['fsource'], $d['fsource'], $d['fsource']]);
        if ($r)
        {
            $r = $r->result_array();
            $this->load->model('sales/l_fu');
            $l['q'] = $this->db->last_query();
            foreach ($r as $k => $v)
            {
            //     $ct = $this->db->query("SELECT fn_address_city(?) x", [$v['M_CustomerM_KelurahanID']])->row()->x;
            //     $ct = json_decode($ct);
                $r[$k]['details'] = json_decode($v['details']);
                $r[$k]['lead_problem'] = json_decode($v['lead_problem']);
                $r[$k]['fus'] = $this->l_fu->search_by_lead($v['lead_id'])['records'];
            //     $r[$k]['city_id'] = $ct->city_id;
            //     $r[$k]['district_name'] = $ct->district_name;
            //     $r[$k]['district_id'] = $ct->district_id;

            //     if ($v['L_LeadIsDS'] == 'Y')
            //     {
            //         $ct = $this->db->query("SELECT fn_address_city(?) x", [$v['ds_customer_kelurahan_id']])->row()->x;
            //         $ct = json_decode($ct);
            //         $r[$k]['ds_city_name'] = $ct->city_name;
            //         $r[$k]['ds_city_id'] = $ct->city_id;
            //         $r[$k]['ds_district_name'] = $ct->district_name;
            //         $r[$k]['ds_district_id'] = $ct->district_id;
            //     }
            }
                
            $l['records'] = $r;
            
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            JOIN m_leadsource ON L_LEadM_LeadSourceID = M_LeadSourceID
            JOIN m_leadtype ON L_LeadM_LeadTypeID = M_LeadTypeID
            
            WHERE (`L_LeadNumber` LIKE ? OR `L_LeadName` LIKE ? OR L_LeadPhone LIKE ?)
                AND L_LeadDate BETWEEN ? AND ?
                AND (`L_LeadIsActive` = 'Y')
                AND ((L_LeadUserID = ? AND ? <> 'Z.GROUP.01' AND ? <> 'Z.GROUP.02') OR ? = 'Z.GROUP.01' OR ? = 'Z.GROUP.02')
                
                AND ((L_LeadClose = ? AND ? <> \"\") OR ? = \"\")
                AND ((L_LeadGreetingM_LeadAttrID = ? AND ? <> 0) OR ? = 0)
                AND ((FIND_IN_SET(L_LeadM_LeadSourceID, ?) AND ? <> \"\") OR ? = \"\")", 
                [$d['search'], $d['search'], $d['search'], $d['sdate'], $d['edate'], $d['user_id'], 
                $d['group_code'], $d['group_code'], $d['group_code'], $d['group_code'],
                $d['fclose'], $d['fclose'], $d['fclose'],
                $d['fgreeting'], $d['fgreeting'], $d['fgreeting'],
                $d['fsource'], $d['fsource'], $d['fsource']]);
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
                "SELECT L_LeadID, L_LeadNumber, L_LeadDate, L_LeadIsDS, L_LeadTotal
                FROM `{$this->table_name}`
                JOIN m_orderstatus ON L_LeadM_OrderStatusID = M_OrderStatusID
                    AND M_OrderStatusCode <> 'SO.NEW'
                    AND M_OrderStatusCode <> 'SO.DRAFT'
                    AND M_OrderStatusCode <> 'SO.Approved'
                WHERE (`L_LeadNumber` LIKE ?)
                AND L_LeadM_CustomerID = ?
                AND `L_LeadIsActive` = 'Y'
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
            JOIN l_lead ON L_InvoiceL_LeadID = L_LeadID
            JOIN m_customer ON L_LeadM_CustomerID = M_CustomerID  AND M_CustomerEmail IS NOT NuLL AND M_CustomerEmail <> ''
            WHERE L_InvoiceID = ?", [$id]);
        return true;
    }

    function get_one($id)
    {
        $r = $this->db->select('l_lead.*, m_customer.*, fn_so_get_items(L_LeadID) items, M_PaymentTypeCode payment_code', false)
                    ->join('m_customer', 'L_LeadM_CustomerID = M_CustomerID')
                    ->join('m_paymenttype', 'L_LeadM_PaymentID = M_PaymentTypeID')
                    ->where('L_LeadID', $id)
                    ->get($this->table_name);
        if ($r->num_rows() > 0)
        {
            $r = $r->row();
            $r->items = json_decode($r->items);
            return $r;
        }

        return false;
    }

    function convert($inp, $uid)
    {
        $r = $this->db->query("CALL sp_lead_convert(?, ?, ?, ?)", 
                                [$inp['lead_id'], $inp['customer_id'], $inp['customer_data'], $uid])
                    ->row();
        $this->clean_mysqli_connection($this->db->conn_id);
        return $r;
    }

    function get_for_sales ($id)
    {
        $r = $this->db->query("
            SELECT JSON_OBJECT(
                'M_CustomerID', M_CustomerID,
                'M_CustomerM_CustomerLevelID', M_CustomerM_CustomerLevelID,
                'M_CustomerCode', M_CustomerCode,
                'M_CustomerName', M_CustomerName,
                'M_CustomerAddress', M_CustomerAddress,
                'M_CustomerM_KelurahanID', M_CustomerM_KelurahanID,
                'M_CustomerPhone', M_CustomerPhone,
                'M_CustomerEmail', M_CustomerEmail,
                'M_CustomerPostCode', M_CustomerPostCode,
                'M_CustomerNote', M_CustomerNote,
                'M_CityName', M_CityName,
                'M_ProvinceName', M_ProvinceName,
                'kelurahan_id', kelurahan_id,
                'full_address', full_address,
                'M_ProvinceID', M_ProvinceID,
                'M_CityID', M_CityID,
                'M_DistrictID', M_DistrictID,
                'M_KelurahanID', M_KelurahanID,
                'M_CustomerLevelID', M_CustomerLevelID,
                'M_CustomerLevelName', M_CustomerLevelName
            ) as customer,

            CONCAT('[', GROUP_CONCAT(JSON_OBJECT('item_id', IF(L_LeadDetailIsPacket='Y',L_LeadDetailM_PacketID,L_LeadDetailM_ItemID), 
                'item_name', L_LeadDetailM_ItemName, 'item_qty', 1,
                'item_price', L_LeadDetailM_ItemPrice, 'item_disc', 0, 'item_discrp', 0,
                'item_subtotal', L_LeadDetailM_ItemPrice,
                'item_weight', IF(L_LeadDetailIsPacket='Y', IFNULL(M_PacketTotalWeight, 0), IFNULL(M_ItemWeight, 0)),
                'coupon_id', 0, 'coupon_amount', 0, 'is_packet', L_LeadDetailIsPacket)), ']') as items

            FROM l_lead
            JOIN l_leaddetail ON L_LeadDetailL_LeadID = L_LEadID
                LEFT JOIN m_item ON L_LeadDetailM_ItemID = M_ItemID AND L_LeadDetailIsPacket = 'N'
                LEFT JOIN m_packet ON L_LeadDetailM_PacketID = M_PacketID AND L_LeadDetailIsPacket = 'U'
            JOIN m_customer ON L_LeadM_CustomerID = M_CustomerID
            JOIN m_customerlevel ON M_CustomerM_CustomerLevelID = M_CustomerLevelID
            JOIN m_kelurahan ON M_KelurahanID = M_CustomerM_KelurahanID
            JOIN m_district ON M_KelurahanM_DistrictID = M_DistrictID
            JOIN m_city ON M_DistrictM_CityID = M_CityID
            JOIN m_province ON M_CityM_ProvinceID = M_ProvinceID
            JOIN v_cities ON kelurahan_id = M_KelurahanID
            WHERE L_LeadID = ?
            GROUP BY L_LeadID
        ", [$id])->row();

        if ($r)
        {
            $r->customer = json_decode($r->customer);
            $r->items = json_decode($r->items);

            $q = $this->db->query("SELECT fn_address_kelurahan(?) as x", [$r->customer->M_CustomerM_KelurahanID])->row();
            $r->customer->address_kelurahan = $q->x;

            $q = $this->db->query("SELECT fn_master_customer_note(?) as x", [$r->customer->M_CustomerID])->row();
            $r->customer->customer_note = json_decode($q->x);
        }

        return $r;
    }

    function wa_send($d)
    {
        /**
         * INSERT INTO LOG */      
        $logdb = $this->load->database('log', true);
        $logdb->trans_start();

        $q = "INSERT INTO crmlog_wa(
                CrmLog_WaRefCode, CrmLog_WaRefID,
                CrmLog_WaContent)
                VALUES(?,?,?)
        ";  
        $logdb->query($q, [$d['ref_code'], $d['ref_id'], $d['content']]);

        $id = $logdb->insert_id();
        $logdb->trans_complete();

        if ($logdb->trans_status() === FALSE) 
        {
            # Something went wrong.
            $logdb->trans_rollback();
            return FALSE;
        } 
        else 
        {
            # Everything is Perfect. 
            # Committing data to the database.
            $logdb->trans_commit();
            return $id;
        }
    }
}
?>