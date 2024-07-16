BEGIN

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

IF ttype = 'A' THEN
    UPDATE m_customer
    SET M_CustomerUserID = nuid
    WHERE M_CustomerUserID = ouid AND M_CustomerIsActive = "Y";

ELSE
    UPDATE m_customer
    SET M_CustomerUserID = nuid
    WHERE M_CustomerUserID = ouid AND M_CustomerIsActive = "Y"
    AND FIND_IN_SET(M_CustomerID, cids);

END IF;
-- IF sales = 1 THEN
--     UPDATE l_so
--     SET L_SoUserID = nuid
--     WHERE L_SoUserID = ouid AND L_SoIsActive = "Y";
-- END IF;

SELECT "OK" status, JSON_OBJECT("old_uid", ouid, "new_uid", nuid) data;

COMMIT;

END