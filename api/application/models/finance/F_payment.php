<?php

class F_payment extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = 'f_payment';
        $this->table_key = 'F_PaymentID';
    }

    function search( $d )
    {
        $limit = isset($d['limit']) ? $d['limit'] : 10;
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $r = $this->db->query(
                "SELECT L_SoID, L_SoDate, L_SoNumber, L_SoTotalQty, L_SoTotal, 
                    L_InvoiceID, L_SoPaymentChannel,
                    L_SoAdsNumber so_ads_number, L_SoMpNumber so_mp_number, L_SoMPCost so_mp_cost,
                    M_CustomerName, M_CustomerAddress, M_CustomerM_KelurahanID,
                    F_PaymentID, F_PaymentDate, F_PaymentAmount,
                    F_PaymentSenderName, F_PaymentReceiptImg,
                    ba.M_BankID M_BankID, ba.M_BankName M_BankName,
                    bb.M_BankID account_bank_id, bb.M_BankName account_bank_name,
                    M_OrderStatusID, M_OrderStatusCode, M_OrderStatusName,
                    M_PaymentTypeCode, M_PaymentTypeName, M_BankAccountNumber, M_BankAccountName,
                    M_CustomerLevelName level_name,
                    L_SoNote order_note,
                    F_IpaymuTrxID, F_IpaymuTrxCode, F_IpaymuExpired, F_IpaymuNote,
                    
                    IFNULL(M_LeadSourceName, '') leadsource_name,
                    IFNULL(M_LeadSourceColor, 'grey') leadsource_color

                FROM `l_invoice`
                JOIN l_so ON L_SoID = L_InvoiceL_SoID
                LEFT JOIN m_leadsource on L_SoM_LeadSourceID = M_LeadSourceID
                LEFT JOIN f_payment ON F_PaymentL_InvoiceID = L_InvoiceID AND F_PaymentIsActive = 'Y'
                LEFT JOIN m_paymenttype ON L_SoM_PaymentID = M_PaymentTypeID
                LEFT JOIN m_bankaccount ON F_PaymentM_BankAccountID = M_BankAccountID
                    LEFT JOIN m_bank bb ON M_BankAccountM_BankID = bb.M_BankID
                LEFT JOIN m_bank ba ON F_PaymentSenderM_BankID = ba.M_BankID
                LEFT JOIN f_ipaymu ON L_SoID = F_IpaymuL_SoID AND F_IpaymuIsActive = 'Y'
                JOIN m_customer ON L_SoM_CustomerID = M_CustomerID
                JOIN m_customerlevel ON M_CustomerM_CustomerLevelID = M_CustomerLevelID
                JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID 
                    AND ((M_OrderStatusCode = 'IV.Paid' AND ? = 'P') OR
                        (M_OrderStatusCode = 'IV.Confirmed' AND ? = 'C') OR
                        ? = 'A')
                LEFT JOIN m_kelurahan ON M_KelurahanID = M_CustomerM_KelurahanID
                WHERE (`L_SoNumber` LIKE ? OR `M_CustomerName` LIKE ?)
                AND L_SoDate BETWEEN ? AND ?
                AND `L_SoIsActive` = 'Y'
                AND L_SoIsCOD = 'N' AND L_InvoiceDue = 'N'
                ORDER BY L_SoNumber DESC
                LIMIT {$limit} OFFSET {$offset}", [$d['status'], $d['status'], $d['status'], $d['search'], $d['search'], $d['sdate'], $d['edate']]);
        if ($r)
        {
            $r = $r->result_array();
            foreach ($r as $k => $v)
            {
                $ct = $this->db->query("SELECT fn_address_city(?) x", [$v['M_CustomerM_KelurahanID']])->row()->x;
                $ct = json_decode($ct);
                $r[$k]['city_name'] = $ct->city_name;
                $r[$k]['city_id'] = $ct->city_id;

                $itms = $this->db->query("SELECT fn_so_get_items(?) x", [$v['L_SoID']])->row()->x;
                $r[$k]['items'] = json_decode($itms);
            }
                
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`L_InvoiceID`) n
            FROM `l_invoice`
            JOIN l_so ON L_SoID = L_InvoiceL_SoID
            JOIN m_customer ON L_SoM_CustomerID = M_CustomerID
            JOIN m_customerlevel ON M_CustomerM_CustomerLevelID = M_CustomerLevelID
            JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID
                AND ((M_OrderStatusCode = 'IV.Paid' AND ? = 'P') OR
                        (M_OrderStatusCode = 'IV.Confirmed' AND ? = 'C') OR
                        ? = 'A')
            WHERE (`L_SoNumber` LIKE ? OR `M_CustomerName` LIKE ?)
            AND L_SoDate BETWEEN ? AND ?
                AND `L_SoIsActive` = 'Y'
                AND L_SoIsCOD = 'N' AND L_InvoiceDue = 'N'", [$d['status'], $d['status'], $d['status'], $d['search'], $d['search'], $d['sdate'], $d['edate']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    function search_cod( $d )
    {
        $limit = isset($d['limit']) ? $d['limit'] : 10;
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $r = $this->db->query(
                "SELECT L_SoID, L_SoDate, L_SoNumber, L_SoTotalQty, L_SoTotal, 
                    L_InvoiceID,
                    M_CustomerName, M_CustomerAddress, M_CustomerM_KelurahanID,
                    F_PaymentID, F_PaymentDate, F_PaymentAmount,
                    F_PaymentSenderName, F_PaymentReceiptImg,
                    M_OrderStatusID, M_OrderStatusCode, M_OrderStatusName,
                    M_PaymentTypeName,
                    M_CustomerLevelName level_name,
                    L_SoNote order_note,
                    IFNULL(M_ExpeditionName, '') expedition_name,
                    IFNULL(M_ExpeditionLogo, '') expedition_logo,
                    L_SoExpService expedition_service,
                    F_PaymentConfirmed payment_confirmed
                FROM `l_invoice`
                JOIN l_so ON L_SoID = L_InvoiceL_SoID
                LEFT JOIN f_payment ON F_PaymentL_InvoiceID = L_InvoiceID AND F_PaymentIsActive = 'Y'
                LEFT JOIN m_paymenttype ON F_PaymentM_PaymentTypeID = M_PaymentTypeID
                JOIN m_customer ON L_SoM_CustomerID = M_CustomerID
                JOIN m_customerlevel ON M_CustomerM_CustomerLevelID = M_CustomerLevelID
                JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID 
                    AND ((M_OrderStatusCode = 'IV.Paid' AND ? = 'P') OR
                        (M_OrderStatusCode = 'IV.Confirmed' AND ? = 'C') OR
                        ? = 'A')
                LEFT JOIN m_kelurahan ON M_KelurahanID = M_CustomerM_KelurahanID
                LEFT JOIN m_expedition ON L_SoM_ExpeditionID = M_ExpeditionID
                WHERE (`L_SoNumber` LIKE ? OR `M_CustomerName` LIKE ?)
                AND L_SoDate BETWEEN ? AND ?
                AND `L_SoIsActive` = 'Y'
                AND L_SoIsCOD = 'Y' AND L_InvoiceDue = 'N'
                ORDER BY L_SoNumber DESC
                LIMIT {$limit} OFFSET {$offset}", [$d['status'], $d['status'], $d['status'], $d['search'], $d['search'], $d['sdate'], $d['edate']]);
        if ($r)
        {
            $r = $r->result_array();
            foreach ($r as $k => $v)
            {
                $ct = $this->db->query("SELECT fn_address_city(?) x", [$v['M_CustomerM_KelurahanID']])->row()->x;
                $ct = json_decode($ct);
                $r[$k]['city_name'] = $ct->city_name;
                $r[$k]['city_id'] = $ct->city_id;

                $itms = $this->db->query("SELECT fn_so_get_items(?) x", [$v['L_SoID']])->row()->x;
                $r[$k]['items'] = json_decode($itms);
            }
                
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`L_InvoiceID`) n
            FROM `l_invoice`
            JOIN l_so ON L_SoID = L_InvoiceL_SoID
            JOIN m_customer ON L_SoM_CustomerID = M_CustomerID
            JOIN m_customerlevel ON M_CustomerM_CustomerLevelID = M_CustomerLevelID
            JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID
                AND ((M_OrderStatusCode = 'IV.Paid' AND ? = 'P') OR
                        (M_OrderStatusCode = 'IV.Confirmed' AND ? = 'C') OR
                        ? = 'A')
            WHERE (`L_SoNumber` LIKE ? OR `M_CustomerName` LIKE ?)
            AND L_SoDate BETWEEN ? AND ?
                AND `L_SoIsActive` = 'Y'
                AND L_SoIsCOD = 'Y' AND L_InvoiceDue = 'N'", [$d['status'], $d['status'], $d['status'], $d['search'], $d['search'], $d['sdate'], $d['edate']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    function search_due( $d )
    {
        $limit = isset($d['limit']) ? $d['limit'] : 10;
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $r = $this->db->query(
                "SELECT L_SoID, L_SoDate, L_SoNumber, L_SoTotalQty, L_SoTotal, 
                    L_InvoiceID, L_SoPaymentChannel,
                    L_SoAdsNumber so_ads_number, L_SoMpNumber so_mp_number, L_SoMPCost so_mp_cost,
                    M_CustomerName, M_CustomerAddress, M_CustomerM_KelurahanID,
                    F_PaymentID, F_PaymentDate, F_PaymentAmount,
                    F_PaymentSenderName, F_PaymentReceiptImg,
                    ba.M_BankID M_BankID, ba.M_BankName M_BankName,
                    bb.M_BankID account_bank_id, bb.M_BankName account_bank_name,
                    M_OrderStatusID, M_OrderStatusCode, M_OrderStatusName,
                    M_PaymentTypeCode, M_PaymentTypeName, M_BankAccountNumber, M_BankAccountName,
                    M_CustomerLevelName level_name,
                    L_SoNote order_note,
                    F_IpaymuTrxID, F_IpaymuTrxCode, F_IpaymuExpired, F_IpaymuNote,
                    
                    IFNULL(M_LeadSourceName, '') leadsource_name,
                    IFNULL(M_LeadSourceColor, 'grey') leadsource_color

                FROM `l_invoice`
                JOIN l_so ON L_SoID = L_InvoiceL_SoID
                LEFT JOIN m_leadsource on L_SoM_LeadSourceID = M_LeadSourceID
                LEFT JOIN f_payment ON F_PaymentL_InvoiceID = L_InvoiceID AND F_PaymentIsActive = 'Y'
                LEFT JOIN m_paymenttype ON L_SoM_PaymentID = M_PaymentTypeID
                LEFT JOIN m_bankaccount ON F_PaymentM_BankAccountID = M_BankAccountID
                    LEFT JOIN m_bank bb ON M_BankAccountM_BankID = bb.M_BankID
                LEFT JOIN m_bank ba ON F_PaymentSenderM_BankID = ba.M_BankID
                LEFT JOIN f_ipaymu ON L_SoID = F_IpaymuL_SoID AND F_IpaymuIsActive = 'Y'
                JOIN m_customer ON L_SoM_CustomerID = M_CustomerID
                JOIN m_customerlevel ON M_CustomerM_CustomerLevelID = M_CustomerLevelID
                JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID 
                    AND ((M_OrderStatusCode = 'IV.Paid' AND ? = 'P') OR
                        (M_OrderStatusCode = 'IV.Confirmed' AND ? = 'C') OR
                        ? = 'A')
                LEFT JOIN m_kelurahan ON M_KelurahanID = M_CustomerM_KelurahanID
                WHERE (`L_SoNumber` LIKE ? OR `M_CustomerName` LIKE ?)
                AND L_SoDate BETWEEN ? AND ?
                AND `L_SoIsActive` = 'Y'
                AND L_SoIsCOD = 'N' AND L_InvoiceDue = 'Y'
                ORDER BY L_SoNumber DESC
                LIMIT {$limit} OFFSET {$offset}", [$d['status'], $d['status'], $d['status'], $d['search'], $d['search'], $d['sdate'], $d['edate']]);
        if ($r)
        {
            $r = $r->result_array();
            foreach ($r as $k => $v)
            {
                $ct = $this->db->query("SELECT fn_address_city(?) x", [$v['M_CustomerM_KelurahanID']])->row()->x;
                $ct = json_decode($ct);
                $r[$k]['city_name'] = $ct->city_name;
                $r[$k]['city_id'] = $ct->city_id;

                $itms = $this->db->query("SELECT fn_so_get_items(?) x", [$v['L_SoID']])->row()->x;
                $r[$k]['items'] = json_decode($itms);
            }
                
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`L_InvoiceID`) n
            FROM `l_invoice`
            JOIN l_so ON L_SoID = L_InvoiceL_SoID
            JOIN m_customer ON L_SoM_CustomerID = M_CustomerID
            JOIN m_customerlevel ON M_CustomerM_CustomerLevelID = M_CustomerLevelID
            JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID
                AND ((M_OrderStatusCode = 'IV.Paid' AND ? = 'P') OR
                        (M_OrderStatusCode = 'IV.Confirmed' AND ? = 'C') OR
                        ? = 'A')
            WHERE (`L_SoNumber` LIKE ? OR `M_CustomerName` LIKE ?)
            AND L_SoDate BETWEEN ? AND ?
                AND `L_SoIsActive` = 'Y'
                AND L_SoIsCOD = 'N' AND L_InvoiceDue = 'Y'", [$d['status'], $d['status'], $d['status'], $d['search'], $d['search'], $d['sdate'], $d['edate']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    function search_detail( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "
                (SELECT *
                FROM `{$this->table_name}`
                JOIN m_packet ON L_SoDetailM_PacketID = M_PacketID
                LEFT JOIN m_item ON L_SoDetailM_ItemID = M_ItemID
                LEFT JOIN i_stock ON I_StockM_ItemID = M_PacketID
                WHERE (`M_PacketName` LIKE ?)
                AND `L_SoDetailIsActive` = 'Y'
                AND `L_SoDetailL_SoID` = ?
                ORDER BY M_PacketName)
                UNION
                (SELECT *
                FROM `{$this->table_name}`
                LEFT JOIN m_packet ON L_SoDetailM_PacketID = M_PacketID
                JOIN m_item ON L_SoDetailM_ItemID = M_ItemID
                LEFT JOIN i_stock ON I_StockM_ItemID = M_ItemID
                WHERE (`M_ItemName` LIKE ?)
                AND `L_SoDetailIsActive` = 'Y'
                AND `L_SoDetailL_SoID` = ?
                AND L_SoDetailIsPrice = 'Y'
                ORDER BY M_ItemName)
                ", [$d['search'], $d['order_id'], $d['search'], $d['order_id']]);
        if ($r)
        {
            $r = $r->result_array();
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            JOIN m_item ON L_SoDetailM_ItemID = M_ItemID
                WHERE (`M_ItemName` LIKE ?)
                AND `L_SoDetailIsActive` = 'Y'
                AND `L_SoDetailL_SoID` = ?", [$d['search'], $d['order_id']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }

    function confirm( $id, $uid )
    {
        $r = $this->db->query("CALL sp_finance_pay_confirm(?, ?)", [$id, $uid])
                        ->row();
        $this->clean_mysqli_connection($this->db->conn_id);
        return $r;
    }

    function save( $d )
    {
        $hdata = [
            'sender_date' => date('Y-m-d H:i:s', strtotime($d['transfer_date'] . ' ' . $d['transfer_time'])),
            'sender_name' => $d['transfer_name'],
            'sender_bank_id' => $d['bank_id'],
            'account_id' => $d['account_id'],
            'note' => $d['transfer_note'],
            'payment_type_id' => $d['payment_type_id']
        ];

        $r = $this->db->query("CALL sp_finance_pay(?, ?, ?)", [$d['order_id'], json_encode($hdata), $d['amount']])
                    ->row();
        $this->clean_mysqli_connection($this->db->conn_id);
        
        return $r;
    }

    function upload_receipt( $id, $img )
    {
        $this->db->set('F_PaymentReceiptImg', $img)
                ->where('F_PaymentID', $id)
                ->update($this->table_name);
        return true;
    }
}
?>