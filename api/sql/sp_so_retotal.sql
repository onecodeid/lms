DROP PROCEDURE `sp_so_retotal`;
DELIMITER ;;
CREATE PROCEDURE `sp_so_retotal` (IN `soid` int)
BEGIN

DECLARE uqty INTEGER;
DECLARE qty DOUBLE;
DECLARE total DOUBLE;
DECLARE total_hpp DOUBLE;
DECLARE weight DOUBLE;

DECLARE uqty_packet INTEGER;
DECLARE qty_packet DOUBLE;
DECLARE total_packet DOUBLE;
DECLARE weight_packet DOUBLE;

DECLARE exp_id INTEGER;
DECLARE cod_rate DOUBLE;
DECLARE cod_cost DOUBLE;
DECLARE item_only CHAR(1) DEFAULT "N";

SET total_hpp = 0; -- (SELECT SUM(L_SoDetailHPP * L_SoDetailQty) FROM l_sodetail WHERE L_SoDetailL_SoID = soid AND L_SoDetailIsActive = "Y" AND L_SoDetailIsPrice = "Y" );

SELECT COUNT(L_SoDetailID), SUM(L_SoDetailQty), SUM(L_SoDetailSubTotal), SUM(M_ItemWeight*L_SoDetailQty)
INTO uqty, qty, total, weight
FROM l_sodetail
JOIN m_item ON L_SoDetailM_ItemID = M_ItemID
WHERE L_SoDetailL_SoID = soid AND L_SoDetailIsActive = "Y" AND L_SoDetailIsPrice = "Y";

SELECT COUNT(L_SoDetailID), SUM(L_SoDetailQty), SUM(L_SoDetailSubTotal), SUM(item_weight*L_SoDetailQty)
INTO uqty_packet, qty_packet, total_packet, weight_packet
FROM l_sodetail
JOIN (
SELECT M_PacketID packet_id, SUM(M_ItemWeight) item_weight
FROM m_packet
JOIN m_packetdetail ON M_PacketDetailM_PacketID = M_PacketID AND M_PAcketDetailIsActive = "Y"
JOIN m_item ON M_PAcketDetailM_ItemID = M_ItemID
GROUP BY M_PacketID
) x on packet_id = L_SoDetailM_PacketID
WHERE L_SoDetailL_SoID = soid AND L_SoDetailIsActive = "Y" AND L_SoDetailIsPacket = "Y";

IF uqty IS NULL THEN SET uqty = 0; END IF;
IF qty IS NULL THEN SET qty = 0; END IF;
IF total IS NULL THEN SET total = 0; END IF;
IF weight IS NULL THEN SET weight = 0; END IF;
IF uqty_packet IS NULL THEN SET uqty_packet = 0; END IF;
IF qty_packet IS NULL THEN SET qty_packet = 0; END IF;
IF total_packet IS NULL THEN SET total_packet = 0; END IF;
IF weight_packet IS NULL THEN SET weight_packet = 0; END IF;

UPDATE l_so
SET L_SoTotalUniqueQty = uqty + uqty_packet, L_SoTotalQty = qty + qty_packet, L_SoSubTotal = total + total_packet, 
L_SoTotal = total + total_packet + L_SoExpCost - L_SoCouponAmount, L_SoSubTotalWeight = weight + weight_packet, 
L_SoTotalWeight = weight + weight_packet + L_SoAddWeight,
L_SoTotalHPP = total_hpp
WHERE L_SoID = soid;


SET exp_id = (SELECT M_ExpeditionID
			FROM l_so 
			JOIN m_expedition ON L_SoM_ExpeditionID = M_ExpeditionID AND M_ExpeditionIsCOD = "Y" ANd M_ExpeditionIsActive = "Y"
			WHERE L_SoID = soid AND L_SoIsCOD = "Y");
IF exp_id IS NOT NULL THEN
	SET cod_rate = (SELECT M_ExpeditionCODRate FROM m_expedition WHERE M_ExpeditionID = exp_id);
    SET item_only = (SELECT M_ExpeditionCODRateItemsOnly FROM m_expedition WHERE M_ExpeditionID = exp_id);
	
    IF item_only = "Y" THEN
        UPDATE l_so
        SET L_SoCODCost = ((L_SoTotal-L_SoExpCost) * (cod_rate) / 100), L_SoTotal = ((L_SoTotal-L_SoExpCost) * (100 + cod_rate) / 100) + L_SoExpCost,
            L_SoCODItemOnly = "Y"
        WHERE L_SoID = soid;
    ELSE
        UPDATE l_so
        SET L_SoCODCost = (L_SoTotal * (cod_rate) / 100), L_SoTotal = (L_SoTotal * (100 + cod_rate) / 100)
        WHERE L_SoID = soid;
    END IF;
	
	
	
END IF;



UPDATE l_so
SET L_SoTotal = L_SoTotal - L_SoUniqueCost
WHERE L_SoID = soid;


END;;
DELIMITER ;