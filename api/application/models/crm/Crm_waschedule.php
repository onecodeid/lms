<?php

class Crm_waschedule extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "crm_waschedule";
        $this->table_key = "Crm_WaScheduleID";
    }

    function get_and_send($qty = 1)
    {
        $r = $this->db->query("SELECT *
                    FROM {$this->table_name}
                    WHERE Crm_WaScheduleCreated < now()
                    AND Crm_WaScheduleIsActive = 'Y'
                    AND Crm_WaScheduleStatus = '0'
                    LIMIT {$qty}")
                    ->result_array();
        if ($r)
        {
            return $r;
        }

        return [];
    }

    function set_status($id, $status)
    {
        $this->db->set('Crm_waScheduleStatus', $status)
                ->where('Crm_WaScheduleID', $id)
                ->update($this->table_name);

        return [$id, $status];
    }

    // function search( $d )
    // {
    //     $limit = isset($d['limit']) ? $d['limit'] : 10;
    //     $offset = ($d['page'] - 1) * $limit;
    //     $l = ['records'=>[], 'total'=>0, 'total_page'=>1];
    //     $pin = isset($d['pin']) ? $d['pin'] : '';

    //     $r = $this->db->query(
    //             "SELECT Crm_WaTemplateID watemplate_id, Crm_WaTemplateName watemplate_name, Crm_WaTemplateContent watemplate_content,
    //                 IFNULL(M_ColorClass, '') color_class, IFNULL(M_ColorName, '') color_name, IFNULL(M_ColorID, 0) color_id,
    //                 IFNULL(M_ColorTextClass, '') color_text_class, Crm_WaTemplatePin watemplate_pin,
    //                 IFNULL(Crm_WaTemplateImage, '') watemplate_img
    //             FROM `{$this->table_name}`
    //             LEFT JOIN m_color ON Crm_WaTemplateM_ColorID = M_ColorID
    //             WHERE `Crm_WaTemplateName` LIKE ?
    //             AND `Crm_WaTemplateIsActive` = 'Y'
    //             AND ((Crm_WaTemplatePin = ? AND ? <> '') OR ? = '')
    //             ORDER BY Crm_WaTemplateSpecial DESC, FIELD(Crm_WaTemplatePin, 'Y', 'N'), Crm_WaTemplateName
    //             LIMIT {$limit} OFFSET {$offset}", [$d['search'], $pin, $pin, $pin]);
    //     if ($r)
    //     {
    //         $r = $r->result_array();
    //         foreach ($r as $k => $v)
    //         {
    //             if ($v['watemplate_img'] != '')
    //             {
    //                 $img_url = base_url().'../assets/img/watzap/'.$v['watemplate_img'];
    //                 $r[$k]['img_url'] = $img_url;
    //                 $r[$k]['img_uri'] = 'data:image/jpeg;base64,'.base64_encode(file_get_contents($img_url));
    //             }
                    
    //         }
    //         $l['records'] = $r;
    //     }

    //     $r = $this->db->query(
    //         "SELECT count(`{$this->table_key}`) n
    //         FROM `{$this->table_name}`
    //         WHERE `Crm_WaTemplateName` LIKE ?
    //         AND `Crm_WaTemplateIsActive` = 'Y'
    //         AND ((Crm_WaTemplatePin = ? AND ? <> '') OR ? = '')", [$d['search'], $pin, $pin, $pin]);
    //     if ($r)
    //     {
    //         $l['total'] = $r->row()->n;
    //         $l['total_page'] = ceil($r->row()->n / $limit);
    //     }
            
    //     return $l;
    // }

    // function save ( $d )
    // {
    //     $img_name = "";
    //     if (isset($d['watemplate_img']))
    //     {
    //         if ($d['watemplate_img'] != '' && $d['watemplate_img'] !=  null)
    //             $img_name = md5($d['watemplate_name']).'.jpg';
    //     }

    //     $r = $this->db->set('Crm_WaTemplateName', $d['watemplate_name'])
    //                 ->set('Crm_WaTemplateContent', $d['watemplate_content'])
    //                 ->set('Crm_WaTemplateM_ColorID', $d['watemplate_color'])
    //                 ->set('Crm_WaTemplateImage', $img_name);
    //                 // ->set('Crm_WaTemplateUserID', $d['user_id']);
    //     if (isset($d['watemplate_id']))
    //     {
    //         $this->db->where('Crm_WaTemplateID', $d['watemplate_id'])
    //             ->update( $this->table_name );
    //         $id = $d['watemplate_id'];
    //     }
    //     else
    //     {
    //         $this->db->insert( $this->table_name );
    //         $id = $this->db->insert_id();
    //     }

    //     if ($r)
    //     {
    //         return (object) ["status"=>"OK", "data"=>["id"=>$id, "img"=>$img_name]];
    //     }

    //     return (object) ["status"=>"ERR"];
    // }

    
}

?>