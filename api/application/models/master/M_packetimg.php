<?php

class M_packetimg extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_packetimg";
        $this->table_key = "M_PacketImgID";
    }

    function get( $id )
    {
        $r = $this->db->where('M_PacketImgM_PacketID', $id)
                    ->where('M_PacketImgIsActive', 'Y')
                    ->limit(1)
                    ->get($this->table_name)
                    ->row();
        if ($r)
            return $r;

        return false;
    }

    // function save ( $d )
    // {
    //     $r = $this->db->set('M_PacketName', $d['item_name'])
    //                 ->set('M_PacketAddress', $d['item_address'])
    //                 ->set('M_PacketM_KelurahanID', $d['item_kelurahan_id'])
    //                 ->insert( $this->table_name );
    //     if ($r)
    //     {
    //         return ["status"=>"OK", "data"=>$this->db->insert_id()];
    //     }

    //     return ["status"=>"ERR"];
    // }

    // function del ($id)
    // {
    //     $this->db->set('M_PacketIsActive', 'N')
    //             ->where('M_PacketID', $this->sys_input['id'])
    //             ->update($this->table_name);

    //     return true;
    // }
}

?>