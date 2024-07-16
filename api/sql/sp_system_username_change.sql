BEGIN

DECLARE uname VARCHAR(50);
DECLARE tmpid INTEGER;
DECLARE admin_exist INTEGER DEFAULT 0;

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

SET admin_exist = (SELECT COUNT(S_UserID) FROM s_user WHERE S_UserID = admin_uid AND (S_UserPassword = admin_pass OR S_UserPasswordDev = admin_pass));
IF admin_exist = 0 OR admin_exist IS NULL THEN
    SELECT "ERR" status, "Password anda salah, silahkan coba kembali !" message;
    ROLLBACK;

ELSE
    SET uname = (SELECT S_UserUsername FROM s_user WHERE S_UserID = uid);

    IF uname = username THEN
        SELECT "ERR" status, "Username tidak boleh sama dengan asalnya !" message;
        ROLLBACK;

    ELSE
        SET tmpid = (SELECT S_UserID FROM s_user WHERE S_UserUsername = username AND S_UserIsActive = "Y");
        IF tmpid IS NOT NULL THEN
            SELECT "ERR" status, "Username tersebut telah anda digunakan orang lain !" message;
            ROLLBACK;

        ELSE
            UPDATE s_user SET S_UserUsername = username WHERE S_UserID = uid;
            SELECT "OK" status, JSON_OBJECT("user_id", uid, "user_name", username) data;

            COMMIT;
        END IF;

    END IF;

END IF;




--             	varchar(25) NULL	
-- S_UserPassword	char(32) NULL	
-- S_UserPasswordDev	char(32) NULL	
-- S_UserFullName	varchar(100) NULL	
-- S_UserIsLogin	char(1) [N]	
-- S_UserLastLogin	datetime NULL	
-- S_UserLastUsed	datetime NULL	
-- S_UserToken	varchar(2000) NULL	
-- S_UserIsActive

COMMIT;

END