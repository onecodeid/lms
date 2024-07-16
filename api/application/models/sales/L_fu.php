<?php

class L_fu extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = 'l_fu';
        $this->table_key = 'L_FuID';
    }

    function search_by_lead($id)
    {
        $rst = ['records'=>[], 'total'=>0];

        $r = $this->db->query("SELECT L_FuID fu_id, L_FuDate fu_date,
                    L_FuNote fu_note, L_FuClose fu_close, L_FuPrecloseM_LeadAttrID fu_preclose
                    FROM l_fu
                    WHERE L_FuL_LeadID = ? AND L_FuIsActive = 'Y'", [$id]);

        if ($r->num_rows() > 0)
            $rst['records'] = $r->result_array();

        $r = $this->db->query("SELECT COUNT(L_FuID) as n
            FROM l_fu
            WHERE L_FuL_LeadID = ? AND L_FuIsActive = 'Y'", [$id]);

        if ($r->num_rows() > 0)
            $rst['total'] = $r->row()->n;

        return $rst;
    }
}
