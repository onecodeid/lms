BEGIN

-- DECLARE @CURR_MONTH_START date;
-- DECLARE @CURR_MONTH_END date;
-- DECLARE @LAST_MONTH_START date;
-- DECLARE @LAST_MONTH_END date;
-- DECLARE @THREE_MONTH_START date;
-- DECLARE @THREE_MONTH_END date;

SET @CURR_MONTH_START = DATE_ADD(LAST_DAY(DATE_SUB(now(), INTERVAL 1 MONTH)), INTERVAL 1 DAY);
SET @CURR_MONTH_END = LAST_DAY(now());
SET @LAST_MONTH_START = DATE_ADD(LAST_DAY(DATE_SUB(now(), INTERVAL 2 MONTH)), INTERVAL 1 DAY);
SET @LAST_MONTH_END = LAST_DAY(DATE_SUB(now(), INTERVAL 1 MONTH));
SET @THREE_MONTH_START = DATE_ADD(LAST_DAY(DATE_SUB(now(), INTERVAL 3 MONTH)), INTERVAL 1 DAY);
SET @THREE_MONTH_END = LAST_DAY(DATE_SUB(now(), INTERVAL 2 MONTH));

SET @sql = CONCAT('SELECT S_UserID user_id, S_UserUsername user_name, M_CustomerID customer_id, M_CustomerName customer_name, 
						M_CustomerLevelID level_id, M_CustomerLevelName level_name,
						SUM(IF(date(L_SoDate) BETWEEN @CURR_MONTH_START AND @CURR_MONTH_END, L_SoTotal, 0)) curr_total,
						SUM(IF(date(L_SoDate) BETWEEN @LAST_MONTH_START AND @LAST_MONTH_END, L_SoTotal, 0)) last_total,
						SUM(IF(date(L_SoDate) BETWEEN @THREE_MONTH_START AND @THREE_MONTH_END, L_SoTotal, 0)) three_total
						FROM l_so
						JOIN f_payment ON F_PaymentL_SoID = L_SoID AND F_PaymentIsActive = "Y" AND F_PaymentConfirmed = "Y"
						JOIN s_user ON L_SoUserID = S_UserID
						JOIN m_customer ON L_SoM_CustomerID = M_CustomerID
						JOIN m_customerlevel ON M_CustomerM_CustomerLevelID = M_CustomerLevelID
						WHERE L_SoIsActive = "Y" AND date(L_SoDate) BETWEEN @THREE_MONTH_START AND @CURR_MONTH_END
						AND L_SoUserID = ?
						GROUP BY L_SoUserID, L_SoM_CustomerID');

IF orderby = "CURR_MONTH_ASC" THEN
	SET @sql = CONCAT(@sql, ' ORDER BY curr_total ASC');
ELSEIF orderby = "CURR_MONTH_DESC" THEN
	SET @sql = CONCAT(@sql, ' ORDER BY curr_total DESC');
ELSEIF orderby = "LAST_MONTH_ASC" THEN
	SET @sql = CONCAT(@sql, ' ORDER BY last_total ASC');
ELSEIF orderby = "LAST_MONTH_DESC" THEN
	SET @sql = CONCAT(@sql, ' ORDER BY last_total DESC');
ELSEIF orderby = "THREE_MONTH_ASC" THEN
	SET @sql = CONCAT(@sql, ' ORDER BY three_total ASC');
ELSEIF orderby = "THREE_MONTH_DESC" THEN
	SET @sql = CONCAT(@sql, ' ORDER BY three_total DESC');
ELSEIF orderby = "NAME_ASC" THEN
	SET @sql = CONCAT(@sql, ' ORDER BY M_CustomerName ASC');
ELSEIF orderby = "NAME_DESC" THEN
	SET @sql = CONCAT(@sql, ' ORDER BY M_CustomerName DESC');
ELSEIF orderby = "LEVEL_ASC" THEN
	SET @sql = CONCAT(@sql, ' ORDER BY M_CustomerLevelName, M_CustomerName ASC');
ELSEIF orderby = "LEVEL_DESC" THEN
	SET @sql = CONCAT(@sql, ' ORDER BY M_CustomerLevelName, M_CustomerName DESC');
END IF;

-- TOTAL
SET @sqlx = CONCAT('SELECT count(*) total, ceil(count(*)/10) total_page FROM (', @sql, ') xxx LIMIT 10 OFFSET ', @offset);

PREPARE stmtx FROM @sqlx;
EXECUTE stmtx USING adminid;
DEALLOCATE PREPARE stmtx;

-- DATA
SET @offset = (ppage - 1) * 10;
SET @sql = CONCAT('SELECT * FROM (', @sql, ') xxx LIMIT 10 OFFSET ', @offset);

PREPARE stmt FROM @sql;
EXECUTE stmt USING adminid;
DEALLOCATE PREPARE stmt;
						
-- SELECT S_UserID, S_UserUsername, M_CustomerID, M_CustomerName,
-- 						SUM(IF(date(L_SoDate) BETWEEN @CURR_MONTH_START AND @CURR_MONTH_END, L_SoTotal, 0)) curr_total,
-- 						SUM(IF(date(L_SoDate) BETWEEN @LAST_MONTH_START AND @LAST_MONTH_END, L_SoTotal, 0)) last_total,
-- 						SUM(IF(date(L_SoDate) BETWEEN @THREE_MONTH_START AND @THREE_MONTH_END, L_SoTotal, 0)) three_total
-- 						FROM l_so
-- 						JOIN f_payment ON F_PaymentL_SoID = L_SoID AND F_PaymentIsActive = "Y" AND F_PaymentConfirmed = "Y"
-- 						JOIN s_user ON L_SoUserID = S_UserID
-- 						JOIN m_customer ON L_SoM_CustomerID = M_CustomerID
-- 						WHERE L_SoIsActive = "Y" AND L_SoDate BETWEEN CONCAT(@THREE_MONTH_START, " 00:00:00") AND CONCAT(@CURR_MONTH_END, " 23:59:59")
-- 						AND L_SoUserID = adminid
-- 						GROUP BY L_SoUserID, L_SoM_CustomerID;

END