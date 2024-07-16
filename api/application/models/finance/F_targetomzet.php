<?php

class F_targetomzet extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = 'f_targetomzet';
        $this->table_key = 'F_TargetOmzetID';
    }

    function search( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT F_TargetOmzetID target_id,
                    F_TargetOmzetAmount target_amount,
                    M_CustomerLevelName level_name
                FROM `f_targetomzet`
                JOIN m_customerlevel ON F_TargetOmzetM_CustomerLevelID = M_CustomerLevelID
                WHERE (`M_CustomerLevelName` LIKE ?)
                AND `F_TargetOmzetIsActive` = 'Y'", [$d['search']]);
        if ($r)
        {
            $r = $r->result_array();                
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`F_TargetOmzetID`) n
            FROM `f_targetomzet`
            JOIN m_customerlevel ON F_TargetOmzetM_CustomerLevelID = M_CustomerLevelID
            WHERE (`M_CustomerLevelName` LIKE ?)
                AND `F_TargetOmzetIsActive` = 'Y'", [$d['search']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }

    function save( $d )
    {
        foreach ($d as $k => $v)
        {
            $this->db->set('F_TargetOmzetAmount', $v['target_amount'])
                    ->where('F_TargetOmzetID', $v['target_id'])
                    ->update($this->table_name);
        }

        return true;
    }
}
?>