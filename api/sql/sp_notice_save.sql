DROP PROCEDURE `sp_notice_save`;
DELIMITER ;;
CREATE PROCEDURE `sp_notice_save` (IN `id` int, IN `hdata` TEXT)
BEGIN

DECLARE m_title VARCHAR(100);
DECLARE m_content TEXT;
DECLARE m_excerpt VARCHAR(250);
DECLARE m_img VARCHAR(255);
DECLARE m_sdate DATE;
DECLARE m_edate DATE;

DECLARE rcps VARCHAR(2000);
DECLARE rcp VARCHAR(100);
DECLARE l INTEGER;
DECLARE n INTEGER DEFAULT 0;
DECLARE rcp_id INTEGER;
DECLARE user_id INTEGER;
DECLARE group_id INTEGER;
DECLARE level_id INTEGER;

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

SET m_title = JSON_UNQUOTE(JSON_EXTRACT(hdata, "$.m_title"));
SET m_content = JSON_UNQUOTE(JSON_EXTRACT(hdata, "$.m_content"));
SET m_excerpt = JSON_UNQUOTE(JSON_EXTRACT(hdata, "$.m_excerpt"));
SET m_img = JSON_UNQUOTE(JSON_EXTRACT(hdata, "$.m_img"));
SET m_sdate = JSON_UNQUOTE(JSON_EXTRACT(hdata, "$.m_sdate"));
SET m_edate = JSON_UNQUOTE(JSON_EXTRACT(hdata, "$.m_edate"));

SET rcps = JSON_UNQUOTE(JSON_EXTRACT(hdata, "$.recipients"));
IF rcps IS NULL THEN SET rcps = "[]"; END IF;

IF id = 0 THEN

    INSERT INTO nt_message(Nt_MessageTitle, 
        Nt_MessageContent,
        Nt_MessageExcerpt, 
        Nt_MessageImage, 
        Nt_MessageStartDate, 
        Nt_MessageEndDate)
    SELECT m_title, m_content, m_excerpt, m_img, m_sdate, m_edate;

    SET id = (SELECT LAST_INSERT_ID());
ELSE
    UPDATE nt_message
    SET Nt_MessageTitle = m_title,
        Nt_MessageContent = m_content,
        Nt_MessageExcerpt = m_excerpt,
        Nt_MessageImage = m_img,
        Nt_MessageStartDate = m_sdate,
        Nt_MessageEndDate = m_edate
    WHERE Nt_MessageID = id;
END IF;

-- RECIPIENTS
UPDATE nt_recipient SET Nt_RecipientIsActive = "O"
WHERE Nt_RecipientNt_MessageID = id AND Nt_RecipientIsActive = "Y";

SET l = JSON_LENGTH(rcps);
WHILE n < l DO
    SET rcp = JSON_EXTRACT(rcps, CONCAT('$[', n, ']'));
    SET user_id = JSON_UNQUOTE(JSON_EXTRACT(rcp, '$.user_id'));
    SET group_id = JSON_UNQUOTE(JSON_EXTRACT(rcp, '$.group_id'));
    SET level_id = JSON_UNQUOTE(JSON_EXTRACT(rcp, '$.level_id'));

    SET rcp_id = (SELECT Nt_RecipientID
        FROM nt_recipient WHERE Nt_RecipientNt_MessageID = id AND Nt_RecipientIsActive = "O" 
        AND Nt_RecipientS_UserID = user_id AND Nt_RecipientS_UserGroupID = group_id
        AND Nt_RecipientM_CustomerLevelID = level_id);
    
    IF rcp_id Is NULL THEN
        INSERT INTO nt_recipient(Nt_RecipientNt_MessageID, Nt_RecipientS_UserID, Nt_RecipientS_UserGroupID, Nt_RecipientM_CustomerLevelID)
        SELECT id, user_id, group_id, level_id;
    ELSE
        UPDATE nt_recipient SET Nt_RecipientIsActive = "Y" WHERE Nt_RecipientID = rcp_id;
    END IF;

    SET n = n + 1;
END WHILE;
UPDATE nt_recipient SET Nt_RecipientIsActive = "N" WHERE Nt_RecipientNt_MessageID = id AND Nt_RecipientIsActive = "O";

-- SHA
UPDATE nt_message SET Nt_MessageSHA = SHA(CONCAT(id, "*", m_content, "*", now())) WHERE Nt_MessageID = id AND (Nt_MessageSHA IS NULL OR Nt_MessageSHA = "");

SELECT "OK" as status, JSON_OBJECT("message_id", id, "img", m_img) as data;

COMMIT;

END;;
DELIMITER ;