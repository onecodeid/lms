DROP PROCEDURE `sp_wh_stock_packet_adjust`;
DELIMITER ;;
CREATE PROCEDURE `sp_wh_stock_packet_adjust` ()
BEGIN

UPDATE i_stockpacket
	LEFT JOIN (
	SELECT M_PacketID packet_id, MIN(I_StockQty) stock_min, I_StockM_WarehouseID warehouseid
	FROM m_packet
	JOIN m_packetdetail ON M_PacketID =M_PacketDetailM_PacketID AND M_PAcketDetailIsactive="Y"
	JOIN m_item ON M_PAcketDetailM_ItemID =M_ItemID AND M_ItemIsActive = "Y"
	JOIN i_stock ON I_StockM_ItemID=M_ItemID AND I_StockIsActive = "Y"
	WHERE M_PacketIsActive= "Y"
	GROUP BY M_PacketID, I_StockM_WarehouseID ) x ON packet_id = I_StockPacketM_PacketID AND  warehouseid = I_StockPacketM_WarehouseID
	SET I_StockPacketQty = IFNULL(stock_min, 0)
	WHERE I_StockPacketIsActive = "Y";

END;;
DELIMITER ;