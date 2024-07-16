<?php

class Crm_salesdur extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "crm_salesdur";
        $this->table_key = "Crm_SalesDurID";
    }

    function search( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT Crm_SalesDurName salesdur_name, Crm_SalesDurMin salesdur_min, Crm_SalesDurMax salesdur_max,
                    Crm_SalesDurID salesdur_id,
                    date_sub(now(), interval Crm_SalesDurMax day) start_date,
                    date_sub(now(), interval Crm_SalesDurMin day) end_date
                FROM `{$this->table_name}`
                WHERE `Crm_SalesDurName` LIKE ?
                AND `Crm_SalesDurIsActive` = 'Y'", [$d['search']]);
        if ($r)
        {
            $l['records'] = $r->result_array();
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            WHERE `Crm_SalesDurName` LIKE ?
            AND `Crm_SalesDurIsActive` = 'Y'", [$d['search']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }

    function save ( $d )
    {
        $r = $this->db->set('Crm_SalesDurName', $d['salesdur_name'])
                    ->set('Crm_SalesDurMin', $d['salesdur_min'])
                    ->set('Crm_SalesDurMax', $d['salesdur_max']);
                    // ->set('Crm_SalesDurUserID', $d['user_id']);
        if (isset($d['salesdur_id']))
        {
            $this->db->where('Crm_SalesDurID', $d['salesdur_id'])
                ->update( $this->table_name );
            $id = $d['salesdur_id'];
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
        $this->db->set('Crm_SalesDurIsActive', 'N')
                ->where('Crm_SalesDurID', $this->sys_input['id'])
                ->update($this->table_name);

        return true;
    }
}

?>