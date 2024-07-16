<?php

class M_fee extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_fee";
        $this->table_key = "M_FeeID";
    }

    function search_by_item( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT M_CustomerLevelID, M_CustomerLevelCode, M_CustomerLevelName, M_FeeID,
                    IFNULL(M_FeeAmount, 0) M_FeeAmount,
                    IFNULL(M_FeeRewardAmount, 0) M_FeeRewardAmount,
                    IFNULL(M_FeePointAmount, 0) M_FeePointAmount
                FROM `m_customerlevel`
                LEFT JOIN `{$this->table_name}` on M_CustomerLevelID = M_FeeM_CustomerLevelID AND M_FeeM_ItemID = ? AND M_FeeIsActive = 'Y'
                AND `M_FeeIsActive` = 'Y'", [$d['item_id']]);
        if ($r)
        {
            $r = $r->result_array();
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
                FROM `m_customerlevel`
                LEFT JOIN `{$this->table_name}` on M_CustomerLevelID = M_FeeM_CustomerLevelID AND M_FeeM_ItemID = ? AND M_FeeIsActive = 'Y'
                AND `M_FeeIsActive` = 'Y'", [$d['item_id']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }
}

?>