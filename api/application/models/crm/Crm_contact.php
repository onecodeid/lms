<?php

class Crm_contact extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "crm_contact";
        $this->table_key = "Crm_ContactID";
    }

    function save($id, $inp, $uid)
    {
        $r = $this->db->query("CALL sp_crm_contact_save(?, ?, ?, ?, ?)", [$id, $inp['hdata'], $inp['phones'], $inp['tags']])
                ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        return $r;
    }

    function search( $d )
    {
        $limit = isset($d['limit']) ? $d['limit'] : 10;
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $r = $this->db->query(
                "SELECT Crm_ContactName contact_name, Crm_ContactAddress contact_address,
                M_CityName city_name, M_ProvinceName province_name, kelurahan_id, full_address,
                M_ProvinceID province_id, M_CityID city_id, M_DistrictID district_id, M_KelurahanID kelurahan_id
                FROM `{$this->table_name}`
                JOIN s_user ON S_UserID = ?
                JOIN s_usergroup ON S_UserS_UserGroupID = S_UserGroupID
                LEFT JOIN m_kelurahan ON Crm_ContactM_KelurahanID = M_KelurahanID
                LEFT JOIN m_district ON Crm_ContactM_DistrictID = M_DistrictID
                JOIN m_city ON Crm_ContactM_CityID = M_CityID
                JOIN m_province ON M_CityM_ProvinceID = M_ProvinceID
                LEFT JOIN v_cities ON Crm_ContactM_KelurahanID = kelurahan_id
                WHERE (`Crm_ContactName` LIKE ?)
                AND `Crm_ContactIsActive` = 'Y'
                AND ((M_ProvinceID = ? AND ? <> 0) OR ? = 0)
                AND ((M_CityID = ? AND ? <> 0) OR ? = 0)
                AND ((Crm_ContactUserID = ? AND S_UserGroupCode <> 'Z.GROUP.01' AND S_UserGroupCode <> 'Z.GROUP.02') OR S_UserGroupCode = 'Z.GROUP.01' OR S_UserGroupCode = 'Z.GROUP.02')
                ORDER BY Crm_ContactName ASC
                LIMIT {$limit} OFFSET {$offset}", [$d['user_id'], $d['search'],
                                                    $d['province'], $d['province'], $d['province'], $d['city'], $d['city'], $d['city'], $d['user_id']]);
        if ($r)
        {
            $r = $r->result_array();
            foreach ($r as $k => $v)
            {
                // $q = $this->db->query("SELECT fn_address_kelurahan_district(?) as x", [$v['kelurahan_id']])->row();
                // $r[$k]['address_kelurahan'] = $q->x;

                // $q = $this->db->query("SELECT fn_master_customer_referrer(?) as x", [$v['Crm_ContactID']])->row();
                // $r[$k]['referrer'] = json_decode($q->x);

                // $q = $this->db->query("SELECT fn_master_customer_mp(?) as x", [$v['Crm_ContactID']])->row();
                // $r[$k]['customer_mps'] = json_decode($q->x);

                // $q = $this->db->query("SELECT fn_master_customer_note(?) as x", [$v['Crm_ContactID']])->row();
                // $r[$k]['customer_note'] = json_decode($q->x);
            }
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            JOIN s_user ON S_UserID = ?
                JOIN s_usergroup ON S_UserS_UserGroupID = S_UserGroupID
                JOIN m_city ON Crm_ContactM_CityID = M_CityID
                JOIN m_province ON M_CityM_ProvinceID = M_ProvinceID
            LEFT JOIN v_cities ON Crm_ContactM_KelurahanID = kelurahan_id
            WHERE (`Crm_ContactName` LIKE ?)
            AND `Crm_ContactIsActive` = 'Y'
            AND ((M_ProvinceID = ? AND ? <> 0) OR ? = 0)
                AND ((M_CityID = ? AND ? <> 0) OR ? = 0)
                AND ((Crm_ContactUserID = ? AND S_UserGroupCode <> 'Z.GROUP.01' AND S_UserGroupCode <> 'Z.GROUP.02') OR S_UserGroupCode = 'Z.GROUP.01' OR S_UserGroupCode = 'Z.GROUP.02')", 
                [$d['user_id'], $d['search'],
                                                    $d['province'], $d['province'], $d['province'], $d['city'], $d['city'], $d['city'], $d['user_id']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }
}

?>