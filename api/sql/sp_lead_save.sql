DROP PROCEDURE `sp_lead_save`;
DELIMITER ;;
CREATE PROCEDURE `sp_lead_save` (IN `lead_id` int, IN `hdata` text, IN `jdata` text, IN `uid` integer)
BEGIN


DECLARE lead_name VARCHAR(100);
DECLARE lead_address VARCHAR(255);
DECLARE lead_city_id INTEGER;
DECLARE lead_postcode VARCHAR(10);
DECLARE lead_phone VARCHAR(25);
DECLARE lead_note VARCHAR(255);
DECLARE lead_problem VARCHAR(255);
DECLARE lead_customer INTEGER;
DECLARE lead_greeting INTEGER;
DECLARE lead_preclose INTEGER;
DECLARE lead_close CHAR(1);
DECLARE lead_adsnumber VARCHAR(50);
DECLARE lead_id INTEGER;

DECLARE lead_source INTEGER;
DECLARE lead_type INTEGER;

DECLARE lead_date DATE;
DECLARE lead_number VARCHAR(25);
DECLARE tmp VARCHAR(500);
DECLARE l INTEGER;
DECLARE i INTEGER DEFAULT 0;

DECLARE fus TEXT;
DECLARE fu_id INTEGER;
DECLARE fu_date DATE;
DECLARE fu_note VARCHAR(2000);
DECLARE fu_close CHAR(1);
DECLARE fu_preclose INTEGER;

DECLARE detail_id INTEGER;
DECLARE item_id INTEGER;
DECLARE item_price DOUBLE;
DECLARE item_name VARCHAR(100);
DECLARE is_packet CHAR(1);

DECLARE EXIT HANDLER FOR SQLEXCEPTION
BEGIN

GET DIAGNOSTICS CONDITION 1
@p1 = RETURNED_SQLSTATE, @p2 = MESSAGE_TEXT, @tb = TABLE_NAME;
SELECT "ERR" as status, @p1 as data  , @p2 as message, @tb as table_name;

ROLLBACK;
END;

DECLARE EXIT HANDLER FOR SQLWARNING
BEGIN

GET DIAGNOSTICS CONDITION 1
@p1 = RETURNED_SQLSTATE, @p2 = MESSAGE_TEXT, @tb = TABLE_NAME;
SELECT "ERR" as status, @p1 as data  , @p2 as message, @tb as table_name;

ROLLBACK;
END;

START TRANSACTION;

IF uid = -1 THEN
SET uid = (SELECT fn_system_user_get_rotate());
END IF;


SET lead_id = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.lead_id'));
SET lead_name = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.lead_name'));
SET lead_address = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.lead_address'));
SET lead_phone = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.lead_phone'));
SET lead_postcode = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.lead_postcode'));
SET lead_city_id = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.lead_city_id'));
SET lead_source = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.lead_source'));
SET lead_type = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.lead_type'));
SET lead_note = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.lead_note'));
SET lead_problem = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.lead_problem'));
SET lead_customer = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.lead_customer'));
SET lead_greeting = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.lead_greeting'));
SET lead_preclose = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.lead_preclose'));
SET lead_close = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.lead_close'));
SET lead_adsnumber = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.lead_adsnumber'));
SET fus = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.followups'));

IF lead_customer IS NULL THEN SET lead_customer = 0; END IF;
IF lead_problem IS NULL THEN SET lead_problem = "[]"; END IF;

IF lead_id IS NULL OR lead_id = 0 THEN
    SET lead_date = date(now());
    SET lead_number = (SELECT fn_numbering('LEAD'));

	INSERT INTO l_lead(
        L_LeadDate,
        L_LeadNumber,
        L_LeadM_LeadSourceID,
        L_LeadM_LeadTypeID,
        L_LeadAdsNumber,
        L_LeadName,
        L_LeadAddress,
        L_LeadM_KelurahanID,
        L_LeadM_DistrictID,
        L_LeadM_CityID,
        L_LeadPhone,
        L_LeadNote,
        L_LeadProblems,
        L_LeadM_CustomerID,
        L_LeadUserID,
        L_LeadGreetingM_LeadAttrID,
        L_LeadPrecloseM_LeadAttrID,
        L_LeadClose,
        L_LeadCloseDate
    )
    
	SELECT lead_date, lead_number, lead_source, lead_type, lead_adsnumber, lead_name, lead_address, 
        0, 0, lead_city_id, lead_phone, lead_note, lead_problem, lead_customer, uid, 
        lead_greeting, lead_preclose, lead_close, IF(lead_close='Y',now(),null);

	SET lead_id = (SELECT LAST_INSERT_ID());

ELSE
    UPDATE l_lead
    SET L_LeadM_LeadSourceID = lead_source,
        L_LeadM_LeadTypeID = lead_type,
        L_LeadAdsNumber = lead_adsnumber,
        L_LeadName = lead_name,
        L_LeadAddress = lead_name,
        L_LeadM_KelurahanID = 0,
        L_LeadM_DistrictID = 0,
        L_LeadM_CityID = lead_city_id,
        L_LeadPhone = lead_phone,
        L_LeadNote = lead_note,
        L_LeadGreetingM_LeadAttrID = lead_greeting,
        L_LeadProblems = lead_problem,
        L_LeadPrecloseM_LeadAttrID = lead_preclose,
        L_LeadClose = lead_close,
        L_LeadCloseDate = IF(lead_close='Y',now(),null)
    WHERE L_LeadID = lead_id;

    SET lead_number = (SELECT L_LeadNumber FROM l_lead WHERE L_LeadID = lead_id);
END IF;


UPDATE l_leaddetail
SET L_LeadDetailIsActive = "O"
WHERE L_LeadDetailL_LeadID = lead_id ANd L_LeadDetailIsActive = "Y";

SET l = JSON_LENGTH(jdata);
WHILE i < l DO
    SET tmp = JSON_EXTRACT(jdata, CONCAT('$[', i, ']'));
    SET item_id = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.item_id'));
    SET item_price = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.item_price'));
    SET item_name = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.item_name'));
    SET is_packet = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.is_packet'));

    SET detail_id = (SELECT L_LeadDetailID FROM l_leaddetail WHERE L_LeadDetailIsActive = "O" 
                        AND L_LeadDetailM_ItemID = item_id AND L_LEadDetailL_LeadID = lead_id);
    IF detail_id IS NULL THEN
        INSERT INTO l_leaddetail(
            L_LeadDetailL_LeadID,
            L_LeadDetailM_ItemID,
            L_LeadDetailM_PacketID,
            L_LeadDetailIsPacket,
            L_LeadDetailM_ItemName,
            L_LeadDetailM_ItemPrice
        )
        SELECT lead_id, IF(is_packet = "Y", 0, item_id), IF(is_packet = "Y", item_id, 0), is_packet, item_name, item_price; 
    ELSE
        UPDATE l_leaddetail
        SET L_LeadDetailM_ItemName = item_name,
            L_LeadDetailM_ItemPrice = item_price,
            L_LeadDetailIsActive = "Y"
        WHERE L_LeadDetailID = detail_id;
    END IF;

    SET i = i + 1;
END WHILE;

UPDATE l_leaddetail
SET L_LeadDetailIsActive = "N"
WHERE L_LeadDetailIsActive = "O" and L_LeadDetailL_LeadID = lead_id;

-- FOLLOWUPS
UPDATE l_fu
SET L_FuIsActive = "O"
WHERE L_FuL_LeadID = lead_id ANd L_FuIsActive = "Y";

SET l = JSON_LENGTH(fus);
SET i = 0;
WHILE i < l DO
    SET tmp = JSON_EXTRACT(fus, CONCAT('$[', i, ']'));
    SET fu_id = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.fu_id'));
    SET fu_date = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.fu_date'));
    SET fu_note = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.fu_note'));
    SET fu_close = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.fu_close'));
    SET fu_preclose = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.fu_preclose'));

    IF fu_id = 0 THEN
        INSERT INTO l_fu(
            L_FuDate,
            L_FuType,
            L_FuL_LeadID,
            L_FuNote,
            L_FuClose,
            L_FuPrecloseM_LeadAttrID,
            L_FuUID)
        SELECT fu_date, 'L', lead_id, fu_note, fu_close, fu_preclose, uid;
    ELSE
        UPDATE l_fu
            SET L_FuDate = fu_date, L_FuNote = fu_note, L_FuClose = fu_close, L_FuPrecloseM_LeadAttrID = fu_preclose, L_FuIsActive = "Y"
            WHERE L_FuID = fu_id;
    END IF;

    SET i = i + 1;

    IF lead_close = "N" AND fu_close = "Y" THEN
        UPDATE l_lead SET L_LeadClose = "Y" WHERE L_LeadID = lead_id;
    END IF;
END WHILE;

UPDATE l_fu
SET L_FuIsActive = "N"
WHERE L_FuL_LeadID = lead_id ANd L_FuIsActive = "O";
-- END OF FOLLOWUPS


SELECT "OK" status, JSON_OBJECT("lead_id", lead_id, "lead_number", lead_number) data;
COMMIT;

END;;
DELIMITER ;