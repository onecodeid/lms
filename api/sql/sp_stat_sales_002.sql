BEGIN

SELECT M_CustomerLevelName level_name, count(DISTINCT M_CustomerID) customer_qty, SUM(L_SoDetailSubTotal) omzet
FROM l_so
JOIN m_customer ON L_SoM_CustomerID = M_CustomerID
JOIN m_customerlevel ON M_CustomerM_CustomerLevelID = M_CustomerLevelID
AND FIND_IN_SET(M_CustomerLevelID, level)
JOIN f_payment ON F_PaymentL_SoID = L_SoID
	AND F_PaymentIsActive = "Y"
	AND F_PaymentConfirmed = "Y"
JOIN l_sodetail ON L_SoDetailL_SoID = L_SoID
	AND L_SoDetailIsActive = "Y"
WHERE L_SoDate BETWEEN CONCAT(sdate, " 00:00:00") AND CONCAT(edate, " 23:59:59")
	AND L_SoIsActive = "Y"
	AND L_SoDetailIsPrice = "Y"
	AND ((`type` = 'A') OR (`type` = 'I' AND L_SoDetailIsPacket = 'N') OR (`type` = 'P' AND L_SoDetailIsPacket = 'Y'))
    AND ((L_SoUserID = uid AND uid <> 0) OR uid = 0)
GROUP BY M_CustomerLevelID
HAVING (((omzet >= omin and omin <> 0) OR omin = 0) AND ((omzet <= omax AND omax <> 0) OR omax = 0))
ORDER BY omzet DESC;

SELECT M_CustomerName customer_name, M_CustomerLevelName level_name, fn_address_city_name(M_CustomerM_KelurahanID) city_name, SUM(L_SoDetailSubTotal) omzet,
fn_sales_pareto_by_customer_date(M_CustomerID, sdate, edate) pareto
FROM l_so
JOIN m_customer ON L_SoM_CustomerID = M_CustomerID
JOIN m_customerlevel ON M_CustomerM_CustomerLevelID = M_CustomerLevelID
AND M_CustomerLevelCode <> 'CUST.ENDUSER'
AND M_CustomerLevelCode <> 'CUST.FAMILY'
AND FIND_IN_SET(M_CustomerLevelID, level)
JOIN f_payment ON F_PaymentL_SoID = L_SoID
	AND F_PaymentIsActive = "Y"
	AND F_PaymentConfirmed = "Y"
JOIN l_sodetail ON L_SoDetailL_SoID = L_SoID
	AND L_SoDetailIsActive = "Y"
WHERE L_SoDate BETWEEN CONCAT(sdate, " 00:00:00") AND CONCAT(edate, " 23:59:59")
	AND L_SoIsActive = "Y"
	AND L_SoDetailIsPrice = "Y"
	AND ((`type` = 'A') OR (`type` = 'I' AND L_SoDetailIsPacket = 'N') OR (`type` = 'P' AND L_SoDetailIsPacket = 'Y'))
    AND ((L_SoUserID = uid AND uid <> 0) OR uid = 0)

GROUP BY M_CustomerID
HAVING (((omzet >= omin and omin <> 0) OR omin = 0) AND ((omzet <= omax AND omax <> 0) OR omax = 0))
ORDER BY omzet DESC
LIMIT 25;

END