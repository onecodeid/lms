BEGIN

DECLARE cid INTEGER;
DECLARE rpoint INTEGER;
DECLARE sentt CHAR(1);

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

SET rpoint = (SELECT F_RedeemPoint FROM f_redeem WHERE F_RedeemID = redeemid);
SET cid = (SELECT F_RedeemM_CustomerID FROM f_redeem WHERE F_RedeemID = redeemid);
SET sentt = (SELECT F_RedeemSent FROM f_redeem WHERE F_RedeemID = redeemid);

IF (sentt = "Y" OR sentt = "X") THEN
    SELECT "ERR" status, "Reward telah dikirim / diambil, tidak bisa dibatalkan :(" message;
ELSE
    UPDATE m_customerpoint SET M_CustomerPointQty = M_CustomerPointQty + rpoint WHERE M_CustomerPointIsActive = "Y" AND M_CustomerPointM_CustomerID = cid;

    UPDATE f_redeem SET F_RedeemIsActive = "N" WHERE F_RedeemID =redeemid;
    SELECT "OK" status, JSON_OBJECT("customer_id", cid, "redeem_id", redeemid, "reward_point", rpoint) data;
END IF;

COMMIT;

END
