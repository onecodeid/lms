DROP PROCEDURE `sp_inventory_opname_save`;
DELIMITER ;;
CREATE PROCEDURE `sp_inventory_opname_save` (IN `hdata` varchar(2000), IN `jdata` varchar(2000), IN `uid` int)
BEGIN

DECLARE opname_date DATE;
DECLARE opname_note VARCHAR(255);
DECLARE opname_id INTEGER DEFAULT NULL;
DECLARE opname_warehouse INTEGER;

DECLARE tmp VARCHAR(255);
DECLARE l INTEGER;
DECLARE i INTEGER DEFAULT 0;
DECLARE curr_qty DOUBLE;
DECLARE item_id INTEGER;
DECLARE adjust_note VARCHAR(255);
DECLARE detail_id INTEGER;

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

IF opname_id IS NULL THEN
	SET opname_date = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.opname_date'));
	SET opname_note = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.opname_note'));
	SET opname_warehouse = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.opname_warehouse'));
	
	INSERT INTO i_opname(I_OpnameDate,
			I_OpnameNumber,
			I_OpnameM_WarehouseID,
			I_OpnameNote,
			I_OpnameS_UserID)
	SELECT opname_date, fn_numbering('OPN'), opname_warehouse, opname_note, uid;
	
	SET opname_id = (SELECT LAST_INSERT_ID());
END IF;

SET l = JSON_LENGTH(jdata);
WHILE i < l DO
	SET tmp = JSON_EXTRACT(jdata, CONCAT('$[', i,']'));
	
	SET item_id = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.item_id'));
	SET curr_qty = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.curr_qty'));
	SET adjust_note = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.adjust_note'));
		
		INSERt INTO i_opnamedetail(
			I_OpnameDetailI_OpnameID,
			I_OpnameDetailM_ItemID,
			I_OpnameDetailQtyBefore,
			I_OpnameDetailQtyCurrent,
			I_OpnameDetailQtyAdjust,
			I_OpnameDetailNote
		)
		SELECT opname_id, item_id, IFNULL(I_StockQty, 0), curr_qty, curr_qty - IFNULL(I_StockQty, 0), adjust_note
		FROM i_stock 
		WHERE I_StockM_ItemID = item_id AND I_StockIsActive = "Y";

		SET detail_id = (SELECT LAST_INSERT_ID());
		UPDATE i_stock
			SET I_StockQty = curr_qty,
			I_StockLastTransCode = 'INV.OPNAME',
			I_StockLastTransRefID = detail_id,
			I_StockLastTransQty = curr_qty - I_StockQty,
			I_StockUserID = uid
		WHERE I_StockM_ItemID = item_id
		AND I_StockIsActive = "Y"
		AND I_StockM_WarehouseID = opname_warehouse;
	
	SET i = i + 1;
END WHILE;

SELECT "OK" as status, JSON_OBJECT("opname_id", opname_id) as data;
COMMIT;

END;;
DELIMITER ;