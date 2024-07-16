BEGIN

DECLARE level_code VARCHAR(50);
DECLARE level_id INTEGER;
DECLARE degrade_level_code VARCHAR(50);
DECLARE degrade_level_id INTEGER;

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

SET level_id = (SELECT M_CustomerM_CustomerLevelID FROM m_customer WHERE M_CustomerID = customerid);
SET level_code = (SELECT M_CustomerLevelCode FROM m_customerlevel WHERE M_CustomerLevelID = level_id);

IF level_code = "CUST.DISTRIBUTOR" OR level_code = "CUST.AGENCY" OR level_code = "CUST.RESELLER" THEN
    IF level_code = "CUST.DISTRIBUTOR" THEN
        SET degrade_level_code = "CUST.AGENCY";
    ELSEIF level_code = "CUST.AGENCY" THEN
        SET degrade_level_code = "CUST.RESELLER";
    ELSEIF level_code = "CUST.RESELLER" THEN
        SET degrade_level_code = "CUST.ENDUSER";        
    END IF;

    SET degrade_level_id = (SELECT M_CustomerLevelID FROM m_customerlevel WHERE M_CustomerLevelCode = degrade_level_code);

    UPDATE m_customer SET M_CustomerM_CustomerLevelID = degrade_level_id WHERE M_CustomerId = customerid;

    insert into `one-sales-log`.log_customer(Log_CustomerDate,
    Log_CustomerM_CustomerID,
    Log_CustomerM_CustomerLevelID,
    Log_CustomerNote)
    select date(m_customerbasedate), m_customerid, b.m_customerlevelid,
    CONCAT("Degradasi dari level ", upper(a.m_customerlevelname), " ke level ", b.m_customerlevelname)
    from m_customer
    join m_customerlevel a on a.m_customerlevelid = level_id
    join m_customerlevel b on b.m_customerlevelid = degrade_level_id
    where m_customerid = customerid;

    UPDATE m_customer SET M_CustomerBaseDate = date(now()) where M_CustomerID = customerid;

    SELECT "OK" status, JSON_OBJECT("level_id", level_id, "to_level_id", degrade_level_id) data;
ELSE
    SELECT "ERR" status, "System Error" message;
END IF;
COMMIT;

END