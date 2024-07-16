<?php

class Rec_omzet_pivot extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = 'rec_omzet_pivot';
        $this->table_key = 'Rec_OmzetPivotID';
    }

    function search( $d )
    {
        $limit = isset($d['limit']) ? $d['limit'] : 10;
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $this->load->model('master/m_customerlevel');
        $level = $this->m_customerlevel->get_one($d['level_id']);
        
        $date=date_create(date("Y-m-d"));
        date_sub($date,date_interval_create_from_date_string($level->M_CustomerLevelTargetDuration." months"));
        $min_date = date_format($date,"Y-m-01");

        $r = $this->db->query(
                "SELECT m_customerid customer_id, m_customername customer_name, DATE(M_CustomerCreated) created_date, M_CustomerAddress customer_address, M_CustomerM_KelurahanID village_id,
                            M_CustomerLevelID level_id, M_CustomerLevelCode level_code,
                            Rec_OmzetPivotBaseDate base_date,
                            Rec_OmzetPivotMonth1 month1,
                            Rec_OmzetPivotMonth2 month2,
                            Rec_OmzetPivotMonth3 month3,
                            Rec_OmzetPivotMonth4 month4,
                            Rec_OmzetPivotMonth5 month5,
                            Rec_OmzetPivotMonth6 month6,
                            Rec_OmzetPivotMonth7 month7,
                            Rec_OmzetPivotMonth8 month8,
                            Rec_OmzetPivotMonth9 month9,
                            Rec_OmzetPivotMonth10 month10,
                            Rec_OmzetPivotMonth11 month11,
                            Rec_OmzetPivotMonth12 month12,
                            Rec_OmzetPivotCumulative cumulative,
                            M_CustomerLevelName level_name,
                            M_CustomerLevelTargetCumulative target,
                            M_CustomerLevelTargetMonthly target_monthly,
                            M_CustomerLevelTargetDuration target_duration,
                            M_CustomerLevelTargetCumulativeNote target_note
                            FROM `one-sales-log`.rec_omzet_pivot
                
                JOIN m_customer ON Rec_OmzetPivotM_CustomerID = M_CustomerID
                JOIN m_customerlevel ON Rec_OmzetPivotM_CustomerLevelID = M_CustomerLevelID
                WHERE Rec_OmzetPivotIsActive = 'Y' 
                AND Rec_OmzetPivotM_CustomerLevelID = ?
                AND Rec_OmzetPivotBaseDate <= ?
                AND Rec_OmzetPivotCumulative < M_CustomerLevelTargetCumulative
                AND M_CustomerName LIKE ?
                ORDER BY M_CustomerName DESC
                LIMIT {$limit} OFFSET {$offset}", [$d['level_id'], $min_date, $d['search']]);
        if ($r)
        {
            $r = $r->result_array();  
            foreach ($r as $k => $v) {
                $x = $this->db->query("SELECT fn_address_json(?) x", [$v['village_id']])->row();
                $r[$k]['cities'] = json_decode($x->x);
            }
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`Rec_OmzetPivotID`) n
            FROM `one-sales-log`.rec_omzet_pivot
                JOIN m_customer ON Rec_OmzetPivotM_CustomerID = M_CustomerID
                JOIN m_customerlevel ON Rec_OmzetPivotM_CustomerLevelID = M_CustomerLevelID
                WHERE Rec_OmzetPivotIsActive = 'Y' 
                AND Rec_OmzetPivotM_CustomerLevelID = ?
                AND Rec_OmzetPivotBaseDate <= ?
                AND Rec_OmzetPivotCumulative < M_CustomerLevelTargetCumulative
                AND M_CustomerName LIKE ?", [$d['level_id'], $min_date, $d['search']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }
}
?>