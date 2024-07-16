sp:BEGIN

DECLARE coupon_id INTEGER;
DECLARE coupon_type VARCHAR(25);
DECLARE coupon_min_spend DOUBLE;
DECLARE coupon_max_spend DOUBLE;
DECLARE coupon_amount DOUBLE;
DECLARE coupon_amount_p DOUBLE;
DECLARE coupon_product_id INTEGER;
DECLARE coupon_courier_id INTEGER;
DECLARE coupon_item_id VARCHAR(255) DEFAULT "[]";
DECLARE category_id INTEGER;

DECLARE item_ln INTEGER;
DECLARE item_id INTEGER;
DECLARE n INTEGER DEFAULT 0;
DECLARE status INTEGER DEFAULT 0;
DECLARE min_spend DOUBLE;
DECLARE max_spend DOUBLE;
DECLARE max_qty INTEGER;
DECLARE used_qty INTEGER;

SET coupon_id = (SELECT M_CouponID FROM m_coupon WHERE M_CouponCode = coupon_code AND M_CouponIsActive = 'Y' AND M_CouponStartDate <= date(now()) AND M_CouponEndDate >= date(now()) LIMIT 1);

IF coupon_id IS NOT NULL THEN
	SELECT M_CouponTypeCode, M_CouponMinSpend, M_CouponMaxSpend, M_CouponAmount, M_CouponAmountPercent, M_CouponMinSpend, M_CouponMaxSpend, M_CouponMaxQty, M_CouponUsedQty
	INTO coupon_type, coupon_min_spend, coupon_max_spend, coupon_amount, coupon_amount_p, min_spend, max_spend, max_qty, used_qty
	FROM m_coupon 
	JOIN m_coupontype ON M_CouponM_CouponTypeID = M_CouponTypeID WHERE M_CouponID = coupon_id;
	
	IF coupon_type = "COUPON.COURIER" THEN
		SET coupon_courier_id = (SELECT M_CouponExpID FROM m_couponexp WHERE M_CouponExpM_ExpeditionID = courier_id AND M_CouponExpM_CouponID = coupon_id AND M_CouponExpIsActive = "Y");
		
		IF coupon_courier_id IS NULL THEN
			SELECT "ERR" status, "Kupon ini tidak mendukung expedisi yang anda pilih" message;
			ROLLBACK;
			LEAVE sp;			
		END IF;
	ELSEIF coupon_type = "COUPON.PRODUCT" THEN
		SET item_ln = JSON_LENGTH(items);
		item_loop:WHILE n <= item_ln DO
			SET item_id = JSON_UNQUOTE(JSON_EXTRACT(items, CONCAT('$[', n, ']')));
			SET n = n + 1;
			
			SET coupon_product_id = (SELECT M_CouponProductID FROM m_couponproduct WHERE M_CouponProductM_ItemID = item_id AND M_CouponProductM_CouponID = coupon_id AND M_CouponProductIsActive = "Y");
			
			IF coupon_product_id IS NOT NULL THEN 
				SET status = 1;
				SET coupon_item_id = JSON_ARRAY_APPEND(coupon_item_id, '$', item_id);

			END IF;
		END WHILE item_loop;
		
		


		IF status = 0 THEN
			SELECT "ERR" status, "Kupon ini tidak mendukung produk yang anda pilih" message;
			ROLLBACK;
			LEAVE sp;			
		END IF;
	ELSEIF coupon_type = "COUPON.PACKET" THEN
		SET item_ln = JSON_LENGTH(packets);
		
		item_loop:WHILE n <= item_ln DO
			SET item_id = JSON_UNQUOTE(JSON_EXTRACT(packets, CONCAT('$[', n, ']')));
			SET n = n + 1;
			
			SET coupon_product_id = (SELECT M_CouponPacketID FROM m_couponpacket WHERE M_CouponPacketM_PacketID = item_id AND M_CouponPacketM_CouponID = coupon_id AND M_CouponPacketIsActive = "Y");
			
			IF coupon_product_id IS NOT NULL THEN 
				SET status = 1;
				SET coupon_item_id = JSON_ARRAY_APPEND(coupon_item_id, '$', item_id);

			END IF;
		END WHILE item_loop;
		
		


		IF status = 0 THEN
			SELECT "ERR" status, "Kupon ini tidak mendukung produk yang anda pilih" message;
			ROLLBACK;
			LEAVE sp;			
		END IF;
	ELSEIF coupon_type = "COUPON.CATEGORY" THEN
		SET category_id = (SELECT M_CouponM_CategoryID FROM m_coupon WHERE M_CouponID = coupon_id);
		SET item_ln = JSON_LENGTH(items);
		item_loop:WHILE n <= item_ln DO
			SET item_id = JSON_UNQUOTE(JSON_EXTRACT(items, CONCAT('$[', n, ']')));
			SET n = n + 1;
			
			SET coupon_product_id = (SELECT M_ItemID FROM m_item WHERE M_ItemM_CategoryID = category_id AND M_ItemID = item_id AND M_ItemIsActive = "Y");
			IF coupon_product_id IS NOT NULL THEN 
				SET status = 1;
				SET coupon_item_id = JSON_ARRAY_APPEND(coupon_item_id, '$', item_id);
			END IF;
		END WHILE item_loop;
		
		


		IF status = 0 THEN
			SELECT "ERR" status, "Kupon ini tidak mendukung kategori produk yang anda pilih" message;
			ROLLBACK;
			LEAVE sp;			
		END IF;
	END IF;
ELSE
	SELECT "ERR" status, "Kupon tidak ditemukan" message;
	ROLLBACK;
	LEAVE sp;
END IF;

IF min_spend <> 0 AND spend < min_spend THEN
	SELECT "ERR" status, "Minimum belanja tidak terpenuhi" message;
	ROLLBACK;
	LEAVE sp;
END IF;

IF max_spend <> 0 AND spend > max_spend THEN
	SELECT "ERR" status, "Maksimum belanja tidak terpenuhi" message;
	ROLLBACK;
	LEAVE sp;
END IF;

IF max_qty > 0 AND used_qty >= max_qty THEN
	SELECT "ERR" status, "Kuota Kupon sudah habis, maaf kakak :)" message;
	ROLLBACK;
	LEAVE sp;
END IF;

SELECT "OK" status, JSON_OBJECT("coupon_id", coupon_id, "coupon_code", coupon_code, "coupon_type", coupon_type, "coupon_amount", coupon_amount, "coupon_amount_percent", coupon_amount_p, "coupon_item_id", coupon_item_id, "coupon_courier_id", coupon_courier_id) data;
COMMIT;

END