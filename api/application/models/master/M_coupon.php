<?php

class M_coupon extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_coupon";
        $this->table_key = "M_CouponID";
    }

    function search( $d )
    {
        $limit = isset($d['limit']) ? $d['limit'] : 10;
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $r = $this->db->query(
                "SELECT *, fn_master_coupon_product_detail(M_CouponID) items, 
                    fn_master_coupon_packet_detail(M_CouponID) packets,
                    fn_master_coupon_exp_detail(M_CouponID) exps,
                    IF (M_CouponStartDate <= now() AND M_CouponEndDate >= now(), 'N', 'Y') expired,
                    M_CouponMaxQty max_qty, M_CouponUsedQty used_qty
                FROM `{$this->table_name}`
                JOIN m_coupontype ON M_CouponM_CouponTypeID = M_CouponTypeID
                WHERE `M_CouponCode` LIKE ?
                AND `M_CouponIsActive` = 'Y'
                LIMIT {$limit} OFFSET {$offset}", [$d['coupon_code']]);
        if ($r)
        {
            $l['records'] = $r->result_array();
            foreach ($l['records'] as $k => $v)
            {
                $l['records'][$k]['items'] = json_decode($v['items']);
                $l['records'][$k]['packets'] = json_decode($v['packets']);
                $l['records'][$k]['exps'] = json_decode($v['exps']);
            }        
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            JOIN m_coupontype ON M_CouponM_CouponTypeID = M_CouponTypeID
            WHERE `M_CouponCode` LIKE ?
            AND `M_CouponIsActive` = 'Y'", [$d['coupon_code']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    function save ( $d )
    {
        $r = $this->db->set('M_CouponCode', $d['coupon_code'])
                    ->set('M_CouponStartDate', $d['coupon_sdate'])
                    ->set('M_CouponEndDate', $d['coupon_edate'])
                    ->set('M_CouponMinSpend', $d['coupon_min_spend'])
                    ->set('M_CouponMaxSpend', $d['coupon_max_spend'])
                    ->set('M_CouponAmount', $d['coupon_amount'])
                    ->set('M_CouponAmountPercent', isset($d['coupon_amount_p'])?$d['coupon_amount_p']:0)
                    ->set('M_CouponM_CouponTypeID', $d['coupon_type'])
                    ->set('M_CouponM_CategoryID', isset($d['coupon_category_id'])?$d['coupon_category_id']:0)
                    ->set('M_CouponMaxQty', $d['coupon_qty']);
        
        if (isset($d['coupon_reset'])) {
            $this->db->set('M_CouponUsedQty', 0);
        }
                    // ->set('M_CouponUserID', $d['user_id']);
        if (isset($d['coupon_id']))
        {
            $this->db->where('M_CouponID', $d['coupon_id'])
                ->update( $this->table_name );
            $id = $d['coupon_id'];
        }
        else
        {
            $this->db->insert( $this->table_name );
            $id = $this->db->insert_id();
        }

        // DETAIL PRODUCT / PACKET
        if ($r)
        {
            $x = $this->db->query("CALL sp_master_coupon_detail_save(?,?,?,?,?)", [$id,$d['coupon_type_code'],json_encode($d['items']),json_encode($d['packets']),json_encode($d['exps'])])
                        ->row();
            $this->clean_mysqli_connection($this->db->conn_id);
            return (object) ["status"=>"OK", "data"=>['coupon_id'=>$id,'coupon_detail'=>$x], "q"=>$this->db->last_query()];
        }

        return (object) ["status"=>"ERR"];
    }

    function del ($id)
    {
        $this->db->set('M_CouponIsActive', 'N')
                ->where('M_CouponID', $this->sys_input['id'])
                ->update($this->table_name);

        return true;
    }

    function check ($d)
    {
        $r = $this->db->query("CALL sp_master_coupon_check_spend(?,?,?,?,?)", [$d['coupon_code'], $d['courier_id'], json_encode($d['items']), json_encode($d['packets']),
                                isset($d['spend'])?$d['spend']:0])
                        ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        return $r;
    }
}

?>