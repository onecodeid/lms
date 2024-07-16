<?php

class Crm_postsales extends MY_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function search($d)
    {
        $limit = isset($d['limit']) ? $d['limit'] : 10;
        $offset = ($d['page'] - 1) * $limit;
        $rst = ['records'=>[], 'total'=>0, 'total_page'=>1];
        $sdate = date("Y-m-d 00:00:00", strtotime($d['start_date']));
        $edate = date("Y-m-d 23:59:59", strtotime($d['end_date']));
        
        $customer_status = $d['customer_status'] == 'A' ? "" : "AND M_CustomerIsNew = '{$d['customer_status']}'";
        $sales_status = $d['sales_status'];
        
        // Query Items and Packets
        $qproducts = [];
        $item_ids = [];
        if (isset($this->sys_input['items'])) {
            if (!empty($this->sys_input['items']))
                $item_ids = array_merge($item_ids, explode(",", $this->sys_input['items']));
        }

        $packet_ids = [];
        if (isset($this->sys_input['packets'])) {
            if (!empty($this->sys_input['packets']))
                $packet_ids = array_merge($packet_ids, explode(",", $this->sys_input['packets']));
        }

        if (sizeof($item_ids) > 0) $qproducts[] = "(L_SoDetailM_ItemID IN (".implode(",", $item_ids).") AND L_SoDetailIsPacket = 'N')";
        if (sizeof($packet_ids) > 0) $qproducts[] = "(L_SoDetailM_PacketID IN (".implode(",", $packet_ids).") AND L_SoDetailIsPacket = 'Y')";
        $qproduct = sizeof($qproducts) > 0 ? "AND (" . implode(" OR ", $qproducts) . ")" : "";
        // End of Query Items and Packets

        // Query Areas
        $qprovince = "";
        $qcity = "";
        if (isset($this->sys_input['provinces'])) {
            if (!empty($this->sys_input['provinces'])) {
                $qprovince = "AND M_ProvinceID IN (" . implode(",", $this->sys_input['provinces']) . ")";
            }
        }

        if (isset($this->sys_input['cities'])) {
            if (!empty($this->sys_input['cities'])) {
                $qprovince = "AND M_CityID IN (" . implode(",", $this->sys_input['cities']) . ")";
            }
        }
        // End of Query Areas

        // Query Expeditions
        $qexpedition = "";
        $qservice = "";
        if (isset($this->sys_input['expeditions'])) {
            if (!empty($this->sys_input['expeditions'])) {
                $qexpedition = "AND L_SoM_ExpeditionID IN (" . implode(",", $this->sys_input['expeditions']) . ")";
            }
        }

        if (isset($this->sys_input['services'])) {
            if (!empty($this->sys_input['services'])) {
                $qservice = "AND FIND_IN_SET(L_SoExpService, '" . implode(",", $this->sys_input['services']) . "')";
            }
        }
        // End of Query Expeditions

        $q = "SELECT *,
                CONCAT('[',
                GROUP_CONCAT(
                    JSON_OBJECT('so_id', so_id, 'so_number', so_number, 'so_date', so_date, 
                        'so_ds', so_isds, 'so_unique_qty', so_unique_qty, 'so_qty', so_qty,
                        'so_expedition', so_expedition, 'so_service', so_service, 'so_total', so_total,
                        'so_sub_weight', so_sub_weight, 'so_add_weight', so_add_weight, 'so_total_weight', so_total_weight,
                        'so_iscod', so_iscod, 'so_cod_cost', so_cod_cost, 'so_coupon_amount', so_coupon_amount,
                        'so_courier_cost', so_courier_cost,
                        'items', items)
                ), ']') details

                FROM (
                SELECT 
                L_SoID so_id, L_SoNumber so_number, date(L_SoDate) so_date, L_SoIsDS so_isds, L_SoTotalUniqueQty so_unique_qty,
                L_SoTotalQty so_qty, M_ExpeditionName so_expedition, L_SoExpService so_service, L_SoTotal so_total,
                L_SoSubTotalWeight so_sub_weight, L_SoAddWeight so_add_weight, L_SoTotalWeight so_total_weight,
                L_SoIsCOD so_iscod, L_SoCouponAmount so_coupon_amount, L_SoCODCost so_cod_cost,
                L_SoExpCost so_courier_cost,

                a.M_CustomerID customer_id,
                a.M_CustomerName customer_name,
                a.M_CustomerAddress customer_address,
                a.M_CustomerPhone customer_phone,
                a.M_CustomerM_KelurahanID customer_kelurahan_id,
                M_OrderStatusID orderstatus_id, M_OrderStatusCode orderstatus_code, 
                M_OrderStatusName orderstatus_name,
                IFNULL(b.M_CustomerName, '') ds_customer_name,
                IFNULL(b.M_CustomerAddress, '') ds_customer_address,
                IFNULL(b.M_CustomerM_KelurahanID, 0) ds_customer_kelurahan_id,
                IFNULL(b.M_CustomerPhone, '') ds_customer_phone,
                IFNULL(M_CustomerLevelName, '') level_name,
                M_ExpeditionName expedition_name,
                L_SoExpService `service_name`,

                CONCAT('[',
                GROUP_CONCAT(
                    JSON_OBJECT('item_id', IFNULL(M_ItemID, M_PacketID), 'item_name', IFNULL(M_ItemName, M_PacketName),
                        'item_qty', L_SoDetailQty, 'item_app_qty', L_SoDetailApprovedQty,
                        'item_price', L_SoDetailPrice, 'item_disc', L_SoDetailDiscTotal,
                        'item_subtotal', L_SoDetailSubTotal,
                        'sod_id', L_SoDetailID, 'sod_approved', L_SoDetailApproved)
                SEPARATOR ','), ']') items,

                IFNULL(	S_UserProfileFullName, '') admin_name,
                IFNULL(S_UserProfilePhone, '') admin_phone,
                IFNULL(date(Crm_WaLastDate), '') walast_date

            FROM l_so
            JOIN l_sodetail ON L_SoDetailL_SoID = L_SoID AND L_SoDetailIsActive = 'Y'
                {$qproduct}
            LEFT JOIN m_item ON L_SoDetailM_ItemID = M_ItemID AND L_SoDetailIsPacket = 'N'
            LEFT JOIN m_packet ON L_SoDetailM_PacketID = M_PacketID AND L_SoDetailIsPacket = 'Y'

            JOIN m_customer a ON L_SoM_CustomerID = a.M_CustomerID {$customer_status}
            JOIN m_customerlevel ON a.M_CustomerM_CustomerLevelID = M_CustomerLevelID
            JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID
            JOIN m_expedition ON L_SoM_ExpeditionID = M_ExpeditionID

            JOIN m_kelurahan ON a.M_CustomerM_KelurahanID = M_KelurahanID
            JOIN m_district ON M_KelurahanM_DistrictID = M_DistrictID
            JOIN m_city ON M_DistrictM_CityID = M_CityID
                {$qcity}
            JOIN m_province ON M_CityM_ProvinceID = M_ProvinceID
                {$qprovince}

            LEFT JOIN crm_walast ON Crm_WaLastM_CustomerID = a.M_CustomerID AND Crm_WaLastIsActive = 'Y'
            LEFT JOIN m_customer b ON L_SoDSM_CustomerID = b.M_CustomerID
 --           LEFT JOIN l_coupon ON L_CouponL_SoID = L_SoID ANd L_CouponIsActive = 'Y'

            LEFT JOIN s_userprofile ON S_UserProfileS_UserID = ? AND S_UserProfileIsActive = 'Y'

            WHERE L_SoIsActive = 'Y'
            {$qexpedition} {$qservice}
            AND L_SoDate BETWEEN ? AND ?
            AND ((FIND_IN_SET(L_SoM_OrderStatusID, ?) AND ? <> 0) OR ? = 0)
            GROUP BY L_SoID) x
            
            GROUP BY customer_id
            ORDER BY customer_name ASC
            LIMIT {$limit} OFFSET {$offset}";
        
        $r = $this->db->query($q, [$this->sys_user['user_id'], $sdate, $edate, $sales_status, $sales_status, $sales_status]);
        // $lq = $this->db->last_query();
        if ($r)
        {
            $r = $r->result_array();
            foreach ($r as $k => $v)
            {
                $ct = $this->db->query("SELECT fn_address_city(?) x", [$v['customer_kelurahan_id']])->row()->x;
                $ct = json_decode($ct);
                $r[$k]['city_name'] = $ct->city_name;
                $r[$k]['city_id'] = $ct->city_id;
                $r[$k]['district_name'] = $ct->district_name;
                $r[$k]['district_id'] = $ct->district_id;

                $details = json_decode($v['details']);
                foreach ($details as $l => $w)
                {
                    $details[$l]->items = json_decode($w->items);
                }
                $r[$k]['details'] = $details;
            }
                
            $rst['records'] = $r;
            // $rst['query'] = $lq;
        }

        $q = "SELECT COUNT(DISTINCT M_CustomerID) n
            FROM l_so
            JOIN l_sodetail ON L_SoDetailL_SoID = L_SoID AND L_SoDetailIsActive = 'Y'
                {$qproduct}

            JOIN m_customer a ON L_SoM_CustomerID = a.M_CustomerID {$customer_status}
            JOIN m_customerlevel ON a.M_CustomerM_CustomerLevelID = M_CustomerLevelID
            JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID
            JOIN m_expedition ON L_SoM_ExpeditionID = M_ExpeditionID

            JOIN m_kelurahan ON a.M_CustomerM_KelurahanID = M_KelurahanID
            JOIN m_district ON M_KelurahanM_DistrictID = M_DistrictID
            JOIN m_city ON M_DistrictM_CityID = M_CityID
                {$qcity}
            JOIN m_province ON M_CityM_ProvinceID = M_ProvinceID
                {$qprovince}

            WHERE L_SoIsActive = 'Y'
            {$qexpedition} {$qservice}
            AND L_SoDate BETWEEN ? AND ?
            AND ((FIND_IN_SET(L_SoM_OrderStatusID, ?) AND ? <> 0) OR ? = 0)
            ";
        
        $r = $this->db->query($q, [$sdate, $edate, $sales_status, $sales_status, $sales_status]);
        if ($r)
        {       
            $r = $r->row();
            $rst['total'] = $r->n;
            $rst['total_page'] = ceil($r->n / $limit);
        }

        return $rst;
    }

    function save_template ( $data, $id = 0) {
        $tb_name = "crm_postsales_filter";

        $this->db
                ->set("Crm_PostSalesFilterJson", $data['json']);

        if ($id == 0) {
            $this->db->set("Crm_PostSalesFilterName", $data['name'])
                    ->set("Crm_PostSalesFilterUserID", $data['user_id'])
                    ->insert( $tb_name );

            $id = $this->db->insert_id();
        } else {
            $this->db->where("Crm_PostSalesFilterID", $id)
                    ->update( $tb_name );
        }
        
        return $id;
    }

    function search_profile( $d )
    {
        $limit = isset($d['limit']) ? $d['limit'] : 10;
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $r = $this->db->query(
                "SELECT Crm_PostSalesFilterID profile_id, Crm_PostSalesFilterName profile_name, 
                    Crm_PostSalesFilterJson profile_json
                FROM `crm_postsales_filter`
                WHERE `Crm_PostSalesFilterName` LIKE ?
                AND `Crm_PostSalesFilterIsActive` = 'Y'
                LIMIT {$limit} OFFSET {$offset}", [$d['search']]);
        if ($r)
        {
            $l['records'] = $r->result_array();
        }

        $r = $this->db->query(
            "SELECT count(`Crm_PostSalesFilterID`) n
            FROM `crm_postsales_filter`
            WHERE `Crm_PostSalesFilterName` LIKE ?
            AND `Crm_PostSalesFilterIsActive` = 'Y'", [$d['search']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    function delete_profile ($data)
    {
        $this->db->set('Crm_PostSalesFilterIsActive', 'N')
                ->where('Crm_PostSalesFilterID', $data['id'])
                ->update('crm_postsales_filter');

        return true;
    }
}

?>