<?php

class L_Sodetail extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = 'l_sodetail';
        $this->table_key = 'L_SoDetailID';
    }

    function search( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query("CALL sp_so_detail_search(?, ?)", [$d['order_id'], $d['search']]);
        $this->clean_mysqli_connection($this->db->conn_id);

        if ($r->num_rows() > 0)
        {
            $r = $r->result_array();
            $l['records'] = $r;
            $l['total'] = sizeof($r);
        }

        // $r = $this->db->query(
        //         "SELECT *
        //         FROM `{$this->table_name}`
        //         JOIN m_item ON L_SoDetailM_ItemID = M_ItemID
        //         LEFT JOIN i_stock ON I_StockM_ItemID = M_ItemID
        //         WHERE (`M_ItemName` LIKE ?)
        //         AND `L_SoDetailIsActive` = 'Y'
        //         AND `L_SoDetailL_SoID` = ?", [$d['search'], $d['order_id']]);
        // if ($r)
        // {
        //     $r = $r->result_array();
        //     $l['records'] = $r;
        // }

        // $r = $this->db->query(
        //     "SELECT count(`{$this->table_key}`) n
        //     FROM `{$this->table_name}`
        //     JOIN m_item ON L_SoDetailM_ItemID = M_ItemID
        //         WHERE (`M_ItemName` LIKE ?)
        //         AND `L_SoDetailIsActive` = 'Y'
        //         AND `L_SoDetailL_SoID` = ?", [$d['search'], $d['order_id']]);
        // if ($r)
        // {
        //     $l['total'] = $r->row()->n;
        // }
            
        return $l;
    }

    function cancel($id)
    {
        $r = $this->db->query("CALL sp_so_detail_cancel(?)", $id)
                        ->row();
        $this->clean_mysqli_connection($this->db->conn_id);
        return $r;
    }
}
?>