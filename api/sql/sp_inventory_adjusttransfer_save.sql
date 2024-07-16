DROP PROCEDURE `sp_inventory_adjusttransfer_save`;
DELIMITER ;;
CREATE PROCEDURE `sp_inventory_adjusttransfer_save` (IN `ptype` varchar(10), IN `hdata` text, IN `uid` int)
BEGIN

DECLARE p_warehouse_from INTEGER;
DECLARE p_warehouse_to INTEGER;
DECLARE p_qty DOUBLE;
DECLARE p_item INTEGER;
DECLARE id INTEGER;

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

SET p_warehouse_from = JSON_UNQUOTE(JSON_EXTRACT(hdata, "$.warehouse_from"));
SET p_warehouse_to = JSON_UNQUOTE(JSON_EXTRACT(hdata, "$.warehouse_to"));
SET p_qty = JSON_UNQUOTE(JSON_EXTRACT(hdata, "$.qty"));
SET p_item = JSON_UNQUOTE(JSON_EXTRACT(hdata, "$.item"));

IF p_qty <= 0 THEN

    SELECT "ERR" as status, "Qty tidak boleh 0 atau kurang !" as message;
    ROLLBACK;

ELSEIF ptype = 'ADJUST' THEN
    INSERT INTO i_adjusttransfer(
        I_AdjustTransferDate,
        I_AdjustTransferType,
        I_AdjustTransferFromM_WarehouseID,
        I_AdjustTransferFromBeforeQty,
        I_AdjustTransferFromAfterQty,
        I_AdjustTransferQty,
        I_AdjustTransferToM_WarehouseID,
        I_AdjustTransferToBeforeQty,
        I_AdjustTransferToAfterQty,
        I_AdjustTransferUID
    ) SELECT date(now()), ptype, p_warehouse_from, a.I_StockQty, a.I_StockQty + p_qty, p_qty, 
        0, 0, 0, uid
    FROM i_stock a
    WHERE a.I_StockM_ItemID = p_item AND a.I_StockM_WarehouseID = p_warehouse_from AND a.I_StockIsActive = "Y";

    SET id = (SELECT LAST_INSERT_ID());

    UPDATE i_stock
    SET I_StockQty = I_StockQty + p_qty, I_StockLastTransRefID = id, I_StockLastTransCode = "INV.ADJUSTMENT.2", I_StockLastTransQty = p_qty
    WHERE I_StockM_ItemID = p_item AND I_StockM_WarehouseID = p_warehouse_from AND I_StockIsActive = "Y";

    SELECT "OK" as status, JSON_OBJECT("adjusttransfer_id", id) as data;
    COMMIT;

ELSEIF ptype = 'TRANSFER' THEN

    INSERT INTO i_adjusttransfer(
        I_AdjustTransferDate,
        I_AdjustTransferType,
        I_AdjustTransferFromM_WarehouseID,
        I_AdjustTransferFromBeforeQty,
        I_AdjustTransferFromAfterQty,
        I_AdjustTransferQty,
        I_AdjustTransferToM_WarehouseID,
        I_AdjustTransferToBeforeQty,
        I_AdjustTransferToAfterQty,
        I_AdjustTransferUID
    ) SELECT date(now()), ptype, p_warehouse_from, a.I_StockQty, a.I_StockQty - p_qty, p_qty, 
        p_warehouse_to, b.I_StockQty, b.I_StockQty + p_qty, uid
    FROM i_stock a 
    JOIN i_stock b ON a.I_StockM_itemID = b.I_StockM_ItemID AND b.I_StockM_WarehouseID = p_warehouse_to AND b.I_StockIsActive = "Y"
    WHERE a.I_StockM_ItemID = p_item AND a.I_StockM_WarehouseID = p_warehouse_from AND a.I_StockIsActive = "Y";

    SET id = (SELECT LAST_INSERT_ID());

    UPDATE i_stock
    SET I_StockQty = I_StockQty - p_qty, I_StockLastTransRefID = id, I_StockLastTransCode = "INV.TRANSFER", I_StockLastTransQty = (0 - p_qty)
    WHERE I_StockM_ItemID = p_item AND I_StockM_WarehouseID = p_warehouse_from AND I_StockIsActive = "Y";

    UPDATE i_stock
    SET I_StockQty = I_StockQty + p_qty, I_StockLastTransRefID = id, I_StockLastTransCode = "INV.TRANSFER", I_StockLastTransQty = p_qty
    WHERE I_StockM_ItemID = p_item AND I_StockM_WarehouseID = p_warehouse_to AND I_StockIsActive = "Y";

    SELECT "OK" as status, JSON_OBJECT("adjusttransfer_id", id) as data;
    COMMIT;

END IF;

END;;
DELIMITER ;