DROP PROCEDURE `sp_r_sales_008`;
DELIMITER ;;
CREATE PROCEDURE `sp_r_sales_008` (IN `sdate` date, IN `edate` date, IN `level_id` int)
BEGIN




SELECT *
FROM m_customerlevel
WHERE M_CustomerLevelID = level_id;

SELECT L_SoID so_id, L_SoDetailID detail_id, L_SoDate so_date, L_SoNumber so_number, ca.M_CustomerID customer_id, ca.M_CustomerName customer_name,
	M_CustomerLevelCode level_code, M_CustomerLevelName level_name, IFNULL(cb.M_CustomerID, 0) ds_customer_id, IFNULL(cb.M_CustomerName, '') ds_customer_name,
	L_SoDetailIsPacket is_packet, L_SoDetailM_ItemCode item_code, L_SoDetailM_ItemName item_name, L_SoDetailM_ItemID item_id, 
	L_SoDetailPrice item_price, L_SoDetailDiscTotal item_disc_total, L_SoDetailApprovedQty item_qty, L_SoDetailSubTotal item_subtotal,
	IFNULL(M_CouponID, 0) coupon_id, IFNULL(M_CouponCode, '') coupon_code, L_SoCouponPercentage coupon_percentage, 
	(L_SoDetailSubTotal * L_SoCouponPercentage) / 100 coupon_amount,
    IFNULL(M_LeadSourceName, '') lead_name
FROM l_so
JOIN l_sodetail ON L_SoID = L_SoDetailL_SoID AND L_SoDetailIsActive = "Y" aND L_SoDetailIsPrice = "Y"
JOIN f_payment ON F_PaymentL_SoID = L_SoID AND F_PaymentIsActive = "Y" ANd F_PaymentConfirmed = "Y"
LEFT JOIN m_item ON L_SoDetailM_ItemID = M_ItemID
LEFT JOIN m_coupon ON L_SoDetailM_CouponID = M_CouponID
LEFT JOIN m_leadsource ON L_SoM_LeadSourceID = M_LeadSourceID
JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID 
	AND (M_OrderStatusCode = 'IV.Confirmed' OR
		M_OrderStatusCode = 'WH.Processing' OR
		M_OrderStatusCode = 'WH.Courier' OR
		M_OrderStatusCode = 'WH.Sent' OR
		M_OrderStatusCode = 'WH.Received')
JOIN m_customer ca ON L_SoM_CustomerID = ca.M_CustomerID
JOIN m_customerlevel ON M_CustomerM_CustomerLEvelID = M_CustomerLevelID
	AND M_CustomerLevelID = level_id
LEFT JOIN m_customer cb ON L_SoDSM_CustomerID = cb.M_CustomerID
WHERE L_SoIsActive = "Y"
AND L_SoDate BETWEEN CONCAT(sdate, ' 00:00:00') AND CONCAT(edate, ' 23:59:59')
ORDER BY L_SoNumber ASC;

END;;
DELIMITER ;