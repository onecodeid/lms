BEGIN

DECLARE shareid INTEGER;
DECLARE gpoint INTEGER;
DECLARE tokenid INTEGER;

DECLARE rst TEXT;
DECLARE lmt INTEGER DEFAULT 25;
DECLARE ttl INTEGER;
DECLARE pend INTEGER;

SET tokenid = (SELECT Z_TokenID FROM z_token WHERE Z_TokenCode = token AND Z_TokenCustomerID = customerid ANd Z_TokenUsed = "N" ANd Z_TokenISactive= "Y");
SET tokenid = 0;
IF tokenid IS NULL THEN
    SELECT "ERR" status, "Token not recognized" message;
ELSE
    UPDATE z_token SET Z_TokenUsed = "Y", Z_TokenUsedDate = now() WHERE Z_TokenID = tokenid;

    SET rst = (SELECT CONCAT("[", GROUP_CONCAT(JSON_OBJECT("id", id, "date", `date`, "number", `number`, "courier", courier, "ds", ds, "ds_note", ds_note, "status", status, "status_note", status_note, "amount", amount) SEPARATOR ","), "]")
        FROM (
        SELECT L_SoID id, DATE_FORMAT(L_SoDate, "%d %b %Y") `date`, L_SoNumber `number`, CONCAT(M_ExpeditionName, ", ", IFNULL(L_SoExpService, "")) courier, L_SoIsDS ds, IFNULL(CONCAT(cb.M_CustomerName, ", ", tb.M_CityName), "") ds_note, M_OrderStatusCode `status`, M_OrderStatusSellerName `status_note`, L_SoTotal `amount`
        FROM l_so
        JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID
        JOIN m_customer ca ON L_SoM_CustomerID = ca.M_CustomerID
        JOIN m_expedition ON L_SoM_ExpeditionID = M_ExpeditionID
        LEFT JOIN m_customer cb ON L_SoDSM_CustomerID = cb.M_CustomerID
        LEFT JOIN m_kelurahan kb ON cb.M_CustomerM_KelurahanID = kb.M_KelurahanID
        LEFT JOIN m_district db ON kb.M_KelurahanM_DistrictID = db.M_DistrictID
        LEFT JOIN m_city tb ON db.M_DistrictM_CityID = tb.M_CityID
        WHERE L_SoM_CustomerID = customerid AND L_SoIsActive = "Y"
        ORDER BY L_SoNumber DESC
        LIMIT lmt OFFSET pstart ) a);
    SET pend = JSON_LENGTH(rst);
    IF pend IS NULL THEN SET pend = 0; ENd If;

    SET ttl = (SELECT COUNT(L_SoID) FROM l_so
        JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID
        JOIN m_customer ca ON L_SoM_CustomerID = ca.M_CustomerID
        JOIN m_expedition ON L_SoM_ExpeditionID = M_ExpeditionID
        WHERE L_SoM_CustomerID = customerid AND L_SoIsActive = "Y");

    SELECT "OK" status, CONCAT('{"data":', rst, ', "total":', ttl,', "end":', (pend+pstart), '}') data;

END IF;

END