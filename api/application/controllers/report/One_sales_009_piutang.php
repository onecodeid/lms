<?php

// Report Fee / Komisi Per Admin
//

class One_sales_009_piutang extends RPT_Controller
{
    var $report_code;

    function __construct()
    {
        parent::__construct();

        $this->load->library("pdf");
        $this->report_code = 'SALES-009';
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
        $r = $this->r_report->one_sales_009_piutang( $this->input->get('sdate'), $this->input->get('edate') );

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
                        'SUMBER LEAD'=>$v['lead_name'],
                        'KODE IKLAN'=>$v['so_ads_number'],
                        'NO PESANAN MP'=>$v['so_mp_number'],
                        'NAMA_CUSTOMER'=>$v['customer_name'],
                        'LEVEL_CUSTOMER'=>$cl_name,
                        'NAMA_ADMIN'=>$v['admin_name'],
                        'NAMA_ITEM'=>$v['item_name'],
                        'HARGA'=>$v['item_price'],
                        'POTONGAN'=>$v['item_disc_total'],
                        'QTY'=>$v['item_qty'],
                        'SUBTOTAL'=>$v['item_subtotal'],
                        'KUPON'=>$v['coupon_amount'],
                        'KODE KUPON'=>$v['coupon_code'],
                        'TOTAL'=>$v['item_subtotal'] - $v['coupon_amount']];
                }

                $this->csv($data, 'penjualan_detail_per_tanggal_' .
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
                'Laporan Daftar Piutang', 
                'Periode : ' . date('d/m/Y', strtotime($this->input->get('sdate'))) . ' - ' .  date('d/m/Y', strtotime($this->input->get('edate'))), 'L');

            
            $this->tableHeader($this->pdf);

            $this->pdf->SetFillColor(255,255,255);
            $this->pdf->SetTextColor(0,0,0);
            $this->pdf->SetFont('Arial','', 9);
            foreach ($data as $k => $v)
            {
                $cl_name = $this->levelName($v['level_code']);

                if ($this->pdf->GetY() > 18.5 && sizeof($data) > ($k+1))
                {
                    $this->pdf->SetFillColor(222,222,222);
                    $this->pdf->SetTextColor(0,0,0);
                    $this->pdf->Cell(25, 0.7, 'SUB TOTAL', 'LB', 0, 'C', 1);
                    $this->pdf->Cell(3, 0.7, number_format($grand_total), 'LBR', 0, 'R', 1);
                    $this->pdf->AddPage('L', 'A4');

                    $this->tableHeader($this->pdf);
                    $this->pdf->SetFillColor(255,255,255);
                    $this->pdf->SetTextColor(0,0,0);
                    $this->pdf->SetFont('Arial','', 9);
                }

                $this->pdf->Cell(1, 0.7, ($k+1), 'LB', 0, 'C', 1);
                $this->pdf->Cell(2, 0.7, date('d-m-Y', strtotime($v['so_date'])), 'LB', 0, 'C', 1);
                $this->pdf->Cell(2.5, 0.7, $v['so_number'], 'LB', 0, 'L', 1);
                $this->pdf->Cell(7.5, 0.7, $v['customer_name'], 'LB', 0, 'L', 1);
                // $this->pdf->Cell(2.5, 0.7, $v['admin_name'], 'LB', 0, 'L', 1);
                $this->pdf->Cell(9, 0.7, $v['item_name'], 'LB', 0, 'L', 1);
                $this->pdf->Cell(2, 0.7, number_format($v['invoice_total']), 'LB', 0, 'R', 1);
                $this->pdf->Cell(2, 0.7, number_format($v['invoice_paid']), 'LB', 0, 'R', 1);
                $this->pdf->Cell(2, 0.7, number_format($v['invoice_unpaid']), 'LBR', 0, 'R', 1);
                $this->pdf->Ln(0.7);

                $grand_total += $v['invoice_unpaid'];
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
        $me->Cell(2.5, 1, 'NO ORDER' , 'LTBR', 0, 'C', 1);
        $me->Cell(7.5, 1, 'NAMA SISWA' , 'LTBR', 0, 'C', 1);
        // $me->Cell(2.5, 1, 'ADMIN' , 'LTBR', 0, 'C', 1);
        $me->Cell(9, 1, 'NAMA KURSUS' , 'LTBR', 0, 'C', 1);
        $me->Cell(2, 1, 'TAGIHAN' , 'LTBR', 0, 'C', 1);
        $me->Cell(2, 1, 'TERBAYAR' , 'LTBR', 0, 'C', 1);
        $me->Cell(2, 1, 'SISA' , 'LTBR', 0, 'C', 1);

        $me->Ln(1);
    }
}
?>
