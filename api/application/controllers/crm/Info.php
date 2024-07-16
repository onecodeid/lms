<?php

class Info extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('crm/crm_info');
    }

    function search()
    {
        $prm = ['search'=>$this->sys_input['search'].'%', 'page'=>$this->sys_input['page']];

        $r = $this->crm_info->search($prm);
        foreach ($r['records'] as $k => $v) 
        {
            // $r['records'][$k]['watemplate_content_send'] = preg_replace("/".chr(10)."/", "%0a", $v['watemplate_content']);
            // $r['records'][$k]['watemplate_code'] = str_pad($k+1, 2, "0", STR_PAD_LEFT);
        }
        $this->sys_ok($r);
    }

    function get()
    {
        $r = $this->crm_info->get($this->sys_input['id']);
        $r->info_img = base_url() . '../assets/img/info/' . $r->info_img;
        $this->sys_ok($r);
    }

    function save()
    {
        $this->sys_input['user_id'] = $this->sys_user['user_id'];
        if (isset($this->sys_input['info_id']))
            $r = $this->crm_info->save( $this->sys_input, $this->sys_input['info_id'] );
        else
            $r = $this->crm_info->save( $this->sys_input );
        
        if ($r->status == "OK")
        {
            if ($r->data['img'] != '')
                $r->data['mbuh'] = $this->save_img($this->sys_input['info_img'], $r->data['img']);
            $this->sys_ok($r->data);
        }
        else
            $this->sys_error('ERROR');
    }

    function del()
    {
        $r = $this->crm_info->del( $this->sys_input );
        $this->sys_ok($r);
    }

    function pin()
    {
        $r = $this->crm_watemplate->pin( $this->sys_input, $this->sys_user['user_id'] );
        if ($r->status == "OK")
            $this->sys_ok($r->data);
        else
            $this->sys_error($r->message);
    }

    function save_img($base64_string, $output_file)
    {
        $uri = getcwd().'/../assets/img/info/';
        return $this->base64_to_jpeg($base64_string, $uri.$output_file);
    }

    function base64_to_jpeg($base64_string, $output_file) {
        // open the output file for writing
        $ifp = fopen( $output_file, 'wb' ); 
    
        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode( ',', $base64_string );
    
        // we could add validation here with ensuring count( $data ) > 1
        fwrite( $ifp, base64_decode( $data[ 1 ] ) );
    
        // clean up the file resource
        fclose( $ifp ); 
    
        return $output_file; 
    }
}

?>