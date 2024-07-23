DROP PROCEDURE `sp_finance_pay_confirm`;
DELIMITER ;;
CREATE PROCEDURE `sp_finance_pay_confirm` (IN `payid` int, IN `uid` int)
BEGIN

DECLARE soid INTEGER;

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

UPDATE f_payment
SET F_PaymentConfirmed = "Y",
	F_PaymentConfirmedDate = now(),
	F_PaymentConfirmedUserID = uid
WHERE F_PaymentID = payid;

SET soid = (SELECT F_PaymentL_SoID FROM f_payment WHERE F_PaymentID = payid);

UPDATE l_so
JOIN m_orderstatus ON M_OrderStatusCode = 'IV.Confirmed'
SET L_SoM_OrderStatusID = M_OrderStatusID
WHERE L_SoID = soid;

SELECT "OK" status, JSON_OBJECT("payment_id", payid, "payment_number", F_PaymentNumber, "invoice_number", L_InvoiceNumber) data
FROM f_payment
JOIN l_invoice ON F_PaymentL_InvoiceID = L_InvoiceID WHERE F_PaymentID = payid;

COMMIT;

END;;
DELIMITER ;