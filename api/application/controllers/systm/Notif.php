<?php

class Notif extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('system/s_notif');
    }

    function index()
    {
        return;
    }

    function get_unread()
    {
        $r = $this->s_notif->get_unread($this->sys_user['user_id']);
        // if ($r->unread > 0)
            $r->md5 = md5(json_encode($r->messages));
        // else
        //     $r->md5 = "";

        $popups = $this->get_unread_popup();
        $r->popups = $popups;
        
        $this->sys_ok($r);
    }

    function get_unread_popup()
    {
        $r = $this->s_notif->get_unread_popup($this->sys_user['user_id']);
        if ($r)
        {
            foreach ($r->messages as $k => $v)
            {
                if ($v->notif_action == 'POPUP-INFO')
                    $r->messages[$k]->notif_img = $v->notif_img == '' ? '' : base_url() . '../assets/img/info/' . $v->notif_img;
            }
        }
        // if ($r->unread > 0)
        $r->md5 = md5(json_encode($r->messages));
        // else
        //     $r->md5 = "";

        return $r;
    }

    function set_read()
    {
        $r = $this->s_notif->set_read($this->sys_user['user_id']);
        $this->sys_ok($r);
    }

    function set_read_id()
    {
        $r = $this->s_notif->set_read_id($this->sys_user['user_id'], $this->sys_input['id']);
        $this->sys_ok($r);
    }
}

?>