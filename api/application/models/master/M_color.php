<?php

class M_color extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "m_color";
        $this->table_key = "M_ColorID";
    }

    function search( $d )
    {
        $l = ['records'=>[], 'total'=>0];

        $r = $this->db->query(
                "SELECT M_ColorID color_id, M_ColorName color_name, IFNULL(M_ColorClass, '') color_class,
                    IFNULL(M_ColorValue, '') color_value, IFNULL(M_ColorTextClass, '') color_text_class
                FROM `{$this->table_name}`
                WHERE `M_ColorName` LIKE ?
                AND `M_ColorIsActive` = 'Y'", [$d['search']]);
        if ($r)
        {
            $l['records'] = $r->result_array();
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            WHERE `M_ColorName` LIKE ?
            AND `M_ColorIsActive` = 'Y'", [$d['search']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
        }
            
        return $l;
    }

    // function save ( $d )
    // {
    //     $r = $this->db->set('M_ColorName', $d['category_name'])
    //                 ->set('M_ColorCode', $d['category_code']);
    //                 // ->set('M_ColorUserID', $d['user_id']);
    //     if (isset($d['category_id']))
    //     {
    //         $this->db->where('M_ColorID', $d['category_id'])
    //             ->update( $this->table_name );
    //         $id = $d['category_id'];
    //     }
    //     else
    //     {
    //         $this->db->insert( $this->table_name );
    //         $id = $this->db->insert_id();
    //     }

    //     if ($r)
    //     {
    //         return (object) ["status"=>"OK", "data"=>$id];
    //     }

    //     return (object) ["status"=>"ERR"];
    // }

    // function del ($id)
    // {
    //     $this->db->set('M_ColorIsActive', 'N')
    //             ->where('M_ColorID', $this->sys_input['id'])
    //             ->update($this->table_name);

    //     return true;
    // }
}

?>