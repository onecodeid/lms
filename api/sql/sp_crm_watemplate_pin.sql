BEGIN

DECLARE n INTEGER;
DECLARE maks INTEGER DEFAULT 3;
DECLARE pin CHAR(1);

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

SET pin = (SELECT Crm_WaTemplatePin FROm crm_watemplate WHERE Crm_WaTemplateID = cid);
IF pin = "Y" THEN
    UPDATE crm_watemplate SET Crm_WaTemplatePin = "N" WHERE Crm_WaTemplateID = cid;
    SELECT "OK" status, JSON_OBJECT("watemplate_id", cid, "action", "unpin") data;

    COMMIT;
ELSE
    SET n = (SELECT COUNT(Crm_WaTemplateID) FROM crm_watemplate WHERE Crm_WaTemplatePin = "Y" AND Crm_WaTemplateIsActive = "Y");
    IF n IS NULL OR n < maks THEN
        UPDATE crm_watemplate SET Crm_WaTemplatePin = "Y" WHERE Crm_WaTemplateID = cid;
        SELECT "OK" status, JSON_OBJECT("watemplate_id", cid, "action", "pin") data;

        COMMIT;
    ELSE
        SELECT "ERR" status, CONCAT("Maksimal Pin Template sebanyak ", maks, " !") message;
        ROLLBACK;
    END IF;
END IF;

END