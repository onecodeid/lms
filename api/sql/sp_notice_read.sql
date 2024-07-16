DROP PROCEDURE `sp_notice_read`;
DELIMITER ;;
CREATE PROCEDURE `sp_notice_read` (IN `msgid` int, IN `status` char(1), IN `uid` int, IN `seller` char(1))
BEGIN

DECLARE readid INTEGER;

IF seller = "N" THEN
    SET readid = (SELECT Nt_ReadID FROM nt_read WHERE Nt_ReadNt_MessageID = msgid AND Nt_ReadS_UserID = uid AND Nt_ReadIsactive = "Y");
ELSE
    SET readid = (SELECT Nt_ReadCustomerID FROM nt_readcustomer WHERE Nt_ReadCustomerNt_MessageID = msgid AND Nt_ReadCustomerM_CustomerID = uid AND Nt_ReadCustomerIsactive = "Y");
END IF;

IF readid IS NULL THEN
    IF seller = "N" THEN
        INSERT INTO nt_read(Nt_ReadNt_MessageID, Nt_ReadS_UserID, Nt_ReadStatus, Nt_ReadDate)
        SELECT msgid, uid, "Y", now();
    ELSE
        INSERT INTO nt_readcustomer(Nt_ReadCustomerNt_MessageID, Nt_ReadCustomerM_CustomerID, Nt_ReadCustomerStatus, Nt_ReadCustomerDate)
        SELECT msgid, uid, "Y", now();
    END IF;

    SET readid = (SELECT LAST_INSERT_ID());

ELSE
    IF seller = "N" THEN
        UPDATE nt_read SET Nt_ReadStatus = `status` WHERE Nt_ReadID = readid;
    ELSE
        UPDATE nt_readcustomer SET Nt_ReadCustomerStatus = `status` WHERE Nt_ReadCustomerID = readid;
    END IF;
END IF;

SELECT "OK" status, json_object("read_id", readid, "message_id", msgid) data;

END;;
DELIMITER ;