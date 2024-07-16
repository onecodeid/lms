<?php

class MY_Controller extends CI_Controller
{
    public $sys_input;
    public $is_login;

    public $SECRET_KEY = "--one-secret-1924";
    public $RO_KEY = "bd505a3f96a95686f7ac2775e43a37fa";
    public $RO_URL = "https://pro.rajaongkir.com/api/";

    public $IPAYMU_KEY = "HWuK5V6Q9GRbAzoCG2tDbfbr4OBxk.";
    public $IPAYMU_URL = "https://my.ipaymu.com/api/";
    public $IPAYMU_URL2 = "https://api.ipaymu.com/api/";

    public $MEMBER_URL = "https://register.zalfa.id/";

    public $APP_MAIL_PASSWORD = "kjvwquzhbprhiebr";

    public $YOUTUBE_V3_API = "AIzaSyAnX9izL8bUOhs85CqGldFXwKHssKle01w";
    public $YOUTUBE_V3_URL = "https://www.googleapis.com/youtube/v3/videos";

    public $REPORT_EXCEL_URL = "https://platform.zalfa.id/uploads/reports/";

    function __construct()
    {
        parent::__construct();

        $this->load->library("Jwt");
        $this->load->database();

        $this->sys_input = json_decode($this->input->raw_input_stream, true);
        if (! $this->sys_input ) {
            if ( count($this->input->post()) > 0 ) {
                $this->sys_input = $this->input->post();
            } else {
                $this->sys_input = $this->input->get();
            }
        }
        // $this->sys_input = json_decode(json_decode(json_encode(file_get_contents("php://input"))));

        // CHECK USER TOKEN
        try {
            $prm  = $this->sys_input;
            if (! isset($prm["token"])) {
               $this->is_login = false;
            } else {
               $user = JWT::decode($prm["token"],$this->SECRET_KEY,true);
               unset($this->sys_input["token"]);
               $user = json_decode(json_encode($user),true);
               if ($user["user_id"] > 0 ) {
                  $this->is_login = true;
               }
               $this->sys_user = $user;
               $query = $this->db->query("update s_user SET S_UserLastLogin = now() WHERE S_UserID = ?", array($user["user_id"]));
               if (!$query) {
                 $message = $this->db->error();
                 $this->sys_error($message);
                 exit;
               }
               //update last accessed 
            }
        } catch(Exception $e) {
            $this->is_login = false;
        }
    }

    public function sys_ok($data) {
        echo json_encode(
           array(
              "status" => "OK",
              "data" => $data
           )
        );
    }

    public function sys_error($message) {
        echo json_encode(
           array(
              "status" => "ERR",
              "message" => $message
           )
        );
    }

    public function base64_to_jpeg($base64_string, $output_file) {
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

    public function image_resize($img, $size = [800, 600])
    {
        // Define the maximum dimensions for the resized image
        $max_width = $size[0];
        $max_height = $size[1];

        // Load the original image
        $original_image = imagecreatefromjpeg($img);

        // Get the dimensions of the original image
        $original_width = imagesx($original_image);
        $original_height = imagesy($original_image);

        // Calculate the new dimensions of the resized image while maintaining aspect ratio
        if ($original_width > $max_width || $original_height > $max_height) {
            $ratio = min($max_width / $original_width, $max_height / $original_height);
            $new_width = round($original_width * $ratio);
            $new_height = round($original_height * $ratio);
        } else {
            $new_width = $original_width;
            $new_height = $original_height;
        }

        // Create a new image with the new dimensions
        $resized_image = imagecreatetruecolor($new_width, $new_height);

        // Copy and resize the original image to the new image
        imagecopyresampled($resized_image, $original_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);

        // Save the resized image as a JPEG file
        imagejpeg($resized_image, 'path/to/resized/image.jpg', 90);

        // Free up memory by destroying the images
        imagedestroy($original_image);
        imagedestroy($resized_image);

    }

    public function phone_format($phone, $prefix = "+62 ")
    {
        $x = false;
        $matches = ["/^\+/"];
        $phone = preg_replace("/[\-\s]+/", "", $phone);
        foreach ($matches as $k => $v)
            if (!$x) $x = preg_match($v, $phone);

        if (!$x && preg_match("/^08/", $phone))
            return $prefix.substr($phone, 1);

        if (!$x && preg_match("/^62/", $phone))
            return $prefix.substr($phone, 2);

        if (!$x)
            return $prefix.$phone;

        return $phone;
    }
}

class RPT_Controller extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->library('pdf');
    }

    public static function my_header($me, $title = 'anu', $desc = 'asd', $unit = 'cm')
    {
        $mx = $unit == 'mm' ? 10 : 1;
        $width = $me->pdf->w - $me->pdf->lMargin - $me->pdf->rMargin; //$orientation == 'P' ? 19 : 28;
        $cp = 'me->company';
        $me->pdf->SetFont('Arial', 'B', 13);
        $gy = $me->pdf->GetY();

        $me->pdf->Image(base_url() . '/assets/images/logo-1-1.png', 0.8*$mx, 0.7*$mx, 3*$mx);

        $me->pdf->SetY($gy+(0.5*$mx));
        $me->pdf->SetFont('Arial', '', 18);
        $me->pdf->Cell($width, 0.5*$mx, $title, '', 1, 'R');
        
        $me->pdf->SetFont('Arial', '', 11);
        $me->pdf->Cell($width, 0.7*$mx, $desc, 'B', 1, 'R');
        $me->pdf->Ln(1*$mx);
    }

    public function csv($data, $filename)
    {
        $output = fopen("php://output",'w') or die("Can't open php://output");
        
        header("Content-Type:application/csv"); 
        header("Content-Disposition:attachment;filename=".$filename.".csv"); 

        $columns = [];
        foreach ($data as $k => $v)
        {
            if ($k == 0)
            {
                foreach ($v as $l => $w)
                    $columns[] = $l;
                fputcsv($output, $columns);
            }

            fputcsv($output, $v);
        }
                
        fclose($output) or die("Can't close php://output");
    }

    function levelName($l)
    {
        $cl_name = '';
        if ($l == 'CUST.DISTRIBUTOR') $cl_name = 'Dist';
        if ($l == 'CUST.AGENCY') $cl_name = 'Agen';
        if ($l == 'CUST.RESELLER') $cl_name = 'Resl';
        if ($l == 'CUST.ENDUSER') $cl_name = 'User';
        if ($l == 'CUST.MEMBER') $cl_name = 'Member';
        if ($l == 'CUST.OTHER') $cl_name = 'Lain';
        if ($l == 'CUST.FAMILY') $cl_name = 'Family';

        return $cl_name;
    }
}

class WATZAP_Controller extends MY_Controller
{
    public $wat_key = "0XZS8T3SDZT3D5XQ";
    public $wat_url_main = "https://api.watzap.id/";

    public $wat_url_status = "v1/checking_key";
    public $wat_url_validate = "v1/validate_number";
    public $wat_url_send_message = "v1/send_message";
    public $wat_url_send_image = "v1/send_image_url";

    public $wat_num_keys = [
        "hbBqUFjhzFYV1i5p"
    ];

    function __construct()
    {
        parent::__construct();
    }

    function wat_curl($url, $params = [])
    {
        
        $dataSending = Array();
        $dataSending["api-key"] = $this->wat_key;
        $dataSending["api_key"] = $this->wat_key;

        if ($params != [])
            $dataSending = array_merge($dataSending, $params);

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->wat_url_main . $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($dataSending),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    function wat_multi($url, $params = [])
    {

    }

}


?>