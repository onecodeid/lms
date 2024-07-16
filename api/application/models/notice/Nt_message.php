<?php

class Nt_message extends MY_Model
{
    private $pre_url;

    function __construct()
    {
        parent::__construct();

        $this->table_name = 'nt_message';
        $this->table_key = 'Nt_MessageID';
        $this->pre_url = ($_SERVER['SERVER_NAME'] == 'localhost' ? 'http://localhost/platform-zalfa/' : 'https://platform.zalfa.id/') . 'uploads/images/info/';
    }

    function get_unread($uid, $gid)
    {
        $q = "SELECT Nt_MessageID message_id, Nt_MessageTitle message_title, Nt_MessageContent message_content, Nt_MessageExcerpt message_excerpt,
                IFNULL(Nt_MessageImage, '') message_img, Nt_ReadStatus read_status
                FROM {$this->table_name}
                JOIN nt_recipient ON Nt_RecipientNt_MessageID = Nt_MessageID and Nt_RecipientIsActive = 'Y'

                LEFT JOIN s_user ON Nt_RecipientS_UserID = S_UserID AND Nt_RecipientS_UserID <> 0
                LEFT JOIN s_usergroup ON Nt_RecipientS_UserGroupID = S_UserGroupID AND Nt_RecipientS_UserGroupID <> 0

                LEFT JOIN nt_read ON Nt_ReadNt_MessageID = Nt_MessageID AND Nt_ReadIsActive = 'Y' AND Nt_ReadS_UserID = ?

                WHERE Nt_MessageIsActive = 'Y' 
                    AND ( ((S_UserID = ? AND ? <> 0) OR ? = 0)
                        OR ((S_UserGroupID = ? AND ? <> 0) OR ? = 0) )
                    AND (Nt_ReadID IS NULL OR Nt_ReadStatus != 'Y')
                ORDER BY Nt_MessageCreated DESC";

        $r = $this->db->query($q, [$uid, $uid, $uid, $uid, $gid, $gid, $gid])
                        ->result_array();

        if ($r)
        {
            foreach ($r as $k => $v)
            {
                if ($v['message_img'] != '')
                {
                    if (!preg_match("/(http)/", $v['message_img']))
                    {
                        $img_url = $this->pre_url.$v['message_img']; 
                    }
                    $r[$k]['message_img'] = $img_url . '?t=' . date('YmdHis');
                }

                $r[$k]['message_content'] = nl2br($v['message_content']);
                $r[$k]['message_excerpt'] = nl2br($v['message_excerpt']);
            }
            $l['records'] = $r;
        }

        return $r;
    }

    function get_one($id)
    {
        $q = "SELECT Nt_MessageID info_id, Nt_MessageTitle info_title, date(Nt_MessageCreated) info_date, '' info_number, Nt_MessageContent info_content,
                    IFNULL(Nt_MessageImage, '') info_img, Nt_MessageStartDate info_sdate, Nt_MessageEndDate info_edate, 'Y' info_up, '' info_excerpt
                FROM `{$this->table_name}`
                WHERE `Nt_MessageID` = ?
                AND `Nt_MessageIsActive` = 'Y'";

        $r = $this->db->query($q, [$id])
                        ->row();
        if ($r)
        {
            if ($r->info_img != '')
            {
                if (!preg_match("/(http)/", $r->info_img))
                    $img_url = $this->pre_url.$r->info_img; 
                $r->info_img = $img_url . '?t=' . date('YmdHis');
            }
        }

        return $r;
    }

    function get_feed($uid)
    {
        $q = "SELECT Nt_MessageID message_id, Nt_MessageTitle message_title, Nt_MessageContent message_content,
                IFNULL(Nt_MessageImage, '') message_img, Nt_ReadStatus read_status
                FROM {$this->table_name}
                JOIN nt_recipient ON Nt_RecipientNt_MessageID = Nt_MessageID and Nt_RecipientIsActive = 'Y'
                JOIN s_user ON (Nt_RecipientS_UserID = S_UserID OR Nt_RecipientS_UserGroupID = S_UserS_UserGroupID AND S_UserID = ?)
                LEFT JOIN nt_read ON Nt_ReadNt_MessageID = Nt_MessageID AND Nt_ReadS_UserID = ?
                WHERE Nt_MessageIsActive = 'Y')
                ORDER BY Nt_MessageCreated DESC";

        $r = $this->db->query($q, [$uid, $uid])
                        ->result_array();
        if ($r)
        {
            foreach ($r as $k => $v)
            {
                if ($v['message_img'] != '')
                {
                    if (!preg_match("/(http)/", $v['message_img']))
                    {
                        $img_url = $this->pre_url.$v['message_img']; 
                    }
                    $r[$k]['message_img'] = $img_url . '?t=' . date('YmdHis');
                }

                $r[$k]['message_content'] = nl2br($v['message_content']);
            }
            $l['records'] = $r;
        }

        return $r;
    }

    function set_read($msgid, $uid, $status = 'Y', $seller = 'N')
    {
        $r = $this->db->query("CALL sp_notice_read(?, ?, ?, ?)", [$msgid, $status, $uid, $seller])
                        ->row();
        $this->clean_mysqli_connection($this->db->conn_id);        

        return json_decode($r->data);
    }

    function search( $d )
    {
        $limit = isset($d['limit']) ? $d['limit'] : 10;
        $offset = ($d['page'] - 1) * $limit;
        $l = ['records'=>[], 'total'=>0, 'total_page'=>1];

        $uid = isset($d['uid'])?$d['uid']:0;
        $gid = isset($d['gid'])?$d['gid']:0;

        $this->load->model('notice/nt_read');
        $this->load->model('notice/nt_readcustomer');

        $r = $this->db->query(
                "SELECT Nt_MessageID info_id, Nt_MessageTitle info_title, date(Nt_MessageCreated) info_date, '' info_number, Nt_MessageContent info_content,
                    IFNULL(Nt_MessageImage, '') info_img, Nt_MessageStartDate info_sdate, Nt_MessageEndDate info_edate, 'Y' info_up, IFNULL(Nt_MessageExcerpt, '') info_excerpt,
                    CONCAT('[', GROUP_CONCAT(IF(Nt_RecipientS_UserID <> 0, JSON_OBJECT('id', S_UserID, 'text', S_UserFullName, 'group', false), 
                        JSON_OBJECT('id', S_UserGroupID, 'text', CONCAT('Grup ', S_UserGroupName), 'group', true) )), ']') recipients,
                    CONCAT('[', GROUP_CONCAT( 
                        IF(M_CustomerLevelID IS NOT NULL,
                            JSON_OBJECT('level_id', M_CustomerLevelID, 'level_name', M_CustomerLevelName), NULL) ) ,']') levels
                FROM `{$this->table_name}`
                LEFT JOIN nt_recipient ON Nt_RecipientNt_MessageID = Nt_MessageID AND Nt_RecipientIsActive = 'Y'
                LEFT JOIN s_user ON Nt_RecipientS_UserID = S_UserID AND Nt_RecipientS_UserID <> 0
                LEFT JOIN s_usergroup ON Nt_RecipientS_UserGroupID = S_UserGroupID AND Nt_RecipientS_UserGroupID <> 0
                LEFT JOIN m_customerlevel ON Nt_RecipientM_CustomerLevelID = M_CustomerLevelID AND Nt_RecipientM_CustomerLevelID <> 0
                WHERE `Nt_MessageTitle` LIKE ?
                AND `Nt_MessageIsActive` = 'Y'
                AND ( ((S_UserID = ? AND ? <> 0) OR ? = 0)
                    OR ((S_UserGroupID = ? AND ? <> 0) OR ? = 0) )
                GROUP BY Nt_MessageID
                ORDER BY Nt_MessageCreated ASC
                LIMIT {$limit} OFFSET {$offset}", [$d['search'], $uid, $uid, $uid, $gid, $gid, $gid]);

        if ($r)
        {
            $r = $r->result_array();
            foreach ($r as $k => $v)
            {
                if ($v['info_img'] != '')
                {
                    if (!preg_match("/(http)/", $v['info_img']))
                    {
                        $img_url = $this->pre_url.$v['info_img']; 
                    }
                    // $img_url = base_url().'../assets/img/watzap/'.$v['info_img'];
                    $r[$k]['img_url'] = $img_url;
                    $r[$k]['recipients'] = json_decode($v['recipients']);
                    $r[$k]['levels'] = json_decode($v['levels']);
                        if ($r[$k]['levels'] == null) $r[$k]['levels'] = [];
                    $r[$k]['who_read'] = $this->nt_read->get_who_read($v['info_id']);
                    $r[$k]['customer_read_cnt'] = $this->nt_readcustomer->get_cnt_read($v['info_id']);
                    // $r[$k]['img_uri'] = 'data:image/jpeg;base64,'.base64_encode(file_get_contents($img_url));
                }
                    
            }
            $l['records'] = $r;
        }

        $r = $this->db->query(
            "SELECT count(distinct `{$this->table_key}`) n
            FROM `{$this->table_name}`
                LEFT JOIN nt_recipient ON Nt_RecipientNt_MessageID = Nt_MessageID AND Nt_RecipientIsActive = 'Y'
                LEFT JOIN s_user ON Nt_RecipientS_UserID = S_UserID AND Nt_RecipientS_UserID <> 0
                LEFT JOIN s_usergroup ON Nt_RecipientS_UserGroupID = S_UserGroupID AND Nt_RecipientS_UserGroupID <> 0
            WHERE `Nt_MessageTitle` LIKE ?
            AND `Nt_MessageIsActive` = 'Y'
            AND ( ((S_UserID = ? AND ? <> 0) OR ? = 0)
                OR ((S_UserGroupID = ? AND ? <> 0) OR ? = 0) )", [$d['search'], $uid, $uid, $uid, $gid, $gid, $gid]);
        if ($r)
        {
            $l['total'] = $r->row()->n;
            $l['total_page'] = ceil($r->row()->n / $limit);
        }
            
        return $l;
    }

    function save ( $d, $id = 0 )
    {
        $hdata = (array) json_decode($d['hdata']);
        $hdata['m_excerpt'] = $this->generate_excerpt($hdata['m_content']);

        $img_name = "";
        if (isset($hdata['m_img']))
        {
            if ($hdata['m_img'] != '' && $hdata['m_img'] !=  null)
                $img_name = md5($hdata['m_title']).'.jpg';
        }
        $hdata['m_img'] = $img_name;

        $r = $this->db->query("CALL sp_notice_save(?, ?)", [$id, json_encode($hdata)])->row();
        $this->clean_mysqli_connection($this->db->conn_id);

        return $r;
    }

    function delete($msgid)
    {
        $r = $this->db->query("CALL sp_notice_delete(?)", [$msgid])
                        ->row();
        $this->clean_mysqli_connection($this->db->conn_id);        
        return json_decode($r->data);
    }

    // function set_read_id($uid, $id)
    // {
    //     $r = $this->db->query("CALL sp_system_notif_read_id(?, ?)", [$uid, $id])
    //                     ->row();
    //     $this->clean_mysqli_connection($this->db->conn_id);        

    //     return json_decode($r->data);
    // }

    function generate_excerpt($text, $length = 200, $suffix = '...') {
        // Remove any HTML tags from the text
        $text = strip_tags($text);
      
        // Trim the text to the specified length
        $text = trim(substr($text, 0, $length));
      
        // Find the last word boundary before the end of the string
        $last_space = strrpos($text, ' ');
        if ($last_space !== false) {
          $text = substr($text, 0, $last_space);
        }
      
        // Add the suffix to the end of the excerpt
        $text .= $suffix;
      
        // Return the generated excerpt
        return $text;
    }
      
}
?>
