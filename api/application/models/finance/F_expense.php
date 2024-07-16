<?php

class F_expense extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = 'f_expense';
        $this->table_key = 'F_ExpenseID';
    }

    function search( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT F_ExpenseID expense_id,
                    F_ExpenseDate expense_date,
                    F_ExpenseM_ExpenseID m_expense_id,
                    F_ExpenseM_ExpenseType expense_type,
                    F_ExpenseAmount expense_amount,
                    F_ExpenseNote expense_note,
                    M_ExpenseCode expense_code,
                    M_ExpenseName expense_name
                FROM `f_expense`
                JOIN m_expense ON F_ExpenseM_ExpenseID = M_ExpenseID
                WHERE (`M_ExpenseName` LIKE ?)
                AND F_ExpenseDate BETWEEN ? AND ?
                AND `F_ExpenseIsActive` = 'Y'", [$d['search'], $d['sdate'], $d['edate']]);
        if ($r)
        {
            $r = $r->result_array();                
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`F_ExpenseID`) n
            FROM `f_expense`
            JOIN m_expense ON F_ExpenseM_ExpenseID = M_ExpenseID
            WHERE (`M_ExpenseName` LIKE ?)
                AND F_ExpenseDate BETWEEN ? AND ?
                AND `F_ExpenseIsActive` = 'Y'", [$d['search'], $d['sdate'], $d['edate']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }

    function save( $d, $id = 0 )
    {
        $this->db->set('F_ExpenseM_ExpenseID', $d['expense_m_id'])
                    ->set('F_ExpenseAmount', $d['expense_amount'])
                    ->set('F_ExpenseNote', $d['expense_note'])
                    ->set('F_ExpenseDate', $d['expense_date']);
        if (!$id) {
            $r = $this->db->insert($this->table_name);
            return $this->db->insert_id();
        }
        else
            $r = $this->db->where('F_ExpenseID', $id)
                    ->update($this->table_name);
        return $r;
    }

    function del ($id)
    {
        $this->db->set('F_ExpenseIsActive', 'N')
                ->where('F_ExpenseID', $id)
                ->update($this->table_name);

        return true;
    }
}
?>