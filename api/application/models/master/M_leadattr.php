<?php

class M_leadattr extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_leadattr";
        $this->table_key = "M_LeadAttrID";
    }

    function search( $d )
    {
        $limit = isset($d['limit']) ? $d['limit'] : 10;
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $r = $this->db->query(
                "SELECT M_LeadAttrID attr_id, M_LeadAttrName attr_name, M_LeadAttrCode attr_code
                FROM `{$this->table_name}`
                WHERE ((M_LeadAttrCode = ? AND ? <> \"\") OR ? = \"\") AND `M_LeadAttrName` LIKE ?
                AND `M_LeadAttrIsActive` = 'Y'
                LIMIT {$limit} OFFSET {$offset}", [$d['code'], $d['code'], $d['code'], $d['search']]);
        if ($r)
        {
            $l['records'] = $r->result_array();
        }

        $r = $this->db->query("SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            WHERE ((M_LeadAttrCode = ? AND ? <> \"\") OR ? = \"\") AND `M_LeadAttrName` LIKE ?
            AND `M_LeadAttrIsActive` = 'Y'", [$d['code'], $d['code'], $d['code'], $d['search']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    function save ( $d )
    {
        $r = $this->db->set('M_LeadAttrName', $d['attr_name'])
                    ->set('M_LeadAttrCode', $d['attr_code']);
                    // ->set('M_LeadTypeUserID', $d['user_id']);
        if (isset($d['attr_id']))
        {
            $this->db->where('M_LeadAttrID', $d['attr_id'])
                ->update( $this->table_name );
            $id = $d['attr_id'];
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
        $this->db->set('M_LeadAttrIsActive', 'N')
                ->where('M_LeadAttrID', $id)
                ->update($this->table_name);

        return true;
    }
}

?>