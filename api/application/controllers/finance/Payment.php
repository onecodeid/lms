<?php

class Payment extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('finance/f_payment');
    }

    function search()
    {
        $q = [
            'search' => '%'.$this->sys_input['search'].'%',
            'status' => $this->sys_input['status'],
            'sdate' => date('Y-m-d 00:00:00', strtotime($this->sys_input['sdate'])),
            'edate' => date('Y-m-d 23:59:59', strtotime($this->sys_input['edate'])),
            'page' => isset($this->sys_input['page'])? $this->sys_input['page'] : 1
        ];
        $r = $this->f_payment->search($q);

        $this->sys_ok($r);
    }

    function search_cod()
    {
        $q = [
            'search' => '%'.$this->sys_input['search'].'%',
            'status' => $this->sys_input['status'],
            'sdate' => date('Y-m-d 00:00:00', strtotime($this->sys_input['sdate'])),
            'edate' => date('Y-m-d 23:59:59', strtotime($this->sys_input['edate'])),
            'page' => isset($this->sys_input['page'])? $this->sys_input['page'] : 1
        ];
        $r = $this->f_payment->search_cod($q);

        $this->sys_ok($r);
    }

    function confirm()
    {
        $r = $this->f_payment->confirm($this->sys_input['payment_id'], $this->sys_user['user_id']);
        if ($r->status == "OK")
        {
            $this->load->library('phpmailer_lib');
            // PHPMailer object
            $mail = $this->phpmailer_lib->load();

            // SMTP configuration
            $this->load->model('system/s_email');
            $em = $this->s_email->get_rotate();

            $mail->isSMTP();
            $mail->Host     = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $em->email_username;
            $mail->Password = $em->email_password;
            $mail->SMTPSecure = 'tls';
            $mail->Port     = 587;
            
            $mail->setFrom($em->email_username, 'LPK Global');
            $maildata = json_decode($r->data);

            // Add a recipient
            $mail->addAddress("satukode.id@gmail.com");
            $mail->addAddress("naranis2020@gmail.com");
            $mail->addAddress($maildata->customer_email);

            // Email subject
            $mail->Subject = 'Konfirmasi Pembayaran untuk '. $maildata->customer_email;
                
            // Set email format to HTML
            $mail->isHTML(true);
            
            // Email body content
            // $mailContent = "<h1>Send HTML Email using SMTP in CodeIgniter</h1>
            //     <p>This is a test email sending using SMTP mail server with PHPMailer.</p>";
            
            $mailContent = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Paid Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333333;
        }
        .content {
            font-size: 16px;
            color: #555555;
        }
        .content p {
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            font-size: 14px;
            color: #aaaaaa;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Invoice Telah Terbayar</h1>
        </div>
        <div class="content">
            <p>Halo,</p>
            <p>Terima kasih, pembayaran untuk faktur Anda telah kami terima.</p>
            <p>Berikut adalah detail pembayaran Anda:</p>
            <p><strong>Nomor Invoice:</strong> '.$maildata->invoice_number.'</p>
            <p><strong>Nomor Pembayaran:</strong> '.$maildata->payment_number.'</p>
            <p>Jika Anda memiliki pertanyaan lebih lanjut, jangan ragu untuk menghubungi kami.</p>
            <p>Salam,</p>
            <p>LPK Global</p>
        </div>
        <div class="footer">
            <p>© 2024 Perusahaan Anda. Semua Hak Dilindungi.</p>
        </div>
    </div>
</body>
</html>
';
            //$this->load->view('mail/invoice', (array)$r, true);
            $mail->Body = $mailContent;

            // Send email
            if(!$mail->send()){
                $this->sys_error('Message could not be sent : ' . $mail->ErrorInfo);
                // echo 'Message could not be sent.';
                // echo 'Mailer Error: ' . $mail->ErrorInfo;
            }else{
                $this->sys_ok($r->data);
                // echo 'Email telah terkirim ke Customer';
                
                // $this->s_emailschedule->sent($x->S_EmailScheduleID);
            }

            
        }
            
        else
            $this->sys_error($r->message);
    }

    function save()
    {
        $r = $this->f_payment->save( $this->sys_input );
        if ($r->status == "OK") {
            $x = json_decode($r->data);

            // Upload Receipt
            if ($this->sys_input['receipt_img'] != '')
                $this->upload_receipt($x->order_id, $this->sys_input['receipt_img']);

            // Confirm
            // $this->f_payment->confirm($x->order_id, $this->sys_user['user_id']);

            $this->sys_ok(json_decode($r->data));
        }
            
        else
            $this->sys_error($r->message);
    }

    function upload_receipt($p_id, $r_img)
    {
        $ts = 'r_'.strtotime(date('Y-m-d H:i:s')).'.jpg';
        if ($_SERVER['SERVER_NAME'] == 'localhost')
            $this->base64_to_jpeg($r_img, "./uploads/receipts/".$ts);

        else
            $this->base64_to_jpeg($r_img, "/var/www/html/lms/uploads/receipts/".$ts);

        $this->f_payment->upload_receipt($p_id, $ts);
    }
}

?>