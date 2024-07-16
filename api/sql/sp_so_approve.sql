DROP PROCEDURE `sp_so_approve`;
DELIMITER ;;
CREATE PROCEDURE `sp_so_approve` (IN `hdata` varchar(2000), IN `jdata` varchar(2000), IN `uid` int)
BEGIN

DECLARE tmp VARCHAR(255);
DECLARE l INTEGER;
DECLARE i INTEGER DEFAULT 0;
DECLARE so_id INTEGER;
DECLARE so_qty DOUBLE;
DECLARE so_h_id INTEGER DEFAULT 0;
DECLARE customer_id INTEGER;

DECLARE y INTEGER;

DECLARE expedition_id INTEGER;
DECLARE weight_add DOUBLE;
DECLARE servicex VARCHAR(25);
DECLARE cost DOUBLE;

DECLARE coupon_id INTEGER;
DECLARE coupon_amount DOUBLE;
DECLARE coupon_note VARCHAR(255);
DECLARE coupon_x_id INTEGER;

DECLARE unstock VARCHAR(2000) DEFAULT '';

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

SET expedition_id = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.expedition_id'));
SET servicex = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.service'));
SET cost = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.cost'));
SET weight_add = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.weight_add'));

SET coupon_id = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.coupon_id'));
SET coupon_amount = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.coupon_amount'));
SET coupon_note = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.coupon_note'));

IF coupon_id IS NULL THEN SET coupon_id = 0; END IF;
IF coupon_amount IS NULL THEN SET coupon_amount = 0; END IF;
IF coupon_note IS NULL THEN SET coupon_note = ""; END IF;

SET l = JSON_LENGTH(jdata);
WHILE i < l DO
	SET tmp = JSON_EXTRACT(jdata, CONCAT('$[', i,']'));
	SET so_id = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.id'));
	SET so_qty = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.qty'));
	
	UPDATE l_sodetail SET L_SoDetailApprovedQty = so_qty, L_SoDetailApproved = "Y", L_SoDetailApprovedDate = now(), L_SoDetailApprovedUserID = uid
	WHERE L_SoDetailID = so_id;
	
	IF so_h_id = 0 THEN
		SET so_h_id = (SELECT L_SoDetailL_SoID FROM l_sodetail WHERE L_SoDetailID = so_id);
	END IF;
	
	SET i = i + 1;
END WHILE;

UPDATE l_sodetail SET L_SoDetailApproved = "X", L_SoDetailApprovedDate = now(), L_SoDetailApprovedUserID = uid WHERE L_SoDetailL_SoID = so_h_id AND L_SoDetailApproved = "N" AND L_SoDetailIsActive = "Y";

SET y = (SELECT COUNT(L_SoDetailID) FROM l_sodetail WHERE L_SoDetailApproved = "Y" AND L_SoDetailIsActive = "Y");
IF y > 0 THEN
	UPDATE l_so SET L_SoApproved = "Y" WHERE L_SoID = so_id;
ELSE
	UPDATE l_so SET L_SoApproved = "X" WHERE L_SoID = so_id;
END IF;


UPDATE l_so 
JOIN m_orderstatus ON M_OrderStatusCode = "SO.Approved" AND M_OrderStatusIsActive = "Y"
	SET L_SoAddWeight = weight_add,
	L_SoM_ExpeditionID = expedition_id,
	L_SoExpService = servicex,
	L_SoExpCost	= cost,
	L_SoCouponAmount = coupon_amount,
	L_SoM_OrderStatusID = M_OrderStatusID
WHERE L_SoID = so_h_id AND L_SoM_OrderStatusID < 3;


IF coupon_id <> 0 AND coupon_amount <> 0 THEN
	SET coupon_x_id = (SELECT L_CouponID FROM l_coupon WHERE L_CouponL_SoID = so_id AND L_CouponIsActive = "Y");
	IF coupon_x_id IS NOT NULL THEN
		UPDATE l_coupon 
		JOIN m_coupon ON M_CouponID = coupon_id
		JOIN m_coupontype ON M_CouponM_CouponTypeID = M_CouponTypeID
		SET L_CouponM_CouponID = coupon_id, L_CouponAmount = coupon_amount, L_CouponCode = M_CouponCode, L_CouponNote = coupon_note, L_CouponType = M_CouponTypeCode
		WHERE L_CouponID = coupon_x_id;
	ELSE
		INSERT INTO l_coupon(L_CouponL_SoID, 
							L_CouponM_CouponID,
							L_CouponCode,
							L_CouponType,
							L_CouponAmount,
							L_CouponNote)
		SELECT so_id, coupon_id, M_CouponCode, M_CouponTypeCode, coupon_amount, coupon_amount
		FROM m_coupon
		JOIN m_coupontype ON M_CouponM_CouponTypeID = M_CouponTypeID
		WHERE M_CouponID = coupon_id;
	END IF;
ELSE
	UPDATE l_coupon SET L_CouponIsActive = "N" WHERE L_CouponL_SoID = so_id;
END IF;


SET unstock = (SELECT GROUP_CONCAT(item_namex SEPARATOR ", ") FROM
				(SELECT L_SoDetailM_ItemName item_namex, SUM(L_SoDetailApprovedQty) detail_qty, IFNULL(I_StockQty, 0) stock_qty
				FROM l_sodetail
				LEFT JOIN i_stock ON L_SoDetailM_ItemID = I_StockM_ItemID AND I_StockIsActive = "Y" AND I_StockM_WarehouseID = 2
				WHERE L_SoDetailL_SoID = so_h_id AND L_SoDetailIsActive = "Y" AND L_SoDetailIsPacket = "N"
					AND L_SoDetailApproved = "Y"
				GROUP BY L_SoDetailM_ItemID
				HAVING detail_qty > stock_qty) x);

-- SET CUSTOMER STATUS IS NEW / REPEAT
SET customer_id = (SELECT L_SoM_CustomerID FROM l_so WHERE L_SoID = so_h_id);
CALL sp_master_customer_isnew(customer_id);
-- END OF CUSTOMER STATUS
				
IF unstock IS NOT NULL THEN
	SELECT "ERR" status, unstock data, CONCAT("Item ", unstock, " tidak mencukupi stoknya") message;
	ROLLBACK;
ELSE
	CALL sp_so_confirm(so_h_id);
	SELECT "OK" as status, so_h_id as data;
	COMMIT;
END IF;



END;;
DELIMITER ;