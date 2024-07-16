<?php

class Crm_info extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "crm_info";
        $this->table_key = "Crm_InfoID";
    }

    function search( $d )
    {
        $limit = isset($d['limit']) ? $d['limit'] : 10;
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

//         Crm_InfoDate	date NULL	
// Crm_InfoNumber	varchar(25) NULL	
// Crm_InfoTitle	varchar(255) NULL	
// Crm_InfoContent	text NULL	
// Crm_InfoImage	text NULL	
// Crm_InfoIsActive

        $r = $this->db->query(
                "SELECT Crm_InfoID info_id, Crm_InfoTitle info_title, Crm_InfoDate info_date, Crm_InfoNumber info_number, Crm_InfoContent info_content,
                    IFNULL(Crm_InfoImage, '') info_img, Crm_InfoUpStartDate info_sdate, Crm_InfoUpEndDate info_edate, Crm_InfoIsUp info_up, Crm_InfoExcerpt info_excerpt
                FROM `{$this->table_name}`
                WHERE `Crm_InfoTitle` LIKE ?
                AND `Crm_InfoIsActive` = 'Y'
                ORDER BY Crm_InfoTitle ASC
                LIMIT {$limit} OFFSET {$offset}", [$d['search']]);
        if ($r)
        {
            $r = $r->result_array();
            foreach ($r as $k => $v)
            {
                if ($v['info_img'] != '')
                {
                    // $img_url = base_url().'../assets/img/watzap/'.$v['info_img'];
                    // $r[$k]['img_url'] = $img_url;
                    // $r[$k]['img_uri'] = 'data:image/jpeg;base64,'.base64_encode(file_get_contents($img_url));
                }
                    
            }
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
            WHERE `Crm_InfoTitle` LIKE ?
            AND `Crm_InfoIsActive` = 'Y'", [$d['search']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    function save ( $d )
    {
        $img_name = "";
        if (isset($d['info_img']))
        {
            if ($d['info_img'] != '' && $d['info_img'] !=  null)
                $img_name = md5($d['info_title']).'.jpg';
        }

        $r = $this->db->set('Crm_InfoTitle', $d['info_title'])
                    ->set('Crm_InfoContent', $d['info_content'])
                    // ->set('Crm_InfoM_ColorID', $d['info_color'])
                    ->set('Crm_InfoImage', $img_name)
                    ->set('Crm_InfoUpStartDate', $d['info_sdate'])
                    ->set('Crm_InfoUpEndDate', $d['info_edate']);
                    // ->set('Crm_InfoUserID', $d['user_id']);
        if (isset($d['info_id']))
        {
            $this->db->where('Crm_InfoID', $d['info_id'])
                ->update( $this->table_name );
            $id = $d['info_id'];
        }
        else
        {
            $this->db->insert( $this->table_name );
            $id = $this->db->insert_id();
        }

        if ($r)
        {
            return (object) ["status"=>"OK", "data"=>["id"=>$id, "img"=>$img_name]];
        }

        return (object) ["status"=>"ERR"];
    }

    function del ($id)
    {
        $this->db->set('Crm_InfoIsActive', 'N')
                ->where('Crm_InfoID', $this->sys_input['id'])
                ->update($this->table_name);

        return true;
    }

    function get($id)
    {
        $r = $this->db->query(
                "SELECT Crm_InfoID info_id, Crm_InfoTitle info_title, Crm_InfoDate info_date, Crm_InfoNumber info_number, Crm_InfoContent info_content,
                    IFNULL(Crm_InfoImage, '') info_img, Crm_InfoUpStartDate info_sdate, Crm_InfoUpEndDate info_edate, Crm_InfoIsUp info_up, Crm_InfoExcerpt info_excerpt
                FROM `{$this->table_name}`
                WHERE `Crm_InfoID` = ?
                AND `Crm_InfoIsActive` = 'Y'", [$id]);
        if ($r)
        {
            $r = $r->row();
        }
    
        return $r;
    }

    // function pin ($d, $uid = 0)
    // {
    //     $r = $this->db->query("CALL sp_crm_info_pin(?, ?)", [$d['id'], $uid])
    //                 ->row();
    //     $this->clean_mysqli_connection($this->db->conn_id);

    //     return $r;
    // }

    
}

?>