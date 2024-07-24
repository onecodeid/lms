<?php

class M_schedule extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_schedule";
        $this->table_key = "M_ScheduleID";
    }

    function search( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT M_ScheduleID schedule_id, M_ScheduleTime schedule_time, M_ScheduleCapacity schedule_capacity, M_DayID day_id, M_DayNameLocalized day_name
                FROM `{$this->table_name}`
                JOIN m_day ON M_DayID = M_ScheduleM_DayID
                WHERE `M_ScheduleM_ItemID` = ?
                AND `M_ScheduleIsActive` = 'Y'
                ORDER BY M_DayNameLocalized asc, M_ScheduleTime asc", [$d['item_id']]);
        if ($r)
        {
            $l['records'] = $r->result_array();
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            JOIN m_day ON M_DayID = M_ScheduleM_DayID
            WHERE `M_ScheduleM_ItemID` = ?
            AND `M_ScheduleIsActive` = 'Y'", [$d['item_id']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }
}

?>