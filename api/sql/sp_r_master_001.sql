DROP PROCEDURE `sp_r_master_001`;
DELIMITER ;;
CREATE PROCEDURE `sp_r_master_001` (IN `uid` int, IN `itemid` int, IN `levelid` int)
BEGIN

DECLARE grpcode VARCHAR(25);
SET grpcode = (SELECT S_UserGroupCode FROM s_user JOIN s_usergroup ON S_UserS_UserGroupID = S_UserGroupID WHERE S_UserID = uid);

IF uid = 0 THEN
SET uid = 1;
END IF;

SELECT * FROM s_user WHERE S_UserID = uid;

SELECT M_ProvinceID province_id, M_ProvinceName province_name, M_CityID city_id, M_CityName city_name,
	M_DistrictID district_id, M_DistrictName district_name, M_KelurahanID kelurahan_id, M_KelurahanName kelurahan_name,
	M_CustomerID customer_id, M_CustomerCode customer_code, M_CustomerName customer_name,
	M_CustomerAddress customer_address, M_CustomerPhone customer_phone, M_CustomerPhone2 customer_phone2, 
	M_CustomerPhone3 customer_phone3, M_CustomerEmail customer_email, M_CustomerJoinDate customer_joindate,
	M_CustomerLevelID level_id, M_CustomerLevelCode level_code, M_CustomerLevelName level_name,
	IFNULL(S_UserID, 0) user_id, IFNULL(S_UserUsername, "") user_name, IFNULL(S_UserProfileFullName, "") user_full_name,

	M_ITemID item_id, M_ItemName item_name, IFNULL(d1.M_DayNameLocalized, d2.M_DayNAmeLocalized) sch_day, IFNULL(L_SoDetailScheduleTime, '-') sch_time
FROM m_customer
JOIN l_so ON L_SoM_CustomerID = M_CustomerID AND L_SoIsactive = "Y"
JOIN l_sodetail ON L_SoDetailL_SoID = L_SoID AND L_SoDetailIsActive = "Y"
	AND ((L_SoDetailM_Itemid = itemid AND itemid <> 0) OR itemid = 0)

LEFT JOIN m_schedule ON L_SoDetailM_ScheduleID = M_ScheduleID
LEFT JOIN m_day d1 ON M_ScheduleM_DayID = d1.M_DayID
LEFT JOIN m_day d2 ON L_SoDetailM_DayID = d2.M_DayID
JOIN m_item ON L_SoDetailM_ItemID = M_ItemID
JOIN m_customerlevel ON M_CustomerM_CustomerLevelID = M_CustomerLevelID
left JOIN m_kelurahan ON M_CustomerM_KelurahanID = M_KelurahanID
left JOIN m_district ON M_KelurahanM_DistrictID = M_DistrictID
left JOIN m_city ON M_DistrictM_CityID = M_CityID 
--    AND ((M_CityID = cityid AND cityid <> 0) OR cityid = 0)
left JOIN m_province ON M_CityM_ProvinceID = M_ProvinceID 
--    AND ((M_ProvinceID = provinceid AND provinceid <> 0) OR provinceid = 0)
LEFT JOIN s_user ON M_CustomerUserID = S_UserID
LEFT JOIN s_userprofile ON S_UserProfileS_UserID = S_UserID AND S_UserProfileIsActive = "Y"
WHERE M_CustomerIsActive = "Y"
AND ((M_CustomerUserID = uid AND grpcode <> 'Z.GROUP.01') OR grpcode = 'Z.GROUP.01')
AND ((M_CustomerLevelID = levelid AND levelid <> 0) OR levelid = 0)
ORDER BY M_ProvinceName, M_CityName, M_CustomerCode;

END;;
DELIMITER ;