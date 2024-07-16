<?php

class M_coupontype extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_coupontype";
        $this->table_key = "M_CouponTypeID";
    }

    function search( $d )
    {
        $limit = 10; // isset($d['limit']) ? $d['limit'] : 10;
        $offset = 0; // ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $r = $this->db->query(
                "SELECT *
                FROM `{$this->table_name}`
                WHERE `M_CouponTypeName` LIKE ?
                AND `M_CouponTypeIsActive` = 'Y'
                LIMIT {$limit} OFFSET {$offset}", [$d['coupon_name']]);
        if ($r)
        {
            $l['records'] = $r->result_array();
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            WHERE `M_CouponTypeName` LIKE ?
            AND `M_CouponTypeIsActive` = 'Y'", [$d['coupon_name']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    // function save ( $d )
    // {
    //     $r = $this->db->set('M_CouponName', $d['coupon_name'])
    //                 ->set('M_CouponCode', $d['coupon_code']);
    //                 // ->set('M_CouponUserID', $d['user_id']);
    //     if (isset($d['coupon_id']))
    //     {
    //         $this->db->where('M_CouponID', $d['coupon_id'])
    //             ->update( $this->table_name );
    //         $id = $d['coupon_id'];
    //     }
    //     else
    //     {
    //         $this->db->insert( $this->table_name );
    //         $id = $this->db->insert_id();
    //     }

    //     if ($r)
    //     {
    //         return (object) ["status"=>"OK", "data"=>$id];
    //     }

    //     return (object) ["status"=>"ERR"];
    // }

    // function del ($id)
    // {
    //     $this->db->set('M_CouponIsActive', 'N')
    //             ->where('M_CouponID', $this->sys_input['id'])
    //             ->update($this->table_name);

    //     return true;
    // }
}

?>