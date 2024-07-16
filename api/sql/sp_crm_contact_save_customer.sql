BEGIN

DECLARE code VARCHAR(25);
DECLARE name VARCHAR(100);
DECLARE address VARCHAR(255);
DECLARE kelurahanid INTEGER;
DECLARE districtid INTEGER;
DECLARE cityid INTEGER;
DECLARE email VARCHAR(100);
DECLARE note VARCHAR(255);

DECLARE l INTEGER;
DECLARE n INTEGER DEFAULT 0;
DECLARE tmp VARCHAR(100);
DECLARE prefix VARCHAR(10);
DECLARE numbr VARCHAR(25);
DECLARE tag VARCHAR(100);
DECLARE tagid INTEGER;

DECLARE EXIT HANDLER FOR SQLEXCEPTION
BEGIN

GET DIAGNOSTICS CONDITION 1
@p1 = RETURNED_SQLSTATE, @p2 = MESSAGE_TEXT, @tb = TABLE_NAME;
SELECT "ERR" as status, @p1 as data  , @p2 as message, @tb as table_name;
ROLLBACK;
INSERT INTO `one-sales-log`.log_err(Log_ErrRefID,Log_ErrRefNumber,Log_ErrEvent,Log_ErrNote)
SELECT so_h_id,"","SO Confirm", CONCAT("ERR | ", @p1, " ", @p2);
END;

DECLARE EXIT HANDLER FOR SQLWARNING
BEGIN

GET DIAGNOSTICS CONDITION 1
@p1 = RETURNED_SQLSTATE, @p2 = MESSAGE_TEXT, @tb = TABLE_NAME;
SELECT "ERR" as status, @p1 as data  , @p2 as message, @tb as table_name;
ROLLBACK;
INSERT INTO `one-sales-log`.log_err(Log_ErrRefID,Log_ErrRefNumber,Log_ErrEvent,Log_ErrNote)
SELECT so_h_id,"","SO Confirm", CONCAT("WARNING | ", @p1, " ", @p2);
END;

START TRANSACTION;

SET code = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.code'));
SET name = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.name'));
SET address = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.address'));
SET kelurahanid = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.kelurahanid'));
SET districtid = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.districtid'));
SET cityid = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.cityid'));
SET email = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.email'));
SET note = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.note'));

IF id = 0 THEN
    INSERT INTO crm_contact(
        Crm_ContactCode,
        Crm_ContactName,
        Crm_ContactAddress,
        Crm_ContactM_KelurahanID,
        Crm_ContactM_DistrictID,
        Crm_ContactM_CityID,
        Crm_ContactEmail,
        Crm_ContactNote
    )
    SELECT code,
        name, address, IFNULL(kelurahanid, 0),
        IFNULL(districtid, 0), cityid, email, note;

    SET id = (SELECT LAST_INSERT_ID());

    SET l = JSON_LENGTH(phones);
    WHILE n < l DO
        SET tmp = JSON_EXTRACT(phones, CONCAT('$[', n, ']'));
        SET prefix = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.prefix'));
        SET numbr = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.number'));

        INSERT INTO crm_contactphone(
            Crm_ContactPhoneCrm_ContactID,
            Crm_ContactPhoneCountryCode,
            Crm_ContactPhoneNumber
        )
        SELECT id, prefix, numbr;

        SET n = n + 1;
    END WHILE;

    SET l = JSON_LENGTH(tags);
    SET n = 0;
    WHILE n < l DO
        SET tag = JSON_EXTRACT(tags, CONCAT('$[', n, ']'));
        SET tagid = (SELECT Crm_TagID FROM crm_tag WHERE Crm_TagLabel = tag AND Crm_TagIsActive = "Y");

        IF tagid IS NULL THEN
            INSERt INTO crm_tag(Crm_TagLabel) SELECT tag;
            SET tagid = (SELECT LAST_INSERT_ID());
        END IF;

        INSERT INTO crm_contacttag(
            Crm_ContactPhoneCrm_ContactID,
            Crm_ContactPhoneCrm_TagID
        )
        SELECT id, tagid;

        SET n = n + 1;
    END WHILE;
END IF;

SELECT "OK" status, JSON_OBJECT("contact_id", id) data;

COMMIT;

END