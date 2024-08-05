<?php

class M_customer extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_customer";
        $this->table_key = "M_CustomerID";
    }

    function search( $d )
    {
        $limit = isset($d['limit']) ? $d['limit'] : 10;
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $r = $this->db->query(
                "SELECT m_customer.*, M_CityName, M_ProvinceName, kelurahan_id, full_address,
                M_ProvinceID, M_CityID, M_DistrictID, M_KelurahanID, M_CustomerLevelID,
                M_CustomerLevelCode, M_CustomerLevelName,
                IFNULL(S_UserCustomerID, 0) user_customer_id, S_UserCustomerUsername user_customer_username,
                M_CustomerPointQty point_qty, 
                IFNULL(M_LeadSourceName, '') leadsource_name, IFNULL(M_LeadSourceColor, '') leadsource_color,

                M_ItemName item_name

                FROM `{$this->table_name}`
                JOIN s_user ON S_UserID = ?
                JOIN s_usergroup ON S_UserS_UserGroupID = S_UserGroupID
                JOIN m_customerlevel ON M_CustomerM_CustomerLevelID = M_CustomerLevelID
                    AND ((M_CustomerLevelID = ? AND ? <> 0) OR (? = 0))
                JOIN m_customerpoint ON M_CustomerPointM_CustomerID = M_CustomerID

                JOIN l_so ON L_SoM_CustomerID = M_CustomerID AND L_SoIsActive = 'Y' AND L_SoDate >= '2024-07-01'
                JOIN l_sodetail ON L_SoDetailL_SoID = L_SoID AND L_SoISActive = 'Y'
                JOIN m_item ON L_SoDetailM_ITemID = M_ItemID
                    AND ((M_ItemID = ? AND ? <> 0) OR ? = 0)

                LEFT JOIN m_kelurahan ON M_CustomerM_KelurahanID = M_KelurahanID
                LEFT JOIN m_district ON M_KelurahanM_DistrictID = M_DistrictID
                LEFT JOIN m_city ON m_districtM_CityID = M_CityID
                LEFT JOIN m_province ON M_CityM_ProvinceID = M_ProvinceID
                LEFT JOIN v_cities ON M_CustomerM_KelurahanID = kelurahan_id
                LEFT JOIN s_usercustomer ON S_UserCustomerM_CustomerID = M_CustomerID AND S_UserCustomerIsActive = 'Y'
                LEFT JOIN m_leadsource ON M_CustomerLastM_LeadSourceID = M_LeadSourceID
                WHERE (`M_CustomerName` LIKE ? OR M_CustomerPhone LIKE ?)
                AND `M_CustomerIsActive` = 'Y'
                AND ((M_ProvinceID = ? AND ? <> 0) OR ? = 0)
                AND ((M_CityID = ? AND ? <> 0) OR ? = 0)
                AND ((M_CustomerUserID = ? AND S_UserGroupCode <> 'Z.GROUP.01' AND S_UserGroupCode <> 'Z.GROUP.02') OR S_UserGroupCode = 'Z.GROUP.01' OR S_UserGroupCode = 'Z.GROUP.02')
                AND M_CustomerJoinDate BETWEEN ? AND ?
                ORDER BY L_SoDate DESC, M_CustomerName ASC
                LIMIT {$limit} OFFSET {$offset}", [$d['user_id'], $d['level'], $d['level'], $d['level'], 
                                                    $d['item'], $d['item'], $d['item'],
                                                    $d['customer_name'], $d['customer_name'],
                                                    $d['province'], $d['province'], $d['province'], $d['city'], $d['city'], $d['city'], $d['user_id'], $d['sdate'], $d['edate']]);
        if ($r)
        {
            $r = $r->result_array();
            foreach ($r as $k => $v)
            {
                $q = $this->db->query("SELECT fn_address_kelurahan_district(?) as x", [$v['M_CustomerM_KelurahanID']])->row();
                $r[$k]['address_kelurahan'] = $q->x;

                $q = $this->db->query("SELECT fn_master_customer_referrer(?) as x", [$v['M_CustomerID']])->row();
                $r[$k]['referrer'] = json_decode($q->x);

                $q = $this->db->query("SELECT fn_master_customer_mp(?) as x", [$v['M_CustomerID']])->row();
                $r[$k]['customer_mps'] = json_decode($q->x);

                $q = $this->db->query("SELECT fn_master_customer_note(?) as x", [$v['M_CustomerID']])->row();
                $r[$k]['customer_note'] = json_decode($q->x);

                $r[$k]['customer_join_date_formatted'] = date('d/m/Y', strtotime($v['M_CustomerJoinDate']));
                $r[$k]['customer_end_date_formatted'] = date('d/m/Y', strtotime($v['M_CustomerEndDate']));
            }
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            JOIN s_user ON S_UserID = ?
                JOIN s_usergroup ON S_UserS_UserGroupID = S_UserGroupID
            
            JOIN m_customerpoint ON M_CustomerPointM_CustomerID = M_CustomerID

            JOIN l_so ON L_SoM_CustomerID = M_CustomerID AND L_SoIsActive = 'Y' AND L_SoDate >= '2024-07-01'
            JOIN l_sodetail ON L_SoDetailL_SoID = L_SoID AND L_SoISActive = 'Y'
            JOIN m_item ON L_SoDetailM_ITemID = M_ItemID
                AND ((M_ItemID = ? AND ? <> 0) OR ? = 0)

            JOIN m_customerlevel ON L_SoDetailM_CustomerLevelID = M_CustomerLevelID
                    AND ((M_CustomerLevelID = ? AND ? <> 0) OR (? = 0))

            LEFT JOIN m_kelurahan ON M_CustomerM_KelurahanID = M_KelurahanID
            LEFT JOIN m_district ON M_KelurahanM_DistrictID = M_DistrictID
            LEFT JOIN m_city ON m_districtM_CityID = M_CityID
            LEFT JOIN m_province ON M_CityM_ProvinceID = M_ProvinceID
            LEFT JOIN v_cities ON M_CustomerM_KelurahanID = kelurahan_id
            WHERE (`M_CustomerName` LIKE ? OR M_CustomerPhone LIKE ?)
            AND `M_CustomerIsActive` = 'Y'
            AND ((M_ProvinceID = ? AND ? <> 0) OR ? = 0)
                AND ((M_CityID = ? AND ? <> 0) OR ? = 0)
                AND ((M_CustomerUserID = ? AND S_UserGroupCode <> 'Z.GROUP.01' AND S_UserGroupCode <> 'Z.GROUP.02') OR S_UserGroupCode = 'Z.GROUP.01' OR S_UserGroupCode = 'Z.GROUP.02')
                AND M_CustomerJoinDate BETWEEN ? AND ?", 
                    [$d['user_id'], $d['level'], $d['level'], $d['level'], 
                        $d['item'], $d['item'], $d['item'],
                        $d['customer_name'], $d['customer_name'],
                        $d['province'], $d['province'], $d['province'], $d['city'], $d['city'], $d['city'], $d['user_id'], $d['sdate'], $d['edate']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    function save ( $d )
    {
        $r = $this->db->set('M_CustomerName', $d['customer_name'])
                    ->set('M_CustomerDob', isset($d['customer_dob'])?$d['customer_dob']:null)
                    ->set('M_CustomerSex', $d['customer_sex'])
                    ->set('M_CustomerAddress', $d['customer_address'])
                    ->set('M_CustomerPhone', $d['customer_phone'])
                    ->set('M_CustomerPhone2', $d['customer_phone2'])
                    ->set('M_CustomerPhone3', $d['customer_phone3'])
                    ->set('M_CustomerNote', $d['customer_note'])
                    ->set('M_CustomerEmail', $d['customer_email'])
                    ->set('M_CustomerPostCode', $d['customer_postcode'])
                    ->set('M_CustomerAutoAccept', $d['customer_auto'])
                    ->set('M_CustomerDuePayment', $d['customer_due_payment'])
                    ->set('M_CustomerM_CustomerLevelID', $d['customer_level_id'])
                    ->set('M_CustomerM_KelurahanID', $d['customer_kelurahan_id'])
                    ->set('M_CustomerJoinDate', isset($d['customer_join_date'])?date('Y-m-d', strtotime($d['customer_join_date'])):date('Y-m-d'))
                    ->set('M_CustomerEndDate', isset($d['customer_end_date'])?date('Y-m-d', strtotime($d['customer_end_date'])):date('Y-m-d'))
                    ->set('M_CustomerUserID', $d['user_id'])
                    ->insert( $this->table_name );
        $id = $this->db->insert_id();

        if ($r)
        {
            $x = null;

            $this->load->model('master/m_city');
            $ct = $this->m_city->get_from_kelurahan($d['customer_kelurahan_id']);

            if (isset($d['user_customer']))
            {
                if ($d['user_customer']=='Y')
                {
                    $this->load->model('system/s_usercustomer');
                    $x = $this->s_usercustomer->grant_user($id, $d['customer_name'], $ct->M_CityName, "", true, $d['customer_email']);
                }
            }

            // MP Accounts
            $this->db->query("CALL sp_master_customer_mp_save(?,?)", [$id, $d['customer_mps']]);
            $this->clean_mysqli_connection($this->db->conn_id);

            return ["status"=>"OK", "data"=>$id, "user"=>$x];
        }

        return ["status"=>"ERR"];
    }

    function edit ( $d )
    {
        $r = $this->db->set('M_CustomerName', $d['customer_name'])
                    ->set('M_CustomerDob', isset($d['customer_dob'])?$d['customer_dob']:null)
                    ->set('M_CustomerSex', $d['customer_sex'])
                    ->set('M_CustomerAddress', $d['customer_address'])
                    ->set('M_CustomerPhone', $d['customer_phone'])
                    ->set('M_CustomerPhone2', $d['customer_phone2'])
                    ->set('M_CustomerPhone3', $d['customer_phone3'])
                    ->set('M_CustomerEmail', $d['customer_email'])
                    ->set('M_CustomerPostCode', $d['customer_postcode'])
                    ->set('M_CustomerAutoAccept', $d['customer_auto'])
                    ->set('M_CustomerDuePayment', $d['customer_due_payment'])
                    ->set('M_CustomerNote', $d['customer_note'])
                    ->set('M_CustomerM_CustomerLevelID', $d['customer_level_id'])
                    ->set('M_CustomerM_KelurahanID', $d['customer_kelurahan_id'])
                    ->set('M_CustomerJoinDate', isset($d['customer_join_date'])?date('Y-m-d', strtotime($d['customer_join_date'])):date('Y-m-d'))
                    ->set('M_CustomerEndDate', isset($d['customer_end_date'])?date('Y-m-d', strtotime($d['customer_end_date'])):date('Y-m-d'))
                    ->where('M_CustomerID', $d['customer_id'])
                    ->update( $this->table_name );
        if ($r)
        {
            if ($d['user_customer'] == 'Y' && $d['user_customer_password'] != "")
            {
                $this->load->model('master/m_city');
                $ct = $this->m_city->get_from_kelurahan($d['customer_kelurahan_id']);

                $this->load->model('system/s_usercustomer');
                $this->s_usercustomer->grant_user($d['customer_id'], $d['customer_name'], $ct->M_CityName, $d['user_customer_password'], true);
            }

            // MP Accounts
            $x = $this->db->query("CALL sp_master_customer_mp_save(?,?)", [$d['customer_id'], $d['customer_mps']])->row();
            $this->clean_mysqli_connection($this->db->conn_id);
            // echo $this->db->last_query();
            // print_r($x);

            return ["status"=>"OK", "data"=>$d['customer_id']];
        }

        return ["status"=>"ERR"];
    }

    function del ($id)
    {
        $this->db->set('M_CustomerIsActive', 'N')
                ->where('M_CustomerID', $id)
                ->update($this->table_name);

        return true;
    }

    function revoke_user ($id)
    {
        $this->db->set('S_UserCustomerIsActive', 'N')
                ->where('S_UserCustomerM_CustomerID', $id)
                ->update('s_usercustomer');

        return true;
    }

    function get_one ($id)
    {
        $r = $this->db->select('M_CustomerName customer_name, M_CustomerAddress customer_address,
                                M_KelurahanName kelurahan_name, M_DistrictName district_name,
                                M_CityName city_name, M_ProvinceName province_name,
                                M_CustomerLevelName, M_CustomerEmail customer_email, M_CustomerPhone customer_hp,
                                M_CustomerDuePayment customer_due', false)
                    ->join('m_kelurahan', 'M_KelurahanID = M_CustomerM_KelurahanID')
                    ->join('m_district', 'M_DistrictID = M_KelurahanM_DistrictID')
                    ->join('m_city', 'M_CityID = M_DistrictM_CityID')
                    ->join('m_province', 'M_ProvinceID = M_CityM_ProvinceID')
                    ->join('m_customerlevel', 'M_CustomerM_CustomerLevelID = M_CustomerLevelID')
                    ->where('M_CustomerID', $id)
                    ->get($this->table_name)
                    ->row();
        return $r;
    }

    function search_autocomplete( $d )
    {
        $limit = 50;
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT m_customer.*, M_CityName, M_ProvinceName, kelurahan_id, full_address,
                    M_ProvinceID, M_CityID, M_DistrictID, M_KelurahanID, M_CustomerLevelID,
                    M_CustomerLevelName
                FROM `{$this->table_name}`
                JOIN s_user ON S_UserID = ?
                JOIN s_usergroup ON S_UserS_UserGroupID = S_UserGroupID
                JOIN m_customerlevel ON M_CustomerM_CustomerLevelID = M_CustomerLevelID
                LEFT JOIN m_kelurahan ON M_CustomerM_KelurahanID = M_KelurahanID
                LEFT JOIN m_district ON M_KelurahanM_DistrictID = M_DistrictID
                LEFT JOIN m_city ON m_districtM_CityID = M_CityID
                LEFT JOIN m_province ON M_CityM_ProvinceID = M_ProvinceID
                LEFT JOIN v_cities ON M_CustomerM_KelurahanID = kelurahan_id
                WHERE ((M_CustomerID = ? AND ? <> 0) OR ? = 0)
                AND (`M_CustomerName` LIKE ? OR M_CustomerPhone LIKE ? OR M_CustomerPhone2 LIKE ?)
                AND ((M_CustomerUserID = ? AND S_UserGroupCode <> 'Z.GROUP.01') OR S_UserGroupCode = 'Z.GROUP.01' OR S_UserGroupCode = 'Z.GROUP.02')
                AND ((? <> 0 AND M_CustomerParentID = ?) OR (? = 0))
                AND `M_CustomerIsActive` = 'Y'
                LIMIT {$limit}", [$d['user_id'],
                    $d['customer_id'], $d['customer_id'], $d['customer_id'],
                    $d['customer_name'], $d['customer_name'], $d['customer_name'], $d['user_id'], $d['parent_id'], $d['parent_id'], $d['parent_id']]);
        if ($r)
        {
            $r = $r->result_array();
            foreach ($r as $k => $v)
            {
                $q = $this->db->query("SELECT fn_address_kelurahan(?) as x", [$v['M_CustomerM_KelurahanID']])->row();
                $r[$k]['address_kelurahan'] = $q->x;

                $q = $this->db->query("SELECT fn_master_customer_note(?) as x", [$v['M_CustomerID']])->row();
                $r[$k]['customer_note'] = json_decode($q->x);
            }
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            JOIN m_customerlevel ON M_CustomerM_CustomerLevelID = M_CustomerLevelID
            JOIN s_user ON S_UserID = ?
            JOIN s_usergroup ON S_UserS_UserGroupID = S_UserGroupID
            WHERE ((M_CustomerID = ? AND ? <> 0) OR ? = 0)
            AND (`M_CustomerName` LIKE ? OR M_CustomerPhone LIKE ? OR M_CustomerPhone2 LIKE ?)
            AND ((M_CustomerUserID = ? AND S_UserGroupCode <> 'Z.GROUP.01') OR S_UserGroupCode = 'Z.GROUP.01')
            AND ((? <> 0 AND M_CustomerParentID = ?) OR (? = 0))
            AND `M_CustomerIsActive` = 'Y'", [$d['user_id'], 
                $d['customer_id'], $d['customer_id'], $d['customer_id'],
                $d['customer_name'], $d['customer_name'], $d['customer_name'], $d['user_id'], $d['parent_id'], $d['parent_id'], $d['parent_id']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }

    function customer_non_user()
    {
        $r = $this->db->join('s_usercustomer','S_UserCustomerM_CustomerID = M_CustomerID ANd S_UserCustomerIsActive="Y"', 'left')
                    ->join('m_customerlevel','M_CustomerM_CustomerLevelID = M_CustomerLevelID')
                    ->join('m_kelurahan', 'M_CustomerM_KelurahanID = M_KelurahanID')
                    ->join('m_district', 'M_KelurahanM_DistrictID = M_DistrictID')
                    ->join('m_city', 'M_DistrictM_CityID = M_CityID')
                    ->where('M_CustomerIsActive', 'Y')
                    ->where('M_CustomerLevelCode', 'CUST.DISTRIBUTOR')
                    // ->where('M_CustomerLevelCOde <>', 'CUST.ENDUSER')
                    // ->where('M_CustomerLevelCOde <>', 'CUST.FAMILY')
                    ->where('S_UserCustomerID IS NULL', null)
                    ->get($this->table_name)
                    ->result_array();

        foreach ($r as $k => $v)
        {
            $c = explode(' ', $v['M_CityName']);
            $this->load->model('system/s_usercustomer');
            $x = $this->s_usercustomer->grant_user($customer_id, $c->customer_name, $c->city_name, "", true);
        }

        return $r;
    }

    function grant_user($customer_id)
    {
        $c = $this->get_one($customer_id);
        $this->load->model('system/s_usercustomer');
        $x = $this->s_usercustomer->grant_user($customer_id, $c->customer_name, $c->city_name, "", true, $c->customer_email);

        return $x;
    }

    function search_single($customer_no)
    {
        $r = $this->db->query("CALL sp_master_customer_search_single(?)", [$customer_no])
                        ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        return $r;
    }

    function degrade($customer_id, $uid = 0)
    {
        $r = $this->db->query("CALL sp_master_customer_degrade(?,?)", [$customer_id, $uid])
                        ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        return $r;
    }

    function transfer($d)
    {
        $r = $this->db->query("CALL sp_master_customer_transfer(?,?,?,?)", [$d['old_uid'], $d['new_uid'], $d['type'], $d['customer_ids']])
                        ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        return $r;
    }
}

?>
