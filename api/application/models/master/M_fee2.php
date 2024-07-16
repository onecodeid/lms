<?php

class M_fee2 extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_fee2";
        $this->table_key = "M_Fee2ID";
    }

    
    function search_by_user( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT M_CustomerLevelID level_id, M_CustomerLevelCode level_code, 
                    M_CustomerLevelName level_name, IF(M_Fee2ID IS NULL, 'N', 'Y') `is`,
                    IFNULL(M_Fee2Amount, 0) `amount`,
                    IFNULL(M_Fee2RewardAmount, 0) `point`,
                    IFNULL(M_Fee2PointAmount, 0) `reward`
                FROM `m_customerlevel`
                LEFT JOIN `{$this->table_name}` on M_CustomerLevelID = M_Fee2M_CustomerLevelID AND M_Fee2S_UserID = ? AND M_Fee2IsActive = 'Y'", [$d['user_id']]);
        if ($r)
        {
            $r = $r->result_array();
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`M_CustomerLevelID`) n
                FROM `m_customerlevel`");
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }
}

?>