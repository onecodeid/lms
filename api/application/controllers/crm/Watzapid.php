<?php

class Watzapid extends WATZAP_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('crm/crm_watzapid');
    }

    function get_conf()
    {
        $r = $this->crm_watzapid->get();

        $this->sys_ok($r);
    }

    function save_conf()
    {
        $r = $this->crm_watzapid->save_conf( $this->sys_input, $this->sys_input['id'] );

        $this->sys_ok($r);
    }

    function status()
    {
        $r = json_decode($this->wat_curl($this->wat_url_status));

        // Save to configuration
        $x = $this->crm_watzapid->save($r);
        $this->sys_ok($r);
    }

    function validate_number()
    {
        $params = [];
        $params["number_key"] = $this->wat_num_keys[0];
        $params["phone_no"] = $this->sys_input['no'];

        $r = json_decode($this->wat_curl($this->wat_url_validate, $params));
        if ($r->status == "200")
            $this->sys_ok($r->message);
        else
            $this->sys_error($r);
    }

    function send_message()
    {
        $url = $this->wat_url_send_message;
        $params = [];
        $params["number_key"] = $this->sys_input['key']; //$this->wat_num_keys[0];
        $params["phone_no"] = $this->sys_input['no'];
        $params["message"] = isset($this->sys_input['text'])?$this->sys_input['text']:'TEST API WATZAP';

        if (isset($this->sys_input['img_url']))
        {
            $params["url"] = $this->sys_input['img_url'];
            $params["separate_caption"] = 1;
            $url = $this->wat_url_send_image;
        }

        $r = json_decode($this->wat_curl($url, $params));
        if ($r->status == "200")
            $this->sys_ok($r->message);
        else
            $this->sys_error($r);
    }

    function send_multi()
    {
        $params = [];
        $params["number_key"] = $this->sys_input['key']; //$this->wat_num_keys[0];
        // $params["phone_no"] = $this->sys_input['no'];
        $params["message"] = isset($this->sys_input['text'])?$this->sys_input['text']:'TEST API WATZAP';

        $rst = [];
        foreach ($this->sys_input['destinations'] as $k => $v)
        {
            $params["phone_no"] = $v;
            $r = json_decode($this->wat_curl($this->wat_url_send_message, $params));
            $r->no = $v;
            $rst[] = $r;
        }
        
        // if ($r->status == "200")
            $this->sys_ok($rst);
        // else
        //     $this->sys_error($r);
    }

    function send_multis()
    {
        $params = [];
        $params["number_key"] = $this->sys_input['key'];

        $rst = [];
        foreach ($this->sys_input['destinations'] as $k => $v)
        {
            $params['message'] = $v['text'];
            $params['phone_no'] = $v['number'];

            $r = json_decode($this->wat_curl($this->wat_url_send_message, $params));
            $r->no = $v['number'];
            $rst[] = $r;
        }

        $this->sys_ok($rst);
    }
}

?>