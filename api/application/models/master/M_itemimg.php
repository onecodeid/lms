<?php

class M_itemimg extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_itemimg";
        $this->table_key = "M_ItemImgID";
    }

    function get( $id )
    {
        $r = $this->db->where('M_ItemImgM_ItemID', $id)
                    ->where('M_ItemImgIsActive', 'Y')
                    ->limit(1)
                    ->get($this->table_name)
                    ->row();
        if ($r)
            return $r;

        return false;
    }

    // function save ( $d )
    // {
    //     $r = $this->db->set('M_ItemName', $d['item_name'])
    //                 ->set('M_ItemAddress', $d['item_address'])
    //                 ->set('M_ItemM_KelurahanID', $d['item_kelurahan_id'])
    //                 ->insert( $this->table_name );
    //     if ($r)
    //     {
    //         return ["status"=>"OK", "data"=>$this->db->insert_id()];
    //     }

    //     return ["status"=>"ERR"];
    // }

    // function del ($id)
    // {
    //     $this->db->set('M_ItemIsActive', 'N')
    //             ->where('M_ItemID', $this->sys_input['id'])
    //             ->update($this->table_name);

    //     return true;
    // }
}

?>