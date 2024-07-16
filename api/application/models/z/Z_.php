<?php

/**
 * undocumented class
 */
class Z_ extends MY_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function get_info($customerid)
    {
        $r = $this->db->select("M_CustomerName cust_name, M_CustomerCode cust_code,
                            IFNULL(Z_SharePoint, 0) share_point")
                ->join("z_share", "Z_ShareM_CustomerID = M_CustomerID and Z_ShareIsActive = 'Y'", "left")
                ->where("M_CustomerID", $customerid)
                ->get("m_customer")
                ->row();

        $sales_point = $this->db->query("CALL sp_stat_seller_002(?)", $customerid)->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        $rank = $this->db->query("CALL sp_z_rank(?)", $customerid)->row();
        $this->clean_mysqli_connection($this->db->conn_id);
        $r->rank = json_decode($rank->data);

        $r->order_last = [];
        $ord = $this->myorder($customerid, 0, "");
        if ($ord->status == "OK" && $ord->data != null)
        {
            $x = json_decode($ord->data);
            $r->order_last = $x->data[0];
        }

        $sales_point = json_decode($sales_point->data);
        $r->sales_point = $sales_point->total_point;

        return $r;
    }

    function login_customer($username, $password)
    {
        $password = md5($this->pass_prefix . $password . $this->pass_suffix);
        $r = $this->db->query("CALL sp_system_login_customer_android(?, ?)", [$username, $password])
                    ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        return $r;
    }

    function logout_customer($uid)
    {
        $query = $this->db->query("
            UPDATE s_usercustomer
            SET S_UserCustomerIsLogin = 'N', S_UserCustomerToken = null
            WHERE S_UserCustomerID = ?", 
        [$uid]);

        if (!$query) 
            return false;
        return true;
    }

    function myorder($customerid, $start, $token)
    {
        $r = $this->db->query("CALL sp_z_myorder(?,?,?)", [$customerid, $token, $start])
                ->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        return $r;
    }

    function adminorder($adminid, $start, $token)
    {
        return;
    }

    function myorder_detail($id)
    {
        $r = $this->db->select('L_SoID so_id, DATE_FORMAT(L_SoDate, "%Y-%m-%d") so_date, L_SoNumber so_number, ca.M_CustomerID customer_id, ca.M_CustomerName customer_name, ca.M_CustomerAddress customer_address, L_SoTotal so_total, L_SoSubTotal so_subtotal, L_SoExpCost so_expcost, L_SoCouponAmount so_coupon, L_SoCODCost so_codcost, L_SoIsCOD so_iscod, M_OrderStatusID status_id, M_OrderStatusCode status_code, M_OrderStatusName status_name, M_OrderStatusSellerName status_seller_name, L_SoIsDS so_isds, IFNULL(cb.M_CustomerName, "") ds_customer_name, IFNULL(cb.M_CustomerAddress, "") ds_customer_address, M_ExpeditionName courier_name, L_SoExpService service_name, fn_so_get_items(L_SoID) items', false)
                    // ->select('l_so.*, m_customer.*, fn_so_get_items(L_SoID) items, M_PaymentTypeCode payment_code', false)
                    ->join('m_customer ca', 'L_SoM_CustomerID = ca.M_CustomerID')
                    ->join('m_customer cb', 'L_SoDSM_CustomerID = cb.M_CustomerID AND L_SoIsDS = "Y"', 'left')
                    ->join('m_paymenttype', 'L_SoM_PaymentID = M_PaymentTypeID')
                    ->join('m_orderstatus', 'L_SoM_OrderStatusID = M_OrderStatusID')
                    ->join('m_expedition', 'L_SoM_ExpeditionID = M_ExpeditionID')
                    ->where('L_SoID', $id)
                    ->get('l_so');
        if ($r->num_rows() > 0)
        {
            $r = $r->row();
            $r->items = json_decode($r->items);
            return $r;
        }

        return false;
    }

    function leaderboard()
    {
        $r = $this->db->query("CALL sp_z_leaderboard()")
                ->result_array();
        $this->clean_mysqli_connection($this->db->conn_id);

        foreach ($r as $k => $v)
        {
            $path = getcwd().'/../../one-member/photo/no-pict-tmb.jpg';
            if (file_exists(getcwd().'/../../one-member/photo/'.$v['cust_code'].'-tmb.jpg'))
                $path = getcwd().'/../../one-member/photo/'.$v['cust_code'].'-tmb.jpg';

            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = base64_encode($data);

            $r[$k]['cust_photo'] = $base64;
        }

        return $r;
    }
}

?>