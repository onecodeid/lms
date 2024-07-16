BEGIN

DECLARE cust_level INTEGER DEFAULT 5;
DECLARE cust_code VARCHAR(50);
DECLARE cust_name VARCHAR(100);
DECLARE cust_address VARCHAR(255);
DECLARE cust_phone VARCHAR(50);
DECLARE cust_note VARCHAR(255) DEFAULT "";
DECLARE cust_email VARCHAR(255) DEFAULT "";
DECLARE cust_postcode VARCHAR(25) DEFAULT "";
DECLARE cust_village INTEGER DEFAULT 0;

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

IF customerid = 0 THEN

    SET cust_name = JSON_UNQUOTE(JSON_EXTRACT(cdata, '$.name'));
    SET cust_address = JSON_UNQUOTE(JSON_EXTRACT(cdata, '$.address'));
    SET cust_phone = JSON_UNQUOTE(JSON_EXTRACT(cdata, '$.phone'));
    SET cust_postcode = JSON_UNQUOTE(JSON_EXTRACT(cdata, '$.postcode'));
    SET cust_village = JSON_UNQUOTE(JSON_EXTRACT(cdata, '$.kelurahan'));

    INSERT INTO m_customer(
        M_CustomerName,
        M_CustomerAddress,
        M_CustomerPhone,
        M_CustomerNote,
        M_CustomerEmail,
        M_CustomerPostCode,
        M_CustomerAutoAccept,
        M_CustomerM_CustomerLevelID,
        M_CustomerM_KelurahanID,
        M_CustomerJoinDate,
        M_CustomerUserID
    )
    SELECT cust_name, cust_address, cust_phone, cust_note, cust_email,
        cust_postcode, "Y", cust_level, cust_village, date(now()), uid;

    SET customerid = (SELECT LAST_INSERT_ID());
END IF;

UPDATE l_lead
SET L_LeadM_CustomerID = customerid
WHERE L_LeadID = leadid;

SELECT "OK" status, JSON_OBJECT("customer_id", customerid, "lead_id", leadid) data;
COMMIT;

END