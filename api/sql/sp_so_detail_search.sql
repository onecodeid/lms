DROP PROCEDURE `sp_so_detail_search`;
DELIMITER ;;
CREATE PROCEDURE `sp_so_detail_search` (IN `so_id` int, IN `search` varchar(50))
BEGIN

DECLARE whid INTEGER;

SET whid = (SELECT M_WarehouseID FROM m_warehouse WHERE M_WarehouseCode = "WAREHOUSE.SALES" AND M_WarehouseIsActive = "Y");

SELECT * FROM (
(SELECT L_SoDetailID, L_SoDetailL_SoID, L_SoDetailM_ItemCode, L_SoDetailM_ItemName, L_SoDetailIsPacket, L_SoDetailIsPrice,	
L_SoDetailPrice, L_SoDetailDisc, L_SoDetailDiscRp, L_SoDetailDiscTotal, L_SoDetailQty, L_SoDetailApprovedQty, L_SoDetailSubTotal, L_SoDetailApproved,
M_ItemID, M_ItemCode, M_ItemName, M_ItemMinStock, M_ItemWeight, M_ItemM_UnitID, M_ItemHPP, I_StockQty
FROM `l_sodetail`
                JOIN m_item ON L_SoDetailM_ItemID = M_ItemID
                LEFT JOIN i_stock ON I_StockM_ItemID = M_ItemID AND I_StockM_WarehouseID = whid
                WHERE (`M_ItemName` LIKE CONCAT(search,'%'))
                AND (`L_SoDetailIsActive` = 'Y' OR `L_SoDetailIsActive` = 'X')
                AND `L_SoDetailL_SoID` = so_id
AND L_SoDetailIsPacket = "N"
AND L_SoDetailIsPrice = "Y")

UNION

(SELECT L_SoDetailID, L_SoDetailL_SoID, L_SoDetailM_ItemCode, L_SoDetailM_ItemName, L_SoDetailIsPacket, L_SoDetailIsPrice,	
L_SoDetailPrice, L_SoDetailDisc, L_SoDetailDiscRp, L_SoDetailDiscTotal, L_SoDetailQty, L_SoDetailApprovedQty, L_SoDetailSubTotal, L_SoDetailApproved,
M_PacketID M_ItemID, M_PacketCode M_ItemCode, M_PacketName M_ItemName, 
0 M_ItemMinStock, M_PacketTotalWeight M_ItemWeight, 0 M_ItemM_UnitID, 0 M_ItemHPP, I_StockPacketQty I_StockQty
FROM `l_sodetail`
                JOIN m_packet ON L_SoDetailM_PacketID = M_PacketID
                LEFT JOIN i_stockpacket ON I_StockPacketM_PacketID = M_PacketID AND I_StockPacketM_WarehouseID = whid
                WHERE (`M_PacketName` LIKE CONCAT(search,'%'))
                AND (`L_SoDetailIsActive` = 'Y' OR `L_SoDetailIsActive` = 'X')
                AND `L_SoDetailL_SoID` = so_id
AND L_SoDetailIsPacket = "Y"
AND L_SoDetailIsPrice = "Y")) x;

END;;
DELIMITER ;