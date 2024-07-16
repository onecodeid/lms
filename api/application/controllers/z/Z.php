<?php

class Z extends MY_Controller
{
    private $tmp_folder;
    private $pre_url;

    function __construct()
    {
        parent::__construct();

        $this->load->model('z/z_gallery');
        $this->tmp_folder = "/home/one/Web/one-member/z/";
        $this->pre_url = "https://register.zalfa.id/z/";
    }

    function uf()
    {
        $target_dir = $this->tmp_folder;
        $target_file = $target_dir . basename($_FILES["file"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $target_file = $target_dir . "tmp_z_preview." .$imageFileType;
        // Check if image file is a actual image or fake image
        // if(isset($_POST["submit"])) {
        // $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        // if($check !== false) {
        //     echo "File is an image - " . $check["mime"] . ".";
        //     $uploadOk = 1;
        // } else {
        //     echo "File is not an image.";
        //     $uploadOk = 0;
        // }
        // }

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) 
        {
            $r = [
                "file_name" => "tmp_z_preview." .$imageFileType,
                "file_type" => $imageFileType,
                "file_url" => $this->pre_url."tmp_z_preview." .$imageFileType,
                "tmp_name" => $_FILES["file"]["name"]
            ];
            // $this->sys_ok($r);
            if ($imageFileType == 'mp4')
            {
                $rnd = Date('Ymd-His');
                $r['tmb_name'] = 'tmb'.$rnd.'.jpg';
                
                shell_exec('ffmpeg -i '.$this->tmp_folder.'tmp_z_preview.mp4 -vf "select=eq(n\,0)" -q:v 3 '.$this->tmp_folder.$r['tmb_name']);
                $dur = shell_exec("ffmpeg -i '.$this->tmp_folder.'tmp_z_preview.mp4 2>&1 | grep Duration | awk '{print $2}' | tr -d ,");
                $tmb = $this->img_2_tmb_2($this->tmp_folder.$r['tmb_name']);
                sleep(1);
                $r['tmb'] = base64_encode($tmb);
                $r['duration'] = $dur;
                // $r['tmb_true'] = base64_encode(file_get_contents($this->tmp_folder.$r['tmb_name']));
            }

            $this->sys_ok($r);
            // echo "The file ". htmlspecialchars( basename( $_FILES["file"]["name"])). " has been uploaded.";
        } 
        else 
        {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    function yt_api()
    {
        $data = [];
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->YOUTUBE_V3_URL."?part=snippet,contentDetails,statistics&id=".$this->sys_input['vid_id']."&key=".$this->YOUTUBE_V3_API,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $response = json_decode($response);
        $data['title'] = $response->items[0]->snippet->title;
        $data['description'] = $response->items[0]->snippet->description;
        $data['thumbnail'] = $response->items[0]->snippet->thumbnails->medium->url;

        $d = new DateInterval($response->items[0]->contentDetails->duration);
        $data['duration'] = $d->format("%H:%I:%S");
        $data['views'] = $response->items[0]->statistics->viewCount;

        curl_close($curl);
        
        // print_r(json_decode($response)) ;
        $this->sys_ok($data);
    }

    function save()
    {
        $d = $this->sys_input;
        if (!isset($this->sys_input['id']))
        {
            if ($d['type'] == 'P-YOUTUBE' || $d['type'] == 'Z-VIDEO')
            {
                $type = pathinfo($d['tmb'], PATHINFO_EXTENSION);
                $d['tmb'] = 'data:image/'.preg_replace('/(jpg)/', 'jpeg', $type).';base64,'.base64_encode(file_get_contents($d['tmb']));
                $d['url'] = "https://www.youtube.com/watch?v=".$d['video_id'];
            }

            else if ($d['type'] == 'P-IMAGE')
            {
                $ext = pathinfo($d['url'], PATHINFO_EXTENSION);
                $base = basename($d['url']);
                $new = date('Ymd-His');

                $d['tmb'] = $this->img_2_tmb($d['url']);
                copy($this->tmp_folder.$base, $this->tmp_folder.$new.'.'.$ext);
                $d['url'] = $this->pre_url.$new.'.'.$ext;
            }

            else if ($d['type'] == 'P-VIDEO')
            {
                $ext = pathinfo($d['url'], PATHINFO_EXTENSION);
                $base = basename($d['url']);
                $new = date('Ymd-His');

                $d['tmb'] = 'data:image/jpeg;base64,'.$d['tmb'];
                copy($this->tmp_folder.$base, $this->tmp_folder.$new.'.'.$ext);
                $d['url'] = $this->pre_url.$new.'.'.$ext;
            }

            $r = $this->z_gallery->save($d);
        } else {
            $r = $this->z_gallery->save($d, $this->sys_input['id']);
        }
        
        if ($r)
            $this->sys_ok($r);
        else
            $this->sys_error('Something Wrong !');
    } 

    function img_2_tmb ($uri)
    {
        $b = base64_encode(file_get_contents($uri));
        $image = imagecreatefromstring(base64_decode($b));
        $size = getimagesize($uri);
        $w = 256;
        $h = $size[1] * 256 /$size[0];
        $image = imagescale($image, $w, $h);
        // header('Content-Type: image/jpeg');

        ob_start (); 
        imagejpeg($image);
        $image_data = ob_get_contents ();
        ob_end_clean ();
        $image_data_base64 = base64_encode ($image_data);

        return 'data:image/jpeg;base64,'.$image_data_base64;
    }

    function img_2_tmb_2($file) {
        $name = basename($file);
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        $w = 256;
        $h = $height * $w / $width;
        
        
        $src = imagecreatefromjpeg($file);
        $dst = imagecreatetruecolor($w, $h);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $width, $height);
        ob_start();
        imagejpeg($dst);
        // imagejpeg($dst, $this->tmp_folder.$name);
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    function search()
    {
        $r = $this->z_gallery->search(['search'=>'%', 'type'=>$this->sys_input['type'], 'page'=>$this->sys_input['page']]);
        $this->sys_ok($r);
    }

    function share()
    {
        $this->load->model('z/z_share');
        $r = $this->z_share->share($this->sys_user['customer_id'], $this->sys_input['gallery_id'], $this->sys_input['media'], $this->sys_input['share_token']);

        if ($r->status == "OK")
            $this->sys_ok($r->data);
        else
            $this->sys_error($r->message);
    }

    function get_info()
    {
        // echo '{"status":"OK","data":{"profile":{"code":"Z090100005s","name":"Wulan Sulis"},"point":{"share":"90","sales":"120928","share_rank":"2"},"order":{"last":{"id":2008,"date":"03 Feb 2021","number":"SO-21020063","courier":"Lainnya (Cargo, dsb), ","ds":"N","ds_note":"","status":"WH.Sent","status_note":"Sudah Dikirim","amount":4190400}}}}';
        // return;

        $this->load->model('z/z_');
        $r = $this->z_->get_info($this->sys_user['customer_id']);

        $section = [
            'profile' => ['code' => $r->cust_code, 'name' => $r->cust_name],
            'point' => ['share' => $r->share_point, 'sales' => $r->sales_point, 'share_rank' => $r->rank->rank],
            'order' => ['last' => $r->order_last]];
        
        $rx = [];
        
        if (isset($this->sys_input['section']))
        {
            $e = explode(",", $this->sys_input['section']);
            foreach ($e as $k => $v)
                $rx[$v] = $section[$v];

            $this->sys_ok($rx);
            return;
        }
        
        $this->sys_ok($section);
    }

    function login_customer()
    {
        $this->load->model('z/z_');
        $r = $this->z_->login_customer($this->sys_input['username'],
                                $this->sys_input['password']);

        if ($r->status == "OK")
        {
            $user = (array) json_decode($r->data);
            $user['ip'] = $_SERVER['REMOTE_ADDR'];
            $user['agent'] = $_SERVER['HTTP_USER_AGENT'];
            $token  = JWT::encode($user, $this->SECRET_KEY);
            
            $data = array(
                "user" => $user,
                "token" => $token
            );
                        
            $this->sys_ok($data);    
        }
        else
        {
            $this->sys_error($r->message);
        }
    }

    function logout_customer() 
    {
        $this->load->model('z/z_');
        $prm = $this->sys_input;
        try 
        {
            $this->z_->logout($this->sys_user['user_id']);
            $this->sys_ok("OK");
        } 
        catch(Exception $exc) 
        {
            $message = $exc->getMessage();
            $this->sys_error($message);
        }
    }

    function myorder()
    {
        $this->load->model('z/z_');
        $r = $this->z_->myorder($this->sys_user['customer_id'], $this->sys_input['start'], $this->sys_input['share_token']);

        if ($r->status == "OK")
            $this->sys_ok(json_decode($r->data));
        else
            $this->sys_error($r->message);
    }

    function myorder_detail()
    {
        // $this->load->model('sales/l_so');
        // $r = $this->l_so->get_one($this->sys_input['so_id']);
        $this->load->model('z/z_');
        $r = $this->z_->myorder_detail($this->sys_input['so_id']);
        echo json_encode($r);
    }

    function leaderboard()
    {
        $this->load->model('z/z_');
        $r = $this->z_->leaderboard();

        $this->sys_ok($r);
    }
}
?>