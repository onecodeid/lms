DROP PROCEDURE `sp_so_reduce_stock`;
DELIMITER ;;
CREATE PROCEDURE `sp_so_reduce_stock` (IN `soid` int)
BEGIN
	DECLARE finished INTEGER DEFAULT 0;
	DECLARE detail_id INTEGER;
	DECLARE item_id INTEGER;
	DECLARE item_qty DOUBLE;
	DECLARE is_packet CHAR(1);
	DECLARE packet_id INTEGER;
	
	DEClARE curSO
		CURSOR FOR 
			SELECT L_SoDetailID, L_SoDetailM_ItemID, L_SoDetailApprovedQty, L_SoDetailIsPacket, L_SoDetailM_PacketID
			FROM l_so
			JOIN l_sodetail ON L_SoID = L_SoDetailL_SoID AND L_SoDetailIsActive = "Y" AND L_SoDetailApproved = "Y"
			JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID AND M_OrderStatusCode = "SO.Confirmed"
			WHERE L_SoID = soid;

	DECLARE CONTINUE HANDLER 
        FOR NOT FOUND SET finished = 1; 

	OPEN curSO;

	getSO: LOOP
		FETCH curSO INTO detail_id, item_id, item_qty, is_packet, packet_id;
		IF finished = 1 THEN 
			LEAVE getSO;
		END IF;
		
		
		IF is_packet = "N" THEN

			UPDATE i_stock
            JOIN m_warehouse ON I_StockM_WarehouseID = M_WarehouseID AND M_WarehouseCode = 'WAREHOUSE.SALES' AND M_WarehouseIsActive = "Y"
			SET I_StockQty = (I_StockQty - item_qty), I_StockLastTransCode = 'SO.Sales', I_StockLastTransRefID = detail_id, I_StockLastTransQty = (0 - item_qty)
			WHERE I_StockM_ItemID = item_id
			AND I_StockIsActive = "Y" ;			
		END IF;
		
	END LOOP getSO;
	CLOSE curSO;

END;;
DELIMITER ;