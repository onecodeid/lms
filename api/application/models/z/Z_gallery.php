<?php

/**
 * undocumented class
 */
class Z_gallery extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "z_gallery";
        $this->table_key = "Z_GalleryID";
    }

    function get_youtube( $video_id )
    {
        $r = $this->db->select('Z_GalleryID id, Z_GalleryTitle title, IFNULL(Z_GalleryDescription, "") description,
                                Z_GalleryViews views, Z_GalleryUrl url')
                ->where('Z_GalleryVideoID', $video_id)
                ->get($this->table_name)
                ->row();

        if ($r)
        {
            $r->description = str_replace("\\n", "<br />", $r->description); // preg_replace("/(\\\n)/", "<br />", $r->description);
            return $r;
        }
            
        return false;
    }

    function get_video( $video_id )
    {
        $r = $this->db->select('Z_GalleryID id, Z_GalleryTitle title, IFNULL(Z_GalleryDescription, "") description,
                                Z_GalleryViews views, Z_GalleryUrl url')
                ->where('Z_GalleryID', $video_id)
                ->get($this->table_name)
                ->row();

        if ($r)
        {
            $r->description = str_replace("\\n", "<br />", $r->description);
            return $r;
        }
            
        return false;
    }

    function get_doc( $doc_id )
    {
        $r = $this->db->select('Z_GalleryID id, Z_GalleryTitle title, IFNULL(Z_GalleryDescription, "") description,
                                Z_GalleryViews views, Z_GalleryUrl url, Z_GalleryType `type`, Z_GallerySize `size`')
                ->where('Z_GalleryID', $doc_id)
                ->get($this->table_name)
                ->row();

        if ($r)
        {
            $r->type = preg_replace("(Z\-DOC-)", "", $r->type);
            $r->description = str_replace("\\n", "<br />", $r->description);
            return $r;
        }
            
        return false;
    }

    function save($d, $id = false)
    {
        $idx = $id;

        $this->db
                ->set('Z_GalleryTitle', $d['title'])
                ->set('Z_GalleryDesc', $d['caption'])
                ->set('Z_GalleryDescription', $d['caption']);
        if ($id)
        {
            $r = $this->db->where('Z_GalleryID', $id)
                    ->update($this->table_name);
        }
        else
        {
            $r = $this->db->set('Z_GalleryVideoID', isset($d['video_id'])?$d['video_id']:'')
                        ->set('Z_GalleryTmb', $d['tmb'])
                        ->set('Z_GalleryType', $d['type'])
                        ->set('Z_GalleryUrl', $d['url'])
                        ->set('Z_GalleryDuration', isset($d['duration'])?$d['duration']:'00:00:00')
                        ->set('Z_GalleryViews', isset($d['views'])?$d['views']:0)
                        ->set('Z_GallerySize', isset($d['size'])?$d['size']:0)
                        ->insert($this->table_name);
            $idx = $this->db->insert_id();
        }

        if ($r)
            return $idx; 
        return $r;
    }

    function search( $d )
    {
        $limit = isset($d['limit']) ? $d['limit'] : 10;
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $r = $this->db->query(
                "SELECT Z_GalleryID gallery_id,
                Z_GalleryType gallery_type,
                Z_GalleryTitle gallery_title,
                Z_GalleryDesc gallery_desc,
                Z_GalleryUrl gallery_url,
                Z_GalleryVideoID gallery_video_id,
                Z_GalleryTmb gallery_tmb,
                
                Z_GalleryDuration gallery_duration,
                Z_GalleryViews gallery_views,
                Z_GallerySize gallery_size

                FROM `{$this->table_name}`
                WHERE ((`Z_GalleryType` = ? AND ? <> '') OR ? = '')
                AND `Z_GalleryIsActive` = 'Y'
                ORDER BY Z_GalleryID DESC
                LIMIT {$limit} OFFSET {$offset}", 
                    [$d['type'], $d['type'], $d['type']]);

        if ($r)
        {
            $r = $r->result_array();
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(`{$this->table_key}`) n
            FROM `{$this->table_name}`
                WHERE ((`Z_GalleryType` = ? AND ? <> '') OR ? = '')
                AND `Z_GalleryIsActive` = 'Y'
                ORDER BY Z_GalleryID DESC", [$d['type'], $d['type'], $d['type']]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }
}

?>