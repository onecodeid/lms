BEGIN

DECLARE unread INTEGER;
DECLARE total INTEGER;
DECLARE msgs VARCHAR(5000);

SET unread = (SELECT COUNT(S_NotifID) FROM s_notif
				WHERE S_NotifToUID = uid AND S_NotifRead = "N" AND S_NotifIsActive = "Y" AND S_NotifIsSeller = seller);
SET total = (SELECT COUNT(S_NotifID) FROM s_notif
				WHERE S_NotifToUID = uid AND S_NotifIsActive = "Y" AND S_NotifIsSeller = seller);
				
SET msgs = (SELECT CONCAT("[",
			GROUP_CONCAT(
				JSON_OBJECT("notif_id", S_NotifID, "notif_msg", S_NotifMessage, "notif_code", M_NotifCode, "notif_read", S_NotifRead, "notif_time", S_NotifCreated, "notif_action", S_NotifAction, "notif_action_prop", S_NotifActionProp)
				ORDER BY FIELD(S_NotifRead, "N", "Y") ASC, S_NotifID DESC
				SEPARATOR ",") , "]")
			FROM s_notif
			JOIN m_notif ON M_NotifID = S_NotifM_NotifID
			WHERE S_NotifToUID = uid AND S_NotifIsActive = "Y" AND S_NotifIsSeller = seller
			LIMIT 10
			);
IF msgs IS NULL THEN SET msgs = "[]"; END IF;

SELECT "OK" status, CONCAT('{"unread":', unread,',"total":', total,',"messages":', msgs, '}') data;

END