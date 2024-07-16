BEGIN

DECLARE tmp VARCHAR(1000);

SET tmp = (SELECT JSON_OBJECT("id", L_SoID, "number", L_SoNumber, "date", L_SoDate, "status", M_OrderStatusSellerName, "status_code", M_OrderStatusCode)
FROM l_so
JOIN m_orderstatus ON L_SoM_OrderStatusID = M_OrderStatusID
WHERE L_SoIsActive = "Y"
AND L_SoM_CustomerID = customer_id
ORDER BY L_SoNumber DESC
LIMIT 1);

IF tmp IS NULL THEN SET tmp = "{}"; END IF;

SELECT "OK" status, tmp data;

END