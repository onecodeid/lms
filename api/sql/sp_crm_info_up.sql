BEGIN

DECLARE notif_id INTEGER;
DECLARE notif_uid INTEGER;
DECLARE notif_read CHAR(1);
DECLARE finished INTEGER DEFAULT 0;
DECLARE info_uid INTEGER;
DECLARE info_hash CHAR(40);

DEClARE curNotif
    CURSOR FOR
    SELECT S_UserID
    FROM s_user WHERE (FIND_IN_SET(S_UserID, ToUID) OR FIND_IN_SET(S_UserS_UserGroupID, ToGUID)) ANd S_UserIsActive = "Y"
    GROUP BY S_UserID;
		-- CURSOR FOR 
		-- 	SELECT S_NotifID, S_NotifToUID, S_NotifRead
        --     FROM s_notif
        --     WHERE S_NotifHash = phash
        --     AND S_NotifIsActive = "Y";

DECLARE CONTINUE HANDLER 
        FOR NOT FOUND SET finished = 1;

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

    SELECT Crm_InfoHash
    INTO info_hash
    FROM crm_info
    WHERE Crm_InfoID = infoid;

    UPDATE s_notif
    SET S_NotifIsActive = "O"
    WHERE S_NotifHash = info_hash
    AND S_NotifIsActive = "Y";

    OPEN curNotif;

	getNotif: LOOP
		FETCH curNotif INTO info_uid;
		IF finished = 1 THEN 
			LEAVE getNotif;
		END IF;
		
        SET notif_id = (SELECT S_NotifID
            FROM s_notif WHERE S_NotifHash = info_hash AND S_NotifIsActive = "O" AND S_NotifToUID = info_uid);
        
        IF notif_id IS NOT NULL THEN
            UPDATE s_notif 
            JOIN crm_info ON Crm_InfoID = infoid
            SET S_NotifIsActive = "Y",
                S_NotifMessage = Crm_InfoExcerpt,
                S_NotifMessageFull = Crm_InfoContent 
            WHERE S_NotifID = notif_id;
        ELSE

            INSERT INTO s_notif(
                S_NotifToUID,
                S_NotifM_NotifID,
                S_NotifMessage,
                S_NotifMessageFull,
                S_NotifPopup,
                S_NotifPopupSdate,
                S_NotifPopupEdate,
                S_NotifAction,
                S_NotifActionProp,
                S_NotifHash
            )

            SELECT info_uid, 0, Crm_InfoExcerpt, Crm_InfoContent, "Y", Crm_InfoUpStartDate, Crm_InfoUpEndDate, "POPUP-INFO", concat("crm-info/detail/", Crm_InfoID), info_hash
            FROM crm_info
            WHERE Crm_InfoID = infoid;
        END IF;
		
	END LOOP getNotif;
	CLOSE curNotif;

    UPDATE s_notif
    SET S_NotifIsActive = "N"
    WHERE S_NotifHash = info_hash
    AND S_NotifIsActive = "O";

    SELECT "OK" status, JSON_OBJECT("info_id", infoid) data;

COMMIT;

END