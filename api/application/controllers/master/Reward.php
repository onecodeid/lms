<?php

class Reward extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_reward');
    }

    function search()
    {
        $r = $this->m_reward->search(['search'=>$this->sys_input['search'].'%', 'page'=>$this->sys_input['page']]);
        $this->sys_ok($r);
    }

    function save()
    {
        $this->sys_input['user_id'] = $this->sys_user['user_id'];

        if (isset($this->sys_input['reward_image']))
            $this->sys_input['reward_image_tmb'] = $this->img_tmb($this->sys_input['reward_image']);

        if (isset($this->sys_input['reward_id']))
            $r = $this->m_reward->save( $this->sys_input, $this->sys_input['reward_id'] );
        else
            $r = $this->m_reward->save( $this->sys_input );
        
        if ($r->status == "OK")
            $this->sys_ok($r->data);
        else
            $this->sys_error('ERROR');
    }

    function del()
    {
        $r = $this->m_reward->del( $this->sys_input );
        $this->sys_ok($r);
    }

    function img_tmb($uri)
    {        
        $e = explode(',', $uri);
        if (isset($e[1]))
        {
            $x = $e[1];
            $image = imagecreatefromstring(base64_decode($x));
            $image = imagescale($image, 256, 256);
            // header('Content-Type: image/jpeg');

            ob_start (); 
            imagejpeg($image);
            $image_data = ob_get_contents ();
            ob_end_clean ();
            $image_data_base64 = base64_encode ($image_data);


            return 'data:image/jpeg;base64,'.$image_data_base64;
        }
    
        return null;
    }

    function show()
    {
        $r = $this->m_itemimg->get($this->sys_input['id']);
        if ($r)
        {
            $photo = explode(',', $r->M_ItemImgUri);
        }
        
        header("Content-type: image/jpg");
        $data = $photo[1];
        echo base64_decode($data);
    }

    function show_tmb()
    {
        $r = $this->m_itemimg->get($this->sys_input['id']);
        if ($r)
        {
            $photo = explode(',', $r->M_ItemImgTmbUri);
        }

        header("Content-type: image/jpg");
        $data = $photo[1];
        echo base64_decode($data);
    }
}

?>