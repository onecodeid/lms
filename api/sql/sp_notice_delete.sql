DROP PROCEDURE `sp_notice_delete`;
DELIMITER ;;
CREATE PROCEDURE `sp_notice_delete` (IN `id` int)
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

UPDATE nt_message SET Nt_MessageIsActive = "N" WHERE Nt_MessageID = id;
UPDATE nt_recipient SET Nt_RecipientIsActive = "N" WHERE Nt_RecipientNt_MessageID = id;

SELECT "OK" as status, JSON_OBJECT("message_id", id) as data;

COMMIT;

END;;
DELIMITER ;