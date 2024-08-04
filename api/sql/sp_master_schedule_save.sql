DROP PROCEDURE `sp_master_schedule_save`;
DELIMITER ;;
CREATE PROCEDURE `sp_master_schedule_save` (IN `item_id` int, IN `jdata` text)
BEGIN

DECLARE tmp VARCHAR(255);
DECLARE l INTEGER;
DECLARE i INTEGER DEFAULT 0;
DECLARE sch_id INTEGER;
DECLARE sch_day INTEGER;
DECLARE sch_days VARCHAR(255);
DECLARE sch_time VARCHAR(10);
DECLARE sch_capacity INTEGER;

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

UPDATE m_schedule
SET m_scheduleIsActive = 'O'
wHERE M_ScheduleM_ItemID = item_id AND M_ScheduleIsActive = 'Y';

SET l = JSON_LENGTH(jdata);
WHILE i < l DO
	SET tmp = JSON_EXTRACT(jdata, CONCAT('$[', i,']'));
	SET sch_id = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.id'));
    SET sch_day = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.day'));
    SET sch_time = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.time'));
    SET sch_capacity = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.capacity'));
	SET sch_days = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.days'));
	
	IF sch_id = 0 THEN
		INSERt INTO m_schedule(M_ScheduleM_ItemID, M_ScheduleM_DayID, M_ScheduleDays, M_ScheduleTime, M_ScheduleCapacity)
		SELECT item_id, sch_day, sch_days, sch_time, sch_capacity;
	ELSE
		UPDATE m_schedule SET M_ScheduleM_DayID = sch_day, M_ScheduleDays = sch_days, M_ScheduleTime = sch_time, M_ScheduleCapacity = sch_capacity, M_ScheduleIsActive = 'Y'
		WHERE M_ScheduleID = sch_id;
	ENd IF;
	
	SET i = i + 1;
END WHILE;

UPDATE m_schedule
SET m_scheduleIsActive = 'Y'
wHERE M_ScheduleM_ItemID = item_id AND M_ScheduleIsActive = 'O';

SELECT "OK" as status;

COMMIT;

END;;
DELIMITER ;