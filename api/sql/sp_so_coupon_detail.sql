DROP PROCEDURE `sp_so_coupon_detail`;
DELIMITER ;;
CREATE PROCEDURE `sp_so_coupon_detail` (IN `coupon_id` int, IN `coupon_type` varchar(50), IN `so_id` int)
BEGIN

UPDATE l_sodetail SET L_SoDetailM_CouponID = 0 WHERE L_SoDetailL_SoID = so_id AND L_SoDetailIsActive = "Y";
IF coupon_type = 'COUPON.CART' THEN
    UPDATE l_sodetail SET L_SoDetailM_CouponID = coupon_id WHERE L_SoDetailL_SoID = so_id AND L_SoDetailIsActive = "Y";

ELSEIF coupon_type = 'COUPON.PRODUCT' THEN
    UPDATE l_sodetail
    JOIN m_couponproduct ON M_CouponProductM_CouponID = coupon_id AND M_CouponProductM_ItemID = L_SoDetailM_ItemID
    SET L_SoDetailM_CouponID = M_CouponProductM_CouponID
    WHERE L_SoDetailIsActive = "Y" AND L_SoDetailL_SoID = so_id;

ELSEIF coupon_type = 'COUPON.PACKET' THEN
    UPDATE l_sodetail
    JOIN m_couponpacket ON M_CouponPacketM_CouponID = coupon_id AND M_CouponPacketM_PacketID = L_SoDetailM_PacketID
    SET L_SoDetailM_CouponID = M_CouponPacketM_PacketID
    WHERE L_SoDetailIsActive = "Y" AND L_SoDetailL_SoID = so_id;

END IF;


END;;
DELIMITER ;