<?php

class M_leadtype extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_leadtype";
        $this->table_key = "M_LeadTypeID";
    }

    function search( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT M_LeadTypeID leadtype_id, M_LeadTypeCode leadtype_code, M_LeadTypeName leadtype_name
                FROM `{$this->table_name}`
                WHERE `M_LeadTypeName` LIKE ?
                AND `M_LeadTypeIsActive` = 'Y'", [$d['leadtype_name']]);
        if ($r)
        {
            $l['records'] = $r->result_array();
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            WHERE `M_LeadTypeName` LIKE ?
            AND `M_LeadTypeIsActive` = 'Y'", [$d['leadtype_name']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }

    function save ( $d )
    {
        $r = $this->db->set('M_LeadTypeName', $d['leadtype_name'])
                    ->set('M_LeadTypeCode', $d['leadtype_code']);
                    // ->set('M_LeadTypeUserID', $d['user_id']);
        if (isset($d['leadtype_id']))
        {
            $this->db->where('M_LeadTypeID', $d['leadtype_id'])
                ->update( $this->table_name );
            $id = $d['leadtype_id'];
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
        $this->db->set('M_LeadTypeIsActive', 'N')
                ->where('M_LeadTypeID', $this->sys_input['id'])
                ->update($this->table_name);

        return true;
    }
}

?>