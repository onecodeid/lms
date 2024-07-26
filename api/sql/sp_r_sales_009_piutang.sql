DROP PROCEDURE `sp_r_sales_009_piutang`;
DELIMITER ;;
CREATE PROCEDURE `sp_r_sales_009_piutang` (IN `sdate` date, IN `edate` date)
BEGIN

SELECT "Semua Admin" S_UserUsername;

SELECT L_SoID so_id, L_SoDetailID detail_id, L_SoDate so_date, L_SoNumber so_number, ca.M_CustomerID customer_id, ca.M_CustomerName customer_name,
	M_CustomerLevelCode level_code, M_CustomerLevelName level_name, IFNULL(cb.M_CustomerID, 0) ds_customer_id, IFNULL(cb.M_CustomerName, '') ds_customer_name,
	IFNULL(L_SoAdsNumber, '') so_ads_number, IFNULL(L_SoMpNumber, '') so_mp_number, IFNULL(L_SoMpCost, 0) so_mp_cost,
	L_SoDetailIsPacket is_packet, L_SoDetailM_ItemCode item_code, L_SoDetailM_ItemName item_name, L_SoDetailM_ItemID item_id, 
	L_SoDetailPrice item_price, L_SoDetailDiscTotal item_disc_total, L_SoDetailApprovedQty item_qty, L_SoDetailSubTotal item_subtotal,
	IFNULL(M_CouponID, 0) coupon_id, IFNULL(M_CouponCode, '') coupon_code, 
	IF(M_CouponID IS NULL, 0, L_SoCouponPercentage) coupon_percentage, 
	(L_SoDetailSubTotal * IF(M_CouponID IS NULL, 0, L_SoCouponPercentage)) / 100 coupon_amount,
	IFNULL(S_UserUsername, "") admin_uname, IFNULL(S_UserProfileFullName, "") admin_name,
	IF(L_SoDetailM_CouponID <> 0, L_SoDetailSubTotal * L_SoCouponPercentage / 100, 0) coupon_disc,
    IFNULL(M_LeadSourceName, '') lead_name,
    L_InvoicePaid invoice_paid, L_InvoiceUnpaid invoice_unpaid, L_InvoiceTotal invoice_total
FROM l_invoice
JOIN l_so ON L_InvoiceL_SoID = L_SoID
JOIN l_sodetail ON L_SoID = L_SoDetailL_SoID AND L_SoDetailIsActive = "Y" aND L_SoDetailIsPrice = "Y"
LEFT JOIN m_item ON L_SoDetailM_ItemID = M_ItemID
LEFT JOIN m_coupon ON L_SoDetailM_CouponID = M_CouponID
LEFT JOIN m_leadsource ON L_SoM_LeadSourceID = M_LeadSourceID
JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID 
--	AND (M_OrderStatusCode = 'IV.Confirmed' OR
--		M_OrderStatusCode = 'WH.Processing' OR
--		M_OrderStatusCode = 'WH.Courier' OR
--		M_OrderStatusCode = 'WH.Sent')
JOIN m_customer ca ON L_SoM_CustomerID = ca.M_CustomerID
JOIN m_customerlevel ON M_CustomerM_CustomerLEvelID = M_CustomerLevelID
LEFT JOIN m_customer cb ON L_SoDSM_CustomerID = cb.M_CustomerID
LEFT JOIN s_user ON L_SoUserID = S_UserID
LEFT JOIN s_userprofile ON S_UserProfileS_UserID = S_UserID AND S_UserProfileIsActive= "Y"
WHERE L_SoIsActive = "Y" AND L_InvoiceIsActive = "Y"
AND L_SoDate BETWEEN CONCAT(sdate, ' 00:00:00') AND CONCAT(edate, ' 23:59:59')
-- AND ((L_SoUserID = uid AND uid <> 0) OR uid = 0)
AND L_InvoiceUnpaid > 0
ORDER BY L_SoNumber ASC;

END
;;
DELIMITER ;