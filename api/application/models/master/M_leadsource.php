<?php

class M_leadsource extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_leadsource";
        $this->table_key = "M_LeadSourceID";
    }

    function search( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT M_LeadSourceID leadsource_id, M_LeadSourceCode leadsource_code, M_LeadSourceName leadsource_name,
                    M_LeadSourceRemovable leadsource_removable, M_LeadSourceColor leadsource_color
                FROM `{$this->table_name}`
                WHERE `M_LeadSourceName` LIKE ?
                AND `M_LeadSourceIsActive` = 'Y'", [$d['leadsource_name']]);
        if ($r)
        {
            $l['records'] = $r->result_array();
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            WHERE `M_LeadSourceName` LIKE ?
            AND `M_LeadSourceIsActive` = 'Y'", [$d['leadsource_name']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }

    function save ( $d )
    {
        $r = $this->db->set('M_LeadSourceName', $d['leadsource_name'])
                    ->set('M_LeadSourceCode', $d['leadsource_code']);
                    // ->set('M_LeadSourceUserID', $d['user_id']);
        if (isset($d['leadsource_id']))
        {
            $this->db->where('M_LeadSourceID', $d['leadsource_id'])
                ->update( $this->table_name );
            $id = $d['leadsource_id'];
        }
        else
        {
            $this->db->insert( $this->table_name );
            $id = $this->db->insert_id();
        }

        if ($r)
        {
            return (object) ["status"=>"OK", "data"=>$id];
        }

        return (object) ["status"=>"ERR"];
    }

    function del ($id)
    {
        $this->db->set('M_LeadSourceIsActive', 'N')
                ->where('M_LeadSourceID', $this->sys_input['id'])
                ->update($this->table_name);

        return true;
    }
}

?>