BEGIN

DECLARE user_full_name VARCHAR(100);
DECLARE user_address VARCHAR(255);
DECLARE user_phone VARCHAR(50);
DECLARE user_email VARCHAR(50);
DECLARE user_join DATE;

DECLARE user_pass VARCHAR(50);
DECLARE group_id INTEGER;
DECLARE xid INTEGER;

DECLARE err INTEGER DEFAULT 0;

DECLARE tmp VARCHAR(255);
DECLARE l INTEGER;
DECLARE i INTEGER DEFAULT 0;
DECLARE fee_level_id INTEGER;
DECLARE fee_amount DOUBLE;
DECLARE fee_point DOUBLE;
DECLARE fee_reward DOUBLE;
DECLARE fee_id INTEGER;
DECLARE fee_ids VARCHAR(1000) DEFAULT "";

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

SET user_full_name = JSON_UNQUOTE(JSON_EXTRACT(jdata, '$.user_full_name'));
SET user_address = JSON_UNQUOTE(JSON_EXTRACT(jdata, '$.user_address'));
SET user_phone = JSON_UNQUOTE(JSON_EXTRACT(jdata, '$.user_phone'));
SET user_email = JSON_UNQUOTE(JSON_EXTRACT(jdata, '$.user_email'));
SET user_join = JSON_UNQUOTE(JSON_EXTRACT(jdata, '$.user_join'));
SET user_pass = JSON_UNQUOTE(JSON_EXTRACT(jdata, '$.user_pass'));
SET group_id = JSON_UNQUOTE(JSON_EXTRACT(jdata, '$.group_id'));

IF id = 0 THEN
	SET id = (SELECT S_UserID FROM s_user WHERE S_UserUsername = user_name AND S_UserIsActive = "Y");
	IF id IS NOT NULL THEN
		SELECT "ERR" status, "Username sudah digunakan, pilih yang lain !" message;
        SET err = 1;
	ELSE
		INSERT INTO s_user(S_UserUsername, S_UserPassword, S_UserS_UserGroupID)
		SELECT user_name, user_pass, group_id;
		
		SET id = (SELECT LAST_INSERT_ID());
		
		UPDATE s_userprofile
		SET S_UserProfileFullName = user_full_name,
			S_UserProfileAddress = user_address,
			S_UserProfilePhone = user_phone,
			S_UserProfileEmail = user_email,
			S_UserProfileJoinDate = user_join
		WHERE S_UserProfileS_UserID = id;
		
--		SELECT "OK" status, JSON_OBJECT("user_id", id, "user_name", user_name) data;
	END IF;
ELSE
		UPDATE s_userprofile
		SET S_UserProfileFullName = user_full_name,
			S_UserProfileAddress = user_address,
			S_UserProfilePhone = user_phone,
			S_UserProfileEmail = user_email,
			S_UserProfileJoinDate = user_join
		WHERE S_UserProfileS_UserID = id;
		
		IF user_pass <> "" THEN
			UPDATE s_user
			SET S_UserPassword = user_pass
			WHERE S_UserID = id;
		END IF;
		
--		SELECT "OK" status, JSON_OBJECT("user_id", id, "user_name", user_name) data;
END IF;

IF err = 0 THEN
    -- FEES
    SET l = JSON_LENGTH(fees);
    WHILE i < l DO
        SET tmp = JSON_EXTRACT(fees, CONCAT('$[', i,']'));
        SET fee_level_id = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.level_id'));
        SET fee_amount = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.amount'));
        SET fee_point = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.point'));
        SET fee_reward = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.reward'));
        
        SET fee_id = (SELECT M_Fee2ID FROM m_fee2 WHERE M_Fee2S_UserID = id AND M_Fee2IsActive = "Y" AND M_Fee2M_CustomerLevelID = fee_level_id);
        IF fee_id IS NULL THEN
            INSERt INTO m_fee2(M_Fee2S_UserID, M_Fee2M_CustomerLevelID, M_Fee2Amount, M_Fee2PointAmount, M_Fee2RewardAmount)
            SELECT id, fee_level_id, fee_amount, fee_point, fee_reward;

            SET fee_id = (SELECT LAST_INSERT_ID());
        ELSE
            UPDATE m_fee2 SET M_Fee2Amount = fee_amount, M_Fee2PointAmount = fee_point, M_Fee2RewardAmount = fee_reward
            WHERE M_Fee2ID = fee_id;
        ENd IF;
        
        SET i = i + 1;
        IF fee_ids = "" THEN SET fee_ids = fee_id; ELSE SET fee_ids = CONCAT(fee_ids, ",", fee_id); END IF;
    END WHILE;

    UPDATE m_fee2
    SET M_Fee2IsActive = "N"
    WHERE M_Fee2IsActive = "Y" AND M_Fee2S_UserID = id AND NOT FIND_IN_SET(M_Fee2ID, fee_ids);

    SELECT "OK" status, JSON_OBJECT("user_id", id, "user_name", user_name) data;
END IF;

COMMIT;

END