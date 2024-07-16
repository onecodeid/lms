BEGIN

SELECT M_CustomerName cust_name, M_CustomerCode cust_code, Z_SharePoint score
FROM z_share
JOIN m_customer ON Z_ShareM_CustomerID = M_CustomerID
WHERE Z_ShareIsActive = "Y"
ORDER BY Z_SharePoint DESC
LIMIT 25;

END