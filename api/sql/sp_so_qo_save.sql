DROP PROCEDURE `sp_so_qo_save`;
DELIMITER ;;
CREATE PROCEDURE `sp_so_qo_save` (IN `order_id` int, IN `cdata` text, IN `hdata` text, IN `jdata` text, IN `uid` integer)
BEGIN


DECLARE cust_name VARCHAR(100);
DECLARE cust_address VARCHAR(255);
DECLARE cust_kelurahan_id INTEGER;
DECLARE cust_postcode VARCHAR(10);
DECLARE cust_phone VARCHAR(25);
DECLARE cust_level_id INTEGER;
DECLARE cust_id INTEGER;
DECLARE cust_email VARCHAR(255);
DECLARE cust_sex CHAR(1);

DECLARE order_number VARCHAR(25);
DECLARE tmp VARCHAR(500);
DECLARE l INTEGER;
DECLARE i INTEGER DEFAULT 0;

DECLARE item_id INTEGER;
DECLARE item_qty DOUBLE;
DECLARE item_price DOUBLE;
DECLARE item_disc DOUBLE;
DECLARE item_discrp DOUBLE;
DECLARE item_name VARCHAR(100);
DECLARE sch_id INTEGER;
DECLARE sch_day INTEGER;
DECLARE sch_time VARCHAR(10);
DECLARE is_packet CHAR(1);
DECLARE level_id INTEGER;

DECLARE expedition_id INTEGER;
DECLARE courier_cost DOUBLE;
DECLARE ex_other VARCHAR(100);
DECLARE ex_note VARCHAR(255);
DECLARE ex_code VARCHAR(100);
DECLARE payment_id INTEGER;
DECLARE payment_code VARCHAR(25);
DECLARE channel VARCHAR(25);
DECLARE servicex VARCHAR(100);
DECLARE is_ds CHAR(1);
DECLARE ds_customer_id INTEGER;
DECLARE order_note VARCHAR(255);
DECLARE leadsource INTEGER;

DECLARE unstock VARCHAR(2000) DEFAULT '';
DECLARE cstock DOUBLE default 0;

DECLARE coupon_id INTEGER;
DECLARE coupon_code VARCHAR(25);
DECLARE coupon_type VARCHAR(25);
DECLARE coupon_amount DOUBLE;
DECLARE coupon_amount_percent DOUBLE;
DECLARE coupon_note VARCHAR(1000);

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

IF uid = -1 THEN
SET uid = (SELECT fn_system_user_get_rotate());
END IF;


SET cust_id = JSON_UNQUOTE(JSON_EXTRACT(cdata, '$.cust_id'));
SET cust_name = JSON_UNQUOTE(JSON_EXTRACT(cdata, '$.cust_name'));
SET cust_address = JSON_UNQUOTE(JSON_EXTRACT(cdata, '$.cust_address'));
SET cust_phone = JSON_UNQUOTE(JSON_EXTRACT(cdata, '$.cust_phone'));
SET cust_postcode = JSON_UNQUOTE(JSON_EXTRACT(cdata, '$.cust_postcode'));
SET cust_kelurahan_id = 0;
SET cust_email = JSON_UNQUOTE(JSON_EXTRACT(cdata, '$.cust_email'));
SET cust_sex = JSON_UNQUOTE(JSON_EXTRACT(cdata, '$.cust_sex'));
-- JSON_UNQUOTE(JSON_EXTRACT(cdata, '$.cust_kelurahan_id'));
SET cust_level_id = (SELECT M_CustomerLevelID FROM m_customerlevel WHERE M_CustomerLevelCode = "CUST.ENDUSER");

IF cust_id IS NULL OR cust_id = 0 THEN
	INSERT INTO m_customer(M_CustomerM_CustomerLevelID,
		M_CustomerName,
		M_CustomerAddress,
		M_CustomerM_KelurahanID,
		M_CustomerPhone,
		M_CustomerUserID,
		M_CustomerEmail,
		M_CustomerSex)
	SELECT cust_level_id, cust_name, cust_address, cust_kelurahan_id, cust_phone, uid, cust_email, cust_sex;
	SET cust_id = (SELECT LAST_INSERT_ID());
END IF;

SET expedition_id = 0; -- JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.expedition_id'));
SET payment_id = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.payment_id'));
SET channel = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.channel'));
SET courier_cost = 0; -- JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.courier_cost'));
SET ex_other = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.ex_other'));
SET ex_note = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.ex_note'));
SET ex_code = (SELECT M_ExpeditionCode FROM m_expedition WHERE M_ExpeditionID = expedition_id);
SET servicex = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.service'));
SET is_ds = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.is_dropship'));
SET ds_customer_id = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.ds_customer_id'));
SET order_note = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.order_note'));
SET leadsource = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.leadsource'));

SET payment_code = (SELECT M_PaymentTypeCode FROM m_paymenttype WHERE M_PaymentTypeID = payment_id);

SET coupon_id = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.coupon_id'));
SET coupon_code = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.coupon_code'));
SET coupon_type = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.coupon_type'));
SET coupon_amount = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.coupon_amount'));
SET coupon_note = JSON_UNQUOTE(JSON_EXTRACT(hdata, '$.coupon_note'));

IF coupon_id IS NULL THEN SET coupon_id = 0; END IF;
IF coupon_code IS NULL THEN SET coupon_code = ''; END IF;
IF coupon_type IS NULL THEN SET coupon_type = ''; END IF;
IF coupon_amount IS NULL THEN SET coupon_amount = 0; END IF;
IF coupon_note IS NULL THEN SET coupon_note = ''; END IF;

IF order_note IS NULL THEN SET order_note = ""; END IF;
IF leadsource IS NULL THEN SET leadsource = 1; END IF;

IF ex_code = 'EX.OTHER' THEN
	SET servicex = ex_other;
ELSE
	SET ex_other = '';
	IF ex_code = 'EX.FREE' THEN
		SET servicex = '';
	ELSE
		SET ex_note = '';
	END IF;
END IF;

IF order_id = 0 THEN
	SET order_number = (SELECT fn_numbering('SO'));
	INSERT INTO l_so(L_SoDate,
		L_SoNumber,
		L_SoM_LeadSourceID,
		L_SoM_CustomerID,
		L_SoM_OrderStatusID,
		L_SoM_ExpeditionID,
		L_SoExpService,
		L_SoExpCost,
		L_SoExpOther,
		L_SoExpNote,
		L_SoM_PaymentID,
		L_SoPaymentChannel,
		L_SoIsDS,
		L_SoDSM_CustomerID,
		L_SoIsQo,
		L_SoNote,
		L_SoCouponAmount,
		L_SoIsCOD)

	SELECT now(), order_number, leadsource, cust_id, M_OrderStatusID, expedition_id, servicex, courier_cost, ex_other, ex_note, payment_id, channel, is_ds, ds_customer_id, "Y", order_note, coupon_amount, IF(payment_code = 'COD', 'Y', 'N')
	FROM m_orderstatus WHERE M_OrderStatusCode = "SO.NEW";

	SET order_id = (SELECT LAST_INSERT_ID());

	
	INSERT INTO l_coupon(	
		L_CouponL_SoID,
		L_CouponM_CouponID,
		L_CouponCode,
		L_CouponType,
		L_CouponAmount,
		L_CouponNote)
	SELECT order_id, coupon_id, coupon_code, coupon_type, coupon_amount, coupon_note;
	
	SET l = JSON_LENGTH(jdata);
	WHILE (i < l) DO
		SET tmp = JSON_EXTRACT(jdata, CONCAT('$[', i, ']'));
		SET item_id = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.item_id'));
		SET item_qty = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.item_qty'));
		SET item_price = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.item_price'));
		SET item_disc = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.item_disc'));
		SET item_discrp = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.item_discrp'));
		SET is_packet = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.is_packet'));
		SET level_id = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.item_level'));

		SET sch_id = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.sch_id'));
		SET sch_day = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.sch_day'));
		SET sch_time = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.sch_time'));

		IF sch_time IS NULL THEN SET sch_time = "00:00"; END IF;
		IF level_id IS NULL THEN SET level_id = 0; END IF;
		
		IF is_packet = "N" THEN
			INSERT INTO l_sodetail(L_SoDetailL_SoID,
				L_SoDetailM_ItemID,
				L_SoDetailPrice,
				L_SoDetailDisc,
				L_SoDetailDiscRp,
				L_SoDetailQty,
				L_SoDetailM_ItemCode,
				L_SoDetailM_ItemName,
				L_SoDetailSalesCode,
				L_SoDetailM_ScheduleID,
				L_SoDetailM_DayID,
				L_SoDetailScheduleTime,
				L_SoDetailM_CustomerLevelID)
			SELECT order_id, item_id, item_price, item_disc, item_discrp, item_qty, M_ItemCode, M_ItemName, M_ItemCode,
				sch_id, sch_day, sch_time, level_id
			FROM m_item WHERE M_ItemID = item_id;
			
			





		ELSE
			INSERT INTO l_sodetail(L_SoDetailL_SoID,
				L_SoDetailIsPacket,
				L_SoDetailM_PacketID,
				L_SoDetailPrice,
				L_SoDetailDisc,
				L_SoDetailDiscRp,
				L_SoDetailQty,
				L_SoDetailM_ItemCode,
				L_SoDetailM_ItemName,
				L_SoDetailSalesCode,
				L_SoDetailM_CustomerLevelID)
			SELECT order_id, is_packet, item_id, item_price, item_disc, item_discrp, item_qty, M_PacketCode, M_PacketName, M_PacketCode, level_id
			FROM m_packet WHERE M_PacketID = item_id;
			
			
			INSERT INTO l_sodetail(L_SoDetailL_SoID,
				L_SoDetailIsPrice,
				L_SoDetailM_ItemID,
				L_SoDetailPrice,
				L_SoDetailDisc,
				L_SoDetailDiscRp,
				L_SoDetailQty,
				L_SoDetailM_ItemCode,
				L_SoDetailM_ItemName,
				L_SoDetailSalesCode,
				L_SoDetailM_CustomerLevelID)
			SELECT order_id, "N", M_ItemID, 0, 0, 0, M_PacketDetailQty*item_qty, M_ItemCode, M_ItemName, CONCAT(M_PacketCode, '-', M_ItemCode), level_id
			FROM m_packetdetail 
			JOIN m_item ON M_PacketDetailM_ItemID = M_ItemID
			JOIN m_packet ON M_PacketDetailM_PacketID = M_PacketID
			WHERE M_PacketDetailM_PacketID = item_id AND M_PAcketDetailIsActive = "Y";
		END IF;
		SET i = i + 1;
	END WHILE;
END IF;

SET unstock = (SELECT GROUP_CONCAT(item_namex SEPARATOR ", ") FROM
				(SELECT L_SoDetailM_ItemName item_namex, SUM(L_SoDetailQty) detail_qty, IFNULL(I_StockQty, 0) stock_qty
				FROM l_sodetail
				LEFT JOIN i_stock ON L_SoDetailM_ItemID = I_StockM_ItemID AND I_StockIsActive = "Y" AND I_StockM_WarehouseID = 2
				WHERE L_SoDetailL_SoID = order_id AND L_SoDetailIsActive = "Y" AND L_SoDetailIsPacket = "N"
				GROUP BY L_SoDetailM_ItemID
				HAVING detail_qty > stock_qty) x);

-- IF unstock IS NOT NULL THEN
-- 	SELECT "ERR" status, unstock data, CONCAT("Item ", unstock, " tidak mencukupi stoknya") message;
-- 	ROLLBACK;
-- ELSE
	
	
-- 	UPDATE l_sodetail SET L_SoDetailApprovedQty = L_SoDetailQty, L_SoDetailApproved = "Y", L_SoDetailApprovedDate = now(), L_SoDetailApprovedUserID = uid
-- 	WHERE L_SoDetailL_SoID = order_id;
-- 	UPDATE l_so
-- 	JOIN m_orderstatus ON M_OrderStatusCode = "SO.Approved" AND M_OrderStatusIsActive = "Y"
-- 	SET L_SoApproved = "Y", L_SoM_OrderStatusID = M_OrderStatusID
-- 	WHERE L_SoID = order_id;
	

-- CALL sp_so_coupon_detail(coupon_id, coupon_type, order_id);


UPDATE l_so 
JOIN (
SELECT L_SoDetailL_SoID so_id, SUM(L_SoDetailSubTotal) sub_total FROM l_sodetail WHERE L_SoDetailIsActive = "Y" AND L_SoDetailL_SoID = order_id AND L_SoDetailM_CouponID = coupon_id
) x on so_id = L_SoID
SET L_SoCouponSubTotal = sub_total, L_SoCouponPercentage = IF(sub_total > 0, L_SoCouponAmount * 100 / sub_total, 0)
WHERE L_SoID = order_id;

	CALL sp_so_confirm(order_id);
	
	CALL sp_so_retotal(order_id);
	SELECT "OK" status, JSON_OBJECT("order_id", order_id, "order_number", order_number, "order_md5", L_SoMD5) data
FROM l_so WHERE L_SoID = order_id;

	COMMIT;
-- END IF;


END;;
DELIMITER ;