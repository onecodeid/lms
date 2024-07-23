<?php

class Item extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_item');
    }

    function search()
    {
        $r = $this->m_item->search([
                            'item_name'=>'%'.$this->sys_input['search'].'%', 
                            'page'=>$this->sys_input['page']
                            ]);
        foreach ($r['records'] as $k => $v)
            $r['records'][$k]['img_url'] = base_url() . 'master/itemimg/show_tmb?id=' . $v['M_ItemID'].'&t='.strtotime(date('Y-m-d H:i:s'));
        $this->sys_ok($r);
    }

    function search_w_price()
    {
        $cust_level = isset($this->sys_input['customer_level']) ? $this->sys_input['customer_level'] : 1;
        $search = isset($this->sys_input['search'])?'%'.$this->sys_input['search'].'%':'%';
        $r = $this->m_item->search_w_price([
                'item_name'=>$search, 
                'customer_level'=>$cust_level,
                'item_id'=>isset($this->sys_input['item_id'])?$this->sys_input['item_id']:0,
                'category'=>isset($this->sys_input['category'])?$this->sys_input['category']:0]);
        foreach ($r['records'] as $k => $v)
            $r['records'][$k]['img_url'] = base_url() . 'master/itemimg/show_tmb?id=' . $v['M_ItemID'];
        $this->sys_ok($r);
    }

    function save()
    {
        $id = 0;
        if (isset($this->sys_input['item_id']))
            $id = $this->sys_input['item_id'];

        $r = $this->m_item->save( $this->sys_input, $id );
        $this->img_tmb();

        // UPDATE thumbnail

        echo json_encode($r);
    }

    function del()
    {
        $r = $this->m_item->del( $this->sys_input );
        $this->sys_ok($r);
    }

    function img_tmb()
    {
        if (isset($this->sys_input['item_id']))
            $this->db->where('M_ItemID', $this->sys_input['item_id']);
        $r = $this->db->join('m_itemimg','M_ItemImgM_ItemID = M_ItemID')
                        ->get('m_item')
                        ->result_array();
        foreach($r as $k => $v)
        {
            $e = explode(',', $v['M_ItemImgUri']);
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

                $this->db->set('M_ItemImgTmbUri', '	data:image/jpeg;base64,'.$image_data_base64)->where('M_ItemImgID', $v['M_ItemImgID'])->update('m_itemimg');
            }
            
            // echo $image_data_base64;
            // echo "<br><br>";
            // imagedestroy($image);
        }
        
    }
}

?>