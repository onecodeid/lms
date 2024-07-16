DROP PROCEDURE `sp_r_master_001`;
DELIMITER ;;
CREATE PROCEDURE `sp_r_master_001` (IN `uid` int, IN `provinceid` int, IN `cityid` int, IN `levelid` int)
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
	IFNULL(S_UserID, 0) user_id, IFNULL(S_UserUsername, "") user_name, IFNULL(S_UserProfileFullName, "") user_full_name
FROM m_customer
JOIN m_customerlevel ON M_CustomerM_CustomerLevelID = M_CustomerLevelID
JOIN m_kelurahan ON M_CustomerM_KelurahanID = M_KelurahanID
JOIN m_district ON M_KelurahanM_DistrictID = M_DistrictID
JOIN m_city ON M_DistrictM_CityID = M_CityID 
    AND ((M_CityID = cityid AND cityid <> 0) OR cityid = 0)
JOIN m_province ON M_CityM_ProvinceID = M_ProvinceID 
    AND ((M_ProvinceID = provinceid AND provinceid <> 0) OR provinceid = 0)
LEFT JOIN s_user ON M_CustomerUserID = S_UserID
LEFT JOIN s_userprofile ON S_UserProfileS_UserID = S_UserID AND S_UserProfileIsActive = "Y"
WHERE M_CustomerIsActive = "Y"
AND ((M_CustomerUserID = uid AND grpcode <> 'Z.GROUP.01') OR grpcode = 'Z.GROUP.01')
AND ((M_CustomerLevelID = levelid AND levelid <> 0) OR levelid = 0)
ORDER BY M_ProvinceName, M_CityName, M_CustomerCode;

END;;
DELIMITER ;