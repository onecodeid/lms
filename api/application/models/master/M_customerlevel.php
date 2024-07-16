<?php

class M_customerlevel extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_customerlevel";
        $this->table_key = "M_CustomerLevelID";
    }

    function search( $d )
    {
        $l = ['records'=>[], 'total'=>0];
        $include_inactive = isset($d['include_inactive'])?"":"AND a.`M_CustomerLevelIsActive` = 'Y'";

        $r = $this->db->query(
                "SELECT a.*, s_user.*, s_usergroup.*,
                    a.M_CustomerLevelID level_id,
                    a.M_CustomerLevelCode level_code,
                    a.M_CustomerLevelName level_name,
                    a.M_CustomerLevelIsSeller level_is_seller,
                    a.M_CustomerLevelIsFee level_is_fee,
                    a.M_CustomerLevelIsNew level_is_new,
                    a.M_CustomerLevelTargetMonthly level_target_monthly,
                    a.M_CustomerLevelTargetDuration level_target_duration,
                    a.M_CustomerLevelTargetCumulative level_target_cumulative,
                    a.M_CustomerLevelTargetCumulativeNote level_target_cumulative_note,
                    a.M_CustomerLevelMonthlyMin level_monthly_min,
                    a.M_CustomerLevelMonthlyMax level_monthly_max,
                    a.M_CustomerLevel3MonthMin level_3month_min,
                    a.M_CustomerLevel3MonthMax level_3month_max,
                    a.M_CustomerLevelUpgradeID level_upgrade_id,
                    a.M_CustomerLevelDowngradeID level_downgrade_id,
                    IFNULL(b.M_CustomerLevelName, '') level_upgrade_name,
                    IFNULL(c.M_CustomerLevelName, '') level_downgrade_name,
                    a.M_CustomerLevelIsActive level_active
                    
                FROM `{$this->table_name}` a
                JOIN s_user ON S_UserID = ?
                JOIN s_usergroup ON S_UserGroupID = S_UserS_UserGroupID
                LEFT JOIN `{$this->table_name}` b ON a.M_CustomerLevelUpgradeID = b.M_CustomerLevelID
                LEFT JOIN `{$this->table_name}` c ON a.M_CustomerLevelDowngradeID = c.M_CustomerLevelID
                WHERE a.`M_CustomerLevelName` LIKE ?
                {$include_inactive}
                AND ((a.M_CustomerLevelCode <> 'CUST.FAMILY' AND a.M_CustomerLevelCode <> 'CUST.DISTRIBUTOR' AND S_UserGroupCode <> 'Z.GROUP.01' AND S_UserGroupCode <> 'Z.GROUP.02')
                    OR S_UserGroupCode = 'Z.GROUP.01' OR S_UserGroupCode = 'Z.GROUP.02')
                    
                ORDER BY M_CustomerLevelIsActive DESC, M_CustomerLevelName ASC", [$d['user_id'], $d['customer_level_name']]);
        if ($r)
        {
            $r = $r->result_array(); $l['q'] = $this->db->last_query();
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}` a
                JOIN s_user ON S_UserID = ?
                JOIN s_usergroup ON S_UserGroupID = S_UserS_UserGroupID
            WHERE `M_CustomerLevelName` LIKE ?
                {$include_inactive}
                AND ((M_CustomerLevelCode <> 'CUST.DISTRIBUTOR' AND S_UserGroupCode <> 'Z.GROUP.01' AND S_UserGroupCode <> 'Z.GROUP.02')
                    OR S_UserGroupCode = 'Z.GROUP.01' OR S_UserGroupCode = 'Z.GROUP.02')", [$d['user_id'], $d['customer_level_name']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }

    function get_one($id) {
        $r = $this->db->where('M_CustomerLevelID', $id)
                    ->get($this->table_name)
                    ->row();
        return $r;
    }

    function save ( $d )
    {
        $r = $this->db->set('M_CustomerLevelName', $d['level_name'])
                    ->set('M_CustomerLevelCode', $d['level_code'])
                    ->set('M_CustomerLevel3MonthMin', $d['level_3month_min'])
                    ->set('M_CustomerLevel3MonthMax', $d['level_3month_max'])
                    ->set('M_CustomerLevelMonthlyMin', $d['level_monthly_min'])
                    ->set('M_CustomerLevelMonthlyMax', $d['level_monthly_max'])
                    ->set('M_CustomerLevelUpgradeID', $d['level_upgrade'])
                    ->set('M_CustomerLevelDowngradeID', $d['level_downgrade']);
                    // ->set('M_CategoryUserID', $d['user_id']);
        if (isset($d['level_id']))
        {
            $this->db->where('M_CustomerLevelID', $d['level_id'])
                ->update( $this->table_name );
            $id = $d['level_id'];
        }
        else
        {
            $this->db->insert( $this->table_name );
            $id = $this->db->insert_id();
        }

        if ($r)
        {
            return (object) ["status"=>"OK", "data"=>$id];
        }

        return (object) ["status"=>"ERR"];
    }

    function del ($id)
    {
        $this->db->set('M_CustomerLevelIsActive', 'N')
                ->where('M_CustomerLevelID', $this->sys_input['id'])
                ->update($this->table_name);

        return true;
    }

    function restore ($id)
    {
        $this->db->set('M_CustomerLevelIsActive', 'Y')
                ->where('M_CustomerLevelID', $this->sys_input['id'])
                ->update($this->table_name);

        return true;
    }
}

?>