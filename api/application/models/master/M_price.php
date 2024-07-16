<?php

class M_price extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_price";
        $this->table_key = "M_PriceID";
    }

    function search_by_item( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT m_customerlevel.*, 
                    IFNULL(M_PriceAmount, 0) M_PriceAmount, 
                    IFNULL(M_PriceDisc, 0) M_PriceDisc, 
                    IFNULL(M_PriceDisc2, 0) M_PriceDisc2, 
                    IFNULL(M_PriceDiscRp, 0) M_PriceDiscRp, 
                    IFNULL(M_PriceDiscTotal, 0) M_PriceDiscTotal, 
                    IFNULL(M_PriceTotal, 0) M_PriceTotal,
                    IFNULL(M_PriceSale, 0) M_PriceSale,
                    M_PriceSaleStartDate, M_PriceSaleEndDate
                FROM `m_customerlevel`
                LEFT JOIN `{$this->table_name}` on M_CustomerLevelID = M_PriceM_CustomerLevelID AND M_PriceM_ItemID = ? AND M_PriceIsActive = 'Y'
                AND `M_PriceIsActive` = 'Y'
                WHERE M_CustomerLevelIsActive = 'Y'", [$d['item_id']]);
        if ($r)
        {
            $r = $r->result_array();
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
                FROM `m_customerlevel`
                LEFT JOIN `{$this->table_name}` on M_CustomerLevelID = M_PriceM_CustomerLevelID AND M_PriceM_ItemID = ? AND M_PriceIsActive = 'Y'
                AND `M_PriceIsActive` = 'Y'
                WHERE M_CustomerLevelIsActive = 'Y'", [$d['item_id']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }
}

?>