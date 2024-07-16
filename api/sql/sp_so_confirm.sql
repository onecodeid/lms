DROP PROCEDURE `sp_so_confirm`;
DELIMITER ;;
CREATE PROCEDURE `sp_so_confirm` (IN `so_h_id` int)
BEGIN

DECLARE iv_id INTEGER DEFAULT 0;
DECLARE unique_cost DOUBLE DEFAULT 0;
DECLARE payment_code VARCHAR(25);

DECLARE is_cod CHAR(1);
DECLARE is_due CHAR(1);
DECLARE customer_id INTEGER;

DECLARE tmp TEXT;
DECLARE wat_invoice_number VARCHAR(255);
DECLARE wat_invoice_key VARCHAR(255);
DECLARE bank_accounts TEXT;

DECLARE EXIT HANDLER FOR SQLEXCEPTION
BEGIN

GET DIAGNOSTICS CONDITION 1
@p1 = RETURNED_SQLSTATE, @p2 = MESSAGE_TEXT, @tb = TABLE_NAME;
SELECT "ERR" as status, @p1 as data  , @p2 as message, @tb as table_name;
ROLLBACK;
INSERT INTO `one-sales-log`.log_err(Log_ErrRefID,Log_ErrRefNumber,Log_ErrEvent,Log_ErrNote)
SELECT so_h_id,"","SO Confirm", CONCAT("ERR | ", @p1, " ", @p2);
END;

DECLARE EXIT HANDLER FOR SQLWARNING
BEGIN

GET DIAGNOSTICS CONDITION 1
@p1 = RETURNED_SQLSTATE, @p2 = MESSAGE_TEXT, @tb = TABLE_NAME;
SELECT "ERR" as status, @p1 as data  , @p2 as message, @tb as table_name;
ROLLBACK;
INSERT INTO `one-sales-log`.log_err(Log_ErrRefID,Log_ErrRefNumber,Log_ErrEvent,Log_ErrNote)
SELECT so_h_id,"","SO Confirm", CONCAT("WARNING | ", @p1, " ", @p2);
END;

START TRANSACTION;

SET payment_code = (SELECT M_PaymentTypeCode FROM l_so JOIN m_paymenttype ON L_SoM_PaymentID = M_PaymentTypeID WHERE L_SoID = so_h_id);

IF payment_code = 'TRANSFER' THEN
SET unique_cost = 0;


END IF;

SET iv_id = (SELECT L_InvoiceID FROM l_invoice WHERE L_InvoiceL_SoID = so_h_id AND L_InvoiceIsActive = "Y");
IF iv_id IS NULL THEN
	INSERT INTO l_invoice(L_InvoiceL_SoID, L_InvoiceDate, L_InvoiceNumber, L_InvoiceIsCOD)
	SELECT so_h_id, now(), fn_numbering('IV'), L_SoIsCOD
	FROM l_so WHERE L_SoID = so_h_id;
	
	UPDATE l_so 
	JOIN m_orderstatus ON M_OrderStatusCode = "SO.Confirmed" AND M_OrderStatusIsActive = "Y"
	SET L_SoM_OrderStatusID = M_OrderStatusID, L_SoUniqueCost = unique_cost
	WHERE L_SoID = so_h_id;

	SET iv_id = (SELECT LAST_INSERT_ID());
 CALL sp_so_reduce_stock(so_h_id);
 CALL sp_so_retotal(so_h_id);
 
	
	INSERT INTO s_email_schedule(S_EmailScheduleType, S_EmailScheduleAddress, S_EmailScheduleSubject, S_EmailScheduleReffID)
	SELECT "INVOICE", M_CustomerEmail, CONCAT("Invoice Customer untuk ", M_CustomerName), L_InvoiceID
	FROM l_invoice 
	JOIN l_so ON L_InvoiceL_SoID = L_SoID 
	JOIN m_customer ON L_SoM_CustomerID = M_CustomerID  AND M_CustomerEmail IS NOT NuLL AND M_CustomerEmail <> ""
	WHERE L_InvoiceID = iv_id;

	
	SET tmp = (SELECT Crm_WatzapInvoiceNumber FROM crm_watzap WHERE Crm_WatzapIsActive = "Y" LIMIT 1);
	IF tmp IS NOT NULL AND tmp <> "" THEN
		SET wat_invoice_number = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.wa_number'));
		SET wat_invoice_key = JSON_UNQUOTE(JSON_EXTRACT(tmp, '$.key'));
		SET bank_accounts = (SELECT GROUP_CONCAT(CONCAT(M_BankName, " No *", M_BankAccountNumber, "* a/n ", M_BankAccountName) SEPARATOR "\n")
			FROM m_bankaccount 
			JOIN m_bank ON M_BankAccountM_BankID = M_BankID
			WHERE M_BankAccountIsActive = "Y");

		INSERT INTO crm_waschedule(
			Crm_WaScheduleType,
			Crm_WaScheduleSenderNumber,
			Crm_WaScheduleSenderKey,
			Crm_WaScheduleDestNumber,
			Crm_WaScheduleText,
			Crm_WaScheduleImage,
			Crm_WaScheduleStatus
		)
		SELECT "WA.INVOICE", wat_invoice_number, wat_invoice_key, M_CustomerPhone, 
		REPLACE(
			REPLACE(
				REPLACE(
					REPLACE(
						REPLACE(
							REPLACE(
								REPLACE(
									REPLACE(Crm_WaTemplateContent, '[order_items]', items), 
									'[customer_name]', M_CustomerName), 
								'[order_number]', L_InvoiceNumber
							),
							'[order_total]', L_SoTotal
						),
						'[bank_accounts]', bank_accounts
					),
					'[order_total_text]', fn_terbilang(L_SoTotal)
				),
				'[admin_name]', admin_name
			),
			'[admin_phone]', admin_phone
		),
		Crm_WaTemplateImage, `status`
		FROM (
			SELECT wat_invoice_number, wat_invoice_key, M_CustomerPhone, M_CustomerName, 
			L_InvoiceNumber, L_SoTotal,
			Crm_WaTemplateContent, Crm_WaTemplateImage, 0 `status`,
			GROUP_CONCAT(CONCAT(IFNULL(M_ItemName, M_PacketName), " (", L_SoDetailQty, ")") SEPARATOR "*\n*") items,
			S_UserProfileFullName admin_name, S_UserProfilePhone admin_phone
			FROM l_invoice 
			JOIN l_so ON L_InvoiceL_SoID = L_SoID 
			JOIN l_sodetail ON L_SoDetailL_SoID = L_SoID AND L_SoDetailIsActive = "Y"
			LEFT JOIN m_item ON L_SoDetailM_ItemID = M_ItemID AND L_SoDetailIsPacket = "N"
			LEFT JOIN m_packet ON L_SoDetailM_PacketID = M_PacketID AND L_SoDetailIsPacket = "Y"
			JOIN m_customer ON L_SoM_CustomerID = M_CustomerID  AND M_CustomerPhone	IS NOT NuLL AND M_CustomerPhone <> ""
			JOIN crm_watemplate ON Crm_WaTemplateIsActive = "Y" AND Crm_WaTemplateSpecial = "WA.INVOICE"
			JOIN s_user ON L_SoUserID = S_UserID
			LEFT JOIN s_userprofile ON s_userprofileS_UserID = S_UserID AND S_UserProfileIsActive = "Y"
			WHERE L_InvoiceID = iv_id
			GROUP BY L_InvoiceID
		) x;
	END IF;
	
	

END IF;

UPDATE l_invoice
JOIN l_so ON L_InvoiceL_SoID = L_SoID
JOIN m_paymenttype ON L_SoM_PaymentID = M_PaymentTypeID
SET L_InvoiceDue = M_PaymentTypeDue, 
	L_InvoiceDueDate = IF(M_PaymentTypeDue = 'Y', date(date_add(L_InvoiceDate, interval M_PaymentTypeDuration day)), null),
	L_InvoiceTotal = L_SoTotal, L_InvoiceUnpaid = L_SoTotal - L_InvoicePaid
WHERE L_InvoiceID = iv_id;

SET is_due = (SELECT L_InvoiceDue FROM l_invoice WHERE L_InvoiceID = iv_id);
IF is_due = "Y" THEN
	INSERT INTO w_courier(W_CourierL_SoID, W_CourierDate)
	SELECT so_h_id, now();
END IF;

SET is_cod = (SELECT L_SoIsCOD FROM l_so WHERE L_SoID = so_h_id);
IF is_cod = "Y" THEN
	INSERT INTO f_payment(F_PaymentDate,
		F_PaymentNumber,
		F_PaymentL_InvoiceID,
		F_PaymentL_SoID,
		F_PaymentIsCOD,
		F_PaymentM_PaymentTypeID,
		F_PaymentNote,
		F_PaymentAmount)
	SELECT now(), fn_numbering('PAY'), iv_id, so_h_id, is_cod, L_SoM_PaymentID, "", L_SoTotal
	FROM l_so 
	WHERE L_SoID = so_h_id;
	
	UPDATE l_so 
	JOIN m_orderstatus ON M_OrderStatusCode = 'IV.Paid' ANd M_OrderStatusIsActive = 'Y'
	SET L_SoM_OrderStatusID = M_OrderStatusID WHERE L_SoID = so_h_id;
	
	INSERT INTO w_courier(W_CourierL_SoID, W_CourierDate)
	SELECT so_h_id, now();
END IF;

SET customer_id = (SELECT L_SoM_CustomerID FROM l_so WHERE L_SoID = so_h_id);
CALL sp_master_customer_isnew(customer_id);


SELECT "OK" as status, JSON_OBJECT("so_id", so_h_id, "invoice_id", iv_id, "invoice_number", L_InvoiceNumber, "so_md5", L_SoMD5) as data FROM l_so JOIN l_invoice ON L_InvoiceL_SoID = L_SoID WHERE L_SoID = so_h_id;

COMMIT;

END;;
DELIMITER ;