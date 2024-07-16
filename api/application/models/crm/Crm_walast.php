<?php

class Crm_walast extends MY_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function log($d)
    {
        /**
         * INSERT INTO LOG */      
        $logdb = $this->load->database('log', true);
        
        $logdb->trans_start();

        $q = "INSERT INTO crmlog_wa(
                CrmLog_WaM_CustomerID,
                CrmLog_WaCrm_WaTemplateID,
                CrmLog_WaContent)
                VALUES(?,?,?)
        ";  
        $logdb->query($q, [$d['customer_id'], $d['wa_id'], $d['content']]);

        $id = $logdb->insert_id();

//         INSERT INTO table_listnames (name, address, tele)
// SELECT * FROM (SELECT 'Rupert', 'Somewhere', '022') AS tmp
// WHERE NOT EXISTS (
//     SELECT name FROM table_listnames WHERE name = 'Rupert'
// ) LIMIT 1;

        $q = "INSERT INTO `one-sales`.crm_walast(Crm_WaLastM_CustomerID, Crm_WaLastLogID, Crm_WaLastDate)
                SELECT ?, CrmLog_WaID, now()
                FROM crmlog_wa
                WHERE CrmLog_WaID = ? 
                AND NOT EXISTS (SELECT Crm_WaLastID FROM `one-sales`.crm_walast WHERE Crm_WaLastM_CustomerID = ? 
                AND Crm_WaLastIsActive = 'Y') LIMIT 1";
        $logdb->query($q, [$d['customer_id'], $id, $d['customer_id']]);

        $q = "UPDATE `one-sales`.crm_walast
                SET Crm_WaLastLogID = ?, Crm_WaLastDate = now()
                WHERE  Crm_WaLastM_CustomerID = ? AND Crm_WaLastIsActive = 'Y'";
        $logdb->query($q, [$id, $d['customer_id']]);

        $logdb->trans_complete();

        if ($logdb->trans_status() === FALSE) 
        {
            # Something went wrong.
            $logdb->trans_rollback();
            return FALSE;
        } 
        else 
        {
            # Everything is Perfect. 
            # Committing data to the database.
            $logdb->trans_commit();
            return $id;
        }
    }
}