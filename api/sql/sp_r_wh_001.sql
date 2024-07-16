BEGIN

DECLARE cust_level_code VARCHAR(50);
DECLARE is_ds CHAR(1);
DECLARE uid INTEGER;
DECLARE cust_id INTEGER;
DECLARE sender_name VARCHAR(100);
DECLARE sender_address VARCHAR(255);
DECLARE sender_cities VARCHAR(255);
DECLARE sender_kelurahan VARCHAR(255);
DECLARE sender_district VARCHAR(255);
DECLARE sender_city VARCHAR(255);
DECLARE sender_province VARCHAR(255);
DECLARE sender_phone VARCHAR(50);
DECLARE company_phone VARCHAR(50);

SELECT M_CustomerLevelCode, L_SoIsDS, L_SoUserID, L_SoM_CustomerID
INTO cust_level_code, is_ds, uid, cust_id
FROM l_so
JOIN m_customer ON L_SoM_CustomerID = M_CustomerID
JOIN m_customerlevel ON M_CustomerM_CustomerLevelID = M_CustomerLevelID
WHERE L_SoID = soid;

IF is_ds = "N" THEN
	IF cust_level_code = "CUST.DISTRIBUTOR" THEN
		SET sender_name = "ZALFA MIRACLE";
	ELSE
		SET sender_name = (SELECT S_UserProfileFullName FROM s_userprofile WHERE S_UserProfileS_UserID = uid AND S_UserProfileIsActive = "Y" LIMIT 1);
		SET sender_phone = (SELECT S_UserProfilePhone FROM s_userprofile WHERE S_UserProfileS_UserID = uid AND S_UserProfileIsActive = "Y" LIMIT 1);
	END IF;
ELSE
	SET sender_name = (SELECT M_CustomerName FROM m_customer WHERE M_CustomerID = cust_id);
	SET sender_phone = (SELECT M_CustomerPhone FROM m_customer WHERE M_CustomerID = cust_id);
END IF;

SELECT S_SystemCompanyAddress, M_KelurahanName, M_DistrictName, M_CityName, M_ProvinceName, S_SystemCompanyPhone
INTO sender_address, sender_kelurahan, sender_district, sender_city, sender_province, company_phone
FROM s_system 
JOIN m_kelurahan ON S_SystemCompanyM_KelurahanID = M_KelurahanID
JOIN m_district ON M_KelurahanM_DistrictID = M_DistrictID
JOIN m_city ON M_DistrictM_CityID = M_CityID
JOIN m_province ON M_CityM_ProvinceID = M_ProvinceID
WHERE S_SystemIsActive = "Y" LIMIT 1;

IF sender_phone IS NULL THEN SET sender_phone = company_phone; END IF;

SELECT L_SoNumber order_number, L_SoDate order_date, L_SoIsDS is_ds, cust_level_code,
sender_name, sender_address, sender_kelurahan, sender_district, sender_city, sender_province, sender_phone, 
IF(L_SoIsDS = "Y", cb.M_CustomerName, ca.M_CustomerName) sh_customer_name, 
IF(L_SoIsDS = "Y", cb.M_CustomerAddress, ca.M_CustomerAddress) sh_customer_address, 
IF(L_SoIsDS = "Y", kb.M_KelurahanName, ka.M_KelurahanName) sh_customer_kelurahan, 
IF(L_SoIsDS = "Y", db.M_DistrictName, da.M_DistrictName) sh_customer_district, 
IF(L_SoIsDS = "Y", ctb.M_CityName, cta.M_CityName) sh_customer_city, 
IF(L_SoIsDS = "Y", pb.M_ProvinceName, pa.M_ProvinceName) sh_customer_province, 
IF(L_SoIsDS = "Y", IFNULL(cb.M_CustomerPhone, ''), IFNULL(ca.M_CustomerPhone, '')) sh_customer_phone, 

M_ExpeditionCode expedition_code, M_ExpeditionName expedition_name, L_SoExpService service_name, L_SoDate invoice_date,
L_SoExpCost expedition_cost,
L_SoExpOther exp_other,
L_SoExpNote exp_note,
L_SoUniqueCost	unique_cost,
L_SoTotal grand_total,
L_SoIsCOD is_cod,
M_PaymentTypeCode payment_code,
M_PaymentTypeName payment_name,
F_IpaymuTrxID ipaymu_trx_id,
F_IpaymuVia ipaymu_via,
F_IpaymuChannel ipaymu_channel,
F_IpaymuTrxCode ipaymu_trx_code,
F_IpaymuStatus ipaymu_status
	
FROM l_so
JOIN m_customer ca ON L_SoM_CustomerID = ca.M_CustomerID
JOIN m_kelurahan ka ON ca.M_CustomerM_KelurahanID = ka.M_KelurahanID
JOIN m_district da ON ka.M_KelurahanM_DistrictID = da.M_DistrictID
JOIN m_city cta ON da.M_DistrictM_CityID = cta.M_CityID
JOIN m_province pa ON cta.M_CityM_ProvinceID = pa.M_ProvinceID

LEFT JOIN m_customer cb ON L_SoDSM_CustomerID = cb.M_CustomerID
LEFT JOIN m_kelurahan kb ON cb.M_CustomerM_KelurahanID = kb.M_KelurahanID
LEFT JOIN m_district db ON kb.M_KelurahanM_DistrictID = db.M_DistrictID
LEFT JOIN m_city ctb ON db.M_DistrictM_CityID = ctb.M_CityID
LEFT JOIN m_province pb ON ctb.M_CityM_ProvinceID = pb.M_ProvinceID

JOIN m_expedition ON L_SoM_ExpeditionID	= M_ExpeditionID
JOIN m_paymenttype ON L_SoM_PaymentID = M_PaymentTypeID
LEFT JOIN f_ipaymu ON F_IpaymuL_SoID = L_SoID AND F_IpaymuIsActive = "Y"
WHERE L_SoID = soid;

SELECT L_SoDetailM_ItemID item_id,
	L_SoDetailM_ItemCode item_code,
	L_SoDetailM_ItemName item_name,	
	L_SoDetailIsPacket is_packet,
	L_SoDetailIsPrice is_price,
	L_SoDetailPrice item_price,
	L_SoDetailDisc item_disc,
	L_SoDetailDiscRp item_discrp,
	L_SoDetailDiscTotal item_disctotal,
	L_SoDetailQty item_qty,
	L_SoDetailApprovedQty item_app_qty
FROM l_sodetail
WHERE L_SoDetailIsActive = "Y" AND L_SoDetailL_SoID = soid AND L_SoDetailIsPrice = "Y" AND L_SoDetailQty > 0
ORDER BY L_SoDetailSalesCode ASC;

END