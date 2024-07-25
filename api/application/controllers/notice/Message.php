<?php

class Message extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('notice/nt_message');
    }

    function index()
    {
        return;
    }

    function get_unread()
    {
        $r = $this->nt_message->get_unread($this->sys_user['user_id'], $this->sys_user['group_id']);
        // if ($r->unread > 0)
            // $r->md5 = md5(json_encode($r->messages));
        // else
        //     $r->md5 = "";

        // $popups = $this->get_unread_popup();
        // $r->popups = $popups;
        
        $this->sys_ok($r);
    }

    function search_feed()
    {
        $prm = ['search'=>$this->sys_input['search'].'%', 'page'=>$this->sys_input['page'], 
            'uid'=>$this->sys_user['user_id'], 'gid'=>$this->sys_user['group_id']];

        $r = $this->nt_message->search($prm);
        foreach ($r['records'] as $k => $v) 
        {
            // $r['records'][$k]['watemplate_content_send'] = preg_replace("/".chr(10)."/", "%0a", $v['watemplate_content']);
            // $r['records'][$k]['watemplate_code'] = str_pad($k+1, 2, "0", STR_PAD_LEFT);
        }
        $this->sys_ok($r);
    }

    function search()
    {
        $prm = ['search'=>$this->sys_input['search'].'%', 'page'=>$this->sys_input['page']];

        $r = $this->nt_message->search($prm);
        foreach ($r['records'] as $k => $v) 
        {
            // $r['records'][$k]['watemplate_content_send'] = preg_replace("/".chr(10)."/", "%0a", $v['watemplate_content']);
            // $r['records'][$k]['watemplate_code'] = str_pad($k+1, 2, "0", STR_PAD_LEFT);
        }
        $this->sys_ok($r);
    }

    function get_one()
    {
        $r = $this->nt_message->get_one($this->sys_input['message_id']);
        $this->sys_ok($r);
    }

    // function get_unread_popup()
    // {
    //     $r = $this->s_notif->get_unread_popup($this->sys_user['user_id']);
    //     if ($r)
    //     {
    //         foreach ($r->messages as $k => $v)
    //         {
    //             if ($v->notif_action == 'POPUP-INFO')
    //                 $r->messages[$k]->notif_img = $v->notif_img == '' ? '' : base_url() . '../assets/img/info/' . $v->notif_img;
    //         }
    //     }
    //     // if ($r->unread > 0)
    //     $r->md5 = md5(json_encode($r->messages));
    //     // else
    //     //     $r->md5 = "";

    //     return $r;
    // }

    function set_read()
    {
        $r = $this->nt_message->set_read($this->sys_input['notice_id'], $this->sys_user['user_id']);
        $this->sys_ok($r);
    }

    function set_softread()
    {
        $r = $this->nt_message->set_read($this->sys_input['notice_id'], $this->sys_user['user_id'], 'S');
        $this->sys_ok($r);
    }

    function delete()
    {
        $r = $this->nt_message->delete($this->sys_input['message_id']);
        $this->sys_ok($r);
    }

    function save()
    {
        $this->sys_input['user_id'] = $this->sys_user['user_id'];
        if (isset($this->sys_input['message_id']))
            $r = $this->nt_message->save( $this->sys_input, $this->sys_input['message_id'] );
        else
            $r = $this->nt_message->save( $this->sys_input );
        
        $hdata = (array) json_decode($this->sys_input['hdata']);
        if ($r->status == "OK")
        {
            $r->data = (array) json_decode($r->data);
            if ($r->data['img'] != '')
                $r->data['mbuh'] = $this->save_img($hdata['m_img'], $r->data['img']);
            $this->sys_ok($r->data);
        }
        else
            $this->sys_error($r->message);
    }

    function save_img($base64_string, $output_file)
    {
        $uri = getcwd().'/../../lms/uploads/images/info/';
        // $uri = getcwd().'/../../global-assets/images/info/';
        return $this->base64_to_jpeg($base64_string, $uri.$output_file);
    }

    function get_img_md5()
    {
        $uri = getcwd().'/../../lms/uploads/images/info/';

        // Read the image file into a string
        $image_file = $uri . $this->sys_input['image'];
        $image_data = file_get_contents($image_file);

        // Encode the image data as base64
        $base64_image = base64_encode($image_data);

        // Output the base64-encoded string
        $this->sys_ok("data:image/jpeg;base64,".$base64_image);
    }
}

?>