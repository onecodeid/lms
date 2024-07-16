BEGIN

DECLARE cpoint INTEGER;
DECLARE rpoint INTEGER;

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

SET cpoint = (SELECT M_CustomerPointQty FROM m_customerpoint WHERE M_CustomerPointIsActive = "Y" AND M_CustomerPointM_CustomerID = customerid LIMIT 1);
SET rpoint = (SELECT M_RewardPoint FROM m_reward WHERE M_RewardID = rewardid);

IF cpoint < rpoint THEN
    SELECT "ERR" status, "Poin Customer tidak mencukupi, silahkan belanja kembali :)" message;
ELSE
    UPDATE m_customerpoint SET M_CustomerPointQty = M_CustomerPointQty - rpoint WHERE M_CustomerPointIsActive = "Y" AND M_CustomerPointM_CustomerID = customerid;

    INSERT INTO f_redeem(F_RedeemDate,
        F_RedeemM_CustomerID,
        F_RedeemM_RewardID,
        F_RedeemPointBefore,
        F_RedeemPoint,
        F_RedeemPointAfter,
        F_RedeemNote)
    SELECT date(now()), customerid, rewardid, cpoint, rpoint, cpoint - rpoint, note;

    SELECT "OK" status, JSON_OBJECT("customer_id", customerid, "reward_id", rewardid, "customer_point", cpoint, "reward_point", rpoint) data;
END IF;

COMMIT;

END
