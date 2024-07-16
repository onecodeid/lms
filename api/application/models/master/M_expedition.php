<?php

class M_expedition extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_expedition";
        $this->table_key = "M_ExpeditionID";
    }

    function search( $d )
    {
        $limit = isset($d['limit']) ? $d['limit'] : 10;
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $cod = isset($d['cod'])?"AND M_ExpeditionIsCOD = 'Y'":'';

        $r = $this->db->query(
                "SELECT *
                FROM `{$this->table_name}`
                WHERE `M_ExpeditionName` LIKE ?
                AND `M_ExpeditionIsActive` = 'Y'
                {$cod}
                ORDER BY FIELD(M_ExpeditionAdminOnly, 'N', 'Y'), M_ExpeditionName
                LIMIT {$limit} OFFSET {$offset}", [$d['expedition_name']]);
        if ($r)
        {
            $r = $r->result_array();
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            WHERE `M_ExpeditionName` LIKE ?
            AND `M_ExpeditionIsActive` = 'Y'
            {$cod}", [$d['expedition_name']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    function save ( $d )
    {
        $r = $this->db->set('M_ExpeditionName', $d['expedition_name'])
                    ->set('M_ExpeditionIsCOD', $d['expedition_cod'])
                    ->set('M_ExpeditionCODRate', $d['expedition_cod_rate']);
        if (isset($d['expedition_id']))
        {
            $this->db->where('M_ExpeditionID', $d['expedition_id'])
                ->update( $this->table_name );
            $id = $d['expedition_id'];
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
        $this->db->set('M_ExpeditionIsActive', 'N')
                ->where('M_ExpeditionID', $this->sys_input['id'])
                ->update($this->table_name);

        return true;
    }
}

?>