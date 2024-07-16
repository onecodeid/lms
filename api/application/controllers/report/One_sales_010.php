<?php

// Report Fee / Komisi Per Admin
//

class One_sales_010 extends RPT_Controller
{
    var $report_code;

    function __construct()
    {
        parent::__construct();

        $this->load->library("pdf");
        $this->report_code = 'SALES-010';
    }

    function index() {
        $this->pdf = new FPDF("L","cm",array(21,29.7));
        $this->pdf->SetAutoPageBreak(true,1);

        $this->pdf->rptclass = $this;
        $this->pdf->rptTitle = '-';
        // $this->pdf->header_func = "my_header";
        $this->pdf->footer_func = "my_footer";

        $this->pdf->SetFont('Arial','', 11);

        // Get data
        $this->load->model('report/r_report');
        $r = $this->r_report->one_sales_010( $this->input->get('uid'), $this->input->get('sdate'), $this->input->get('edate'), $this->input->get('levelid') );

        if (isset($this->sys_input['out']))
        {
            if ($this->sys_input['out'] == 'csv')
            {
                $data = [];
                foreach ($r[1] as $k => $v)
                {
                    $cl_name = $this->levelName($v['level_code']);
                    $data[] = ['TANGGAL'=>date('d-m-Y', strtotime($v['so_date'])),
                        'NOMOR'=>$v['so_number'],
                        'NAMA_CUSTOMER'=>$v['customer_name'],
                        'PHONE_CUSTOMER'=>$v['customer_phone'],
                        'LEVEL_CUSTOMER'=>$cl_name,
                        'NAMA_ITEM'=>$v['item_name'],
                        'HARGA'=>$v['item_price'],
                        'POTONGAN'=>$v['item_disc_total'],
                        'QTY'=>$v['item_qty'],
                        'SUBTOTAL'=>$v['item_subtotal']];
                }

                $this->csv($data, 'penjualan_detail_per_admin_per_jenjang_' .
                    (isset($r[0][0])?$r[0][0]['S_UserUsername']:'admin') . '_' .
                    date('d-m-Y', strtotime($this->input->get('sdate'))) . '_' .  
                    date('d-m-Y', strtotime($this->input->get('edate'))));
                return;
            }
        }

        // print_r($r);
        //
        $grand_total = 0;
        $sub_total = 0;

        if ($r)
        {
            $data = [];
            if (isset($r[1]))
                $data = $r[1];
            $r = $r[0][0];
            $this->pdf->SetMargins(0.7, 0.5, 0.5);
            $this->pdf->AddPage('L', 'A4');

            $hy = $this->pdf->GetY();
            // $this->pdf->Image(base_url() . '/assets/images/logo-1-1.png', 0.8, 0.7, 3);
            $this->my_header($this, 
                'Laporan Detail Penjualan Per Admin Per Jenjang', 
                'Admin : '.$r['S_UserUsername'].' | Periode : ' . date('d/m/Y', strtotime($this->input->get('sdate'))) . ' - ' .  date('d/m/Y', strtotime($this->input->get('edate'))), 'L');

            
            $this->tableHeader($this->pdf);

            $this->pdf->SetFillColor(255,255,255);
            $this->pdf->SetTextColor(0,0,0);
            $this->pdf->SetFont('Arial','', 9);
            foreach ($data as $k => $v)
            {
                $cl_name = $this->levelName($v['level_code']);

                $this->pdf->Cell(1, 0.7, $k+1, 'LB', 0, 'C', 1);
                $this->pdf->Cell(2, 0.7, date('d-m-Y', strtotime($v['so_date'])), 'LB', 0, 'C', 1);
                $this->pdf->Cell(3, 0.7, $v['so_number'], 'LB', 0, 'L', 1);
                $this->pdf->Cell(7, 0.7, $v['customer_name'] . '  -  ' . $v['customer_phone'], 'LB', 0, 'L', 1);
                $this->pdf->Cell(9, 0.7, $v['item_name'], 'LB', 0, 'L', 1);
                $this->pdf->Cell(2, 0.7, number_format($v['item_price']-$v['item_disc_total']), 'LB', 0, 'R', 1);
                $this->pdf->Cell(1, 0.7, number_format($v['item_qty']), 'LB', 0, 'R', 1);
                $this->pdf->Cell(3, 0.7, number_format($v['item_subtotal']), 'LBR', 0, 'R', 1);
                $this->pdf->Ln(0.7);

                $grand_total += $v['item_subtotal'];
            }

            $this->pdf->SetFillColor(222,222,222);
            $this->pdf->SetTextColor(0,0,0);
            $this->pdf->Cell(25, 0.7, 'GRAND TOTAL', 'LB', 0, 'C', 1);
            $this->pdf->Cell(3, 0.7, number_format($grand_total), 'LBR', 0, 'R', 1);
        }


        $this->pdf->Output();
    }

//   function myFooter($me) {
//     $me->pdf->SetFont("ArialNarrow","",8);
//     $me->pdf->SetXY(1,-1);
//     $me->pdf->MultiCell(19,1,"LKK/MCU/2015/" ,"","C");
//     $me->pdf->SetXY(1,-1);
//     $me->pdf->MultiCell(19,1,"Halaman : " . $me->pdf->PageNo() ,"","R");
//   }

//   function print_total($me, $total)
//   {
//     $me->pdf->Ln(0.25);
//     $me->pdf->Cell(0.5, 0.7, '', '', 0);
//     $me->pdf->Cell(12.5, 0.7, 'TOTAL', 'LBT', 0, 'C', 1);
//     $me->pdf->Cell(6, 0.7, number_format($total, 0), 'RBTL', 0, 'R', 1);
//     $me->pdf->Ln(0.7);
//   }

//   function print_grandtotal($me, $gtotal)
//   {
//     $me->pdf->Ln(0.25);
//     $me->pdf->Cell(0.5, 0.7, '', '', 0);
//     $me->pdf->Cell(12.5, 0.7, 'GRAND TOTAL', 'LBT', 0, 'C', 1);
//     $me->pdf->Cell(6, 0.7, number_format($gtotal, 0), 'RBTL', 0, 'R', 1);
//     $me->pdf->Ln(0.7);
//   }

    function tableHeader($me)
    {
        $me->SetFillColor(0,0,0);
        $me->SetTextColor(255,255,255);
        $this->pdf->SetFont('Arial','', 10);
        $me->Cell(1, 1, 'NO' , 'LTBR', 0, 'C', 1);
        $me->Cell(2, 1, 'TANGGAL' , 'LTBR', 0, 'C', 1);
        $me->Cell(3, 1, 'NO ORDER' , 'LTBR', 0, 'C', 1);
        $me->Cell(7, 1, 'CUSTOMER' , 'LTBR', 0, 'C', 1);
        $me->Cell(9, 1, 'NAMA ITEM' , 'LTBR', 0, 'C', 1);
        $me->Cell(2, 1, 'HARGA' , 'LTBR', 0, 'C', 1);
        $me->Cell(1, 1, 'QTY' , 'LTBR', 0, 'C', 1);
        $me->Cell(3, 1, 'SUBTOTAL' , 'LTBR', 0, 'C', 1);

        $me->Ln(1);
    }
}
?>
