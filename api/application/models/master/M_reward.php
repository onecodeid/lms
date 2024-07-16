<?php

class M_reward extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_reward";
        $this->table_key = "M_RewardID";
    }

    function search( $d )
    {
        $limit = isset($d['limit']) ? $d['limit'] : 10;
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];
        $pmin = isset($d['point_min']) ? $d['point_min'] : 0;
        $pmax = isset($d['point_max']) ? $d['point_max'] : 9999999;

        $r = $this->db->query(
                "SELECT M_RewardID,
                    M_RewardPoint,
                    M_RewardCode,
                    M_RewardName,
                    M_RewardDescription,
                    M_RewardQuota,
                    M_RewardUsed,	
                    M_RewardImageTmb reward_tmb
                FROM `{$this->table_name}`
                WHERE (`M_RewardName` LIKE ? OR `M_RewardCode` LIKE ?)
                AND M_RewardPoint BETWEEN ? AND ?
                AND `M_RewardIsActive` = 'Y'
                LIMIT {$limit} OFFSET {$offset}", [$d['search'],$d['search'],$pmin,$pmax]);
        if ($r)
        {
            $l['records'] = $r->result_array();
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            WHERE (`M_RewardName` LIKE ? OR `M_RewardCode` LIKE ?)
            AND M_RewardPoint BETWEEN ? AND ?
            AND `M_RewardIsActive` = 'Y'", [$d['search'],$d['search'],$pmin,$pmax]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    function save ( $d, $id = 0 )
    {
        $r = $this->db->set('M_RewardName', $d['reward_name'])
                    ->set('M_RewardCode', $d['reward_code'])
                    ->set('M_RewardPoint', $d['reward_point'])
                    ->set('M_RewardQuota', $d['reward_quota']);
        if (isset($d['reward_image']))
            $this->db->set('M_RewardImage', $d['reward_image'])
                    ->set('M_RewardImageTmb', $d['reward_image_tmb']);
                    // ->set('M_RewardUserID', $d['user_id']);
        if ($id != 0)
        {
            $this->db->where('M_RewardID', $id)
                ->update( $this->table_name );
            $id = $d['reward_id'];
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
        $this->db->set('M_RewardIsActive', 'N')
                ->where('M_RewardID', $this->sys_input['id'])
                ->update($this->table_name);

        return true;
    }
}

?>