<?php

class Crm_watzapid extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "crm_watzap";
        $this->table_key = "Crm_WatzapID";
    }

    function get()
    {
        $x = $this->db->select("Crm_WatzapID id, Crm_WatzapName name, Crm_WatzapLicenses licenses_key,
                Crm_WatzapInvoiceNumber invoice_number", false)
            ->where("Crm_WatzapIsActive", "Y")->get("crm_watzap")->row();
        if ($x)
        {
            $x->licenses_key = json_decode($x->licenses_key);
            if ($x->invoice_number != null && $x->invoice_number != "")
                $x->invoice_number = json_decode($x->invoice_number);
            return $x;
        }

        return null;
    }

    function save($data)
    {
        $r = $data;

        // Save to configuration
        $x = $this->get();

        $this->db->set("Crm_WatzapID", $r->data->id)->set("Crm_WatzapName", $r->data->name)
            ->set("Crm_WatzapLicenses", json_encode($r->data->licenses_key));
        if (!$x)
        {
            $this->db->insert( $this->table_name );
        }
        else
        {
            $this->db->where("Crm_WatzapIsActive", "Y")->update( $this->table_name );
        }

        return $r;
    }

    function save_conf($data, $id)
    {
        $this->db->set("Crm_WatzapInvoiceNumber", $data['invoice_number'])
                ->where("Crm_WatzapID", $id)
                ->update($this->table_name);

        return $id;
    }
}