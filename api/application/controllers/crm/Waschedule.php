<?php

class Waschedule extends WATZAP_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('crm/crm_waschedule');
    }

    function get_and_send()
    {
        $qty = 1;
        if (isset($this->sys_input['qty']))
            $qty = $this->sys_input['qty'];

        $r = $this->crm_waschedule->get_and_send($qty);
        $rst = [];

        foreach ($r as $k => $v)
        {
            $dest = $this->phone_format($v['Crm_WaScheduleDestNumber'], '62');

            $url = $this->wat_url_send_message;
            $params = [];
            $params["number_key"] = $v['Crm_WaScheduleSenderKey']; //$this->wat_num_keys[0];
            $params["phone_no"] = $dest;
            $params["message"] = $v['Crm_WaScheduleText'];
    
            if ($v['Crm_WaScheduleImage'] != '' AND $v['Crm_WaScheduleImage'] != null)
            { 
                $params["url"] =  $v['Crm_WaScheduleImage'];
                $params["separate_caption"] = 1;
                $url = $this->wat_url_send_image;
            }

            $s = json_decode($this->wat_curl($url, $params));
            $rst[] = $s;

            $this->crm_waschedule->set_status($v['Crm_WaScheduleID'], $s->status);
            // if ($s->status == "200")
            //     $this->sys_ok($s->message);
            // else
            //     $this->sys_error($s);
        }

        $this->sys_ok($rst);
    }

    // function search()
    // {
    //     $prm = ['search'=>$this->sys_input['search'].'%', 'page'=>$this->sys_input['page']];
    //     if (isset($this->sys_input['pin']))
    //         $prm['pin'] = $this->sys_input['pin'];
            
    //     $r = $this->crm_watemplate->search($prm);
    //     foreach ($r['records'] as $k => $v) 
    //     {
    //         $r['records'][$k]['watemplate_content_send'] = preg_replace("/".chr(10)."/", "%0a", $v['watemplate_content']);
    //         $r['records'][$k]['watemplate_code'] = str_pad($k+1, 2, "0", STR_PAD_LEFT);
    //     }
    //     $this->sys_ok($r);
    // }

    // function save()
    // {
    //     $this->sys_input['user_id'] = $this->sys_user['user_id'];
    //     if (isset($this->sys_input['watemplate_id']))
    //         $r = $this->crm_watemplate->save( $this->sys_input, $this->sys_input['watemplate_id'] );
    //     else
    //         $r = $this->crm_watemplate->save( $this->sys_input );
        
    //     if ($r->status == "OK")
    //     {
    //         if ($r->data['img'] != '')
    //             $r->data['mbuh'] = $this->save_img($this->sys_input['watemplate_img'], $r->data['img']);
    //         $this->sys_ok($r->data);
    //     }
    //     else
    //         $this->sys_error('ERROR');
    // }
}

?>