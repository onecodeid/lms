<?php

// Report Fee / Komisi Per Admin
//

class One_fin_005 extends RPT_Controller
{
    var $report_code;

    function __construct()
    {
        parent::__construct();

        $this->load->library("pdf");
        $this->report_code = 'FIN-005';
    }

    function index() {
        $this->pdf = new FPDF("P","cm",array(21,29.7));
        $this->pdf->SetAutoPageBreak(true,1);

        $this->pdf->rptclass = $this;
        $this->pdf->rptTitle = '-';
        // $this->pdf->header_func = "my_header";
        $this->pdf->footer_func = "my_footer";

        $this->pdf->SetFont('Arial','', 11);

        // Get data
        $this->load->model('report/r_report');
        $r = $this->r_report->one_fin_005( $this->input->get('sdate'), $this->input->get('edate') );
        // print_r($r);
        //
        $nett_sale = 0;

        if ($r)
        {
            $data = $r[0][0];
            $nett_sale = $data['total'] - $data['codcost'] - $data['expcost'];
            $gross_profit = $nett_sale - $data['hpptotal'];
            // print_r($data);
            $this->pdf->SetMargins(0.7, 0.5, 0.5);
            $this->pdf->AddPage('P', 'A4');
            // $this->pdf->SetFillColor(255,255,255);
            // $this->pdf->SetTextColor(0,0,0);
            // $this->pdf->SetFont('Arial','', 9);

            $hy = $this->pdf->GetY();
            // $this->pdf->Image(base_url() . '/assets/images/logo-1-1.png', 0.8, 0.7, 3);
            $this->my_header($this, 
                'Laporan Laba Kotor', 
                'Periode : ' . date('d/m/Y', strtotime($this->input->get('sdate'))) . ' - ' .  date('d/m/Y', strtotime($this->input->get('edate'))));

            $this->pdf->SetFont("Arial","B",9);
            $this->pdf->Cell(19, 0.7, 'Pendapatan Dari Penjualan', '', 0, 'L', 0);

            $this->pdf->SetFont("Arial","",9);
            $this->pdf->Ln(0.7);
            $this->pdf->Cell(1, 0.7, '', '', 0, 'L', 0);
            $this->pdf->Cell(15, 0.7, 'Penjualan', '', 0, 'L', 0);
            $this->pdf->Cell(3, 0.7, number_format($data['total']+$data['disctotal']), '', 0, 'R', 0);

            $this->pdf->Ln(0.7);
            $this->pdf->Cell(1, 0.7, '', '', 0, 'L', 0);
            $this->pdf->Cell(12, 0.7, 'Potongan Penjualan', '', 0, 'L', 0);
            $this->pdf->Cell(3, 0.7, number_format($data['disctotal']), '', 0, 'R', 0);

            $this->pdf->Ln(0.7);
            $this->pdf->Cell(1, 0.7, '', '', 0, 'L', 0);
            $this->pdf->Cell(12, 0.7, 'Biaya Pengiriman', '', 0, 'L', 0);
            $this->pdf->Cell(3, 0.7, number_format($data['expcost']), '', 0, 'R', 0);

            $this->pdf->Ln(0.7);
            $this->pdf->Cell(1, 0.7, '', '', 0, 'L', 0);
            $this->pdf->Cell(12, 0.7, 'Biaya COD', '', 0, 'L', 0);
            $this->pdf->Cell(3, 0.7, number_format($data['codcost']), '', 0, 'R', 0);

            $this->pdf->SetFont("Arial","B",9);
            $this->pdf->Ln(0.7);
            // $this->pdf->Cell(1, 0.7, '', '', 0, 'L', 0);
            $this->pdf->Cell(16, 0.7, 'Penjualan Bersih', '', 0, 'L', 0);
            $this->pdf->Cell(3, 0.7, number_format($nett_sale), '', 0, 'R', 0);

            $this->pdf->Ln(0.7);
            // $this->pdf->Cell(1, 0.7, '', '', 0, 'L', 0);
            $this->pdf->Cell(16, 0.7, 'Harga Pokok Penjualan', '', 0, 'L', 0);
            $this->pdf->Cell(3, 0.7, "(".number_format($data['hpptotal']).")", 'B', 0, 'R', 0);

            $this->pdf->Ln(0.7);
            // $this->pdf->Cell(1, 0.7, '', '', 0, 'L', 0);
            $this->pdf->Cell(16, 0.7, 'Laba Kotor', '', 0, 'L', 0);
            $this->pdf->Cell(3, 0.7, number_format($gross_profit), '', 0, 'R', 0);

            $this->pdf->Ln(1);
            $this->pdf->Cell(19, 0.7, 'Beban Penjualan', '', 0, 'L', 0);

            $this->pdf->SetFont("Arial","",9);
            $this->pdf->Ln(0.7);
            $this->pdf->Cell(1, 0.7, '', '', 0, 'L', 0);
            $this->pdf->Cell(12, 0.7, 'Komisi Admin', '', 0, 'L', 0);
            $this->pdf->Cell(3, 0.7, number_format($data['feetotal']), '', 0, 'R', 0);

            $this->pdf->SetFont("Arial","B",9);
            $this->pdf->Ln(0.7);
            // $this->pdf->Cell(1, 0.7, '', '', 0, 'L', 0);
            $this->pdf->Cell(16, 0.7, 'Total Beban Penjualan', '', 0, 'L', 0);
            $this->pdf->Cell(3, 0.7, "(".number_format($data['feetotal']).")", 'B', 0, 'R', 0);

            $this->pdf->Ln(0.7);
            // $this->pdf->Cell(1, 0.7, '', '', 0, 'L', 0);
            $this->pdf->Cell(16, 0.7, 'Laba Kotor (2)', '', 0, 'L', 0);
            $this->pdf->Cell(3, 0.7, number_format($gross_profit - $data['feetotal']), '', 0, 'R', 0);

            // $this->subTotal($this->pdf, $sub_total);

            // $this->pdf->SetFillColor(222,222,222);
            // $this->pdf->SetTextColor(0,0,0);
            // $this->pdf->Cell(16, 0.7, 'GRAND TOTAL', 'TLB', 0, 'C', 1);
            // $this->pdf->Cell(3, 0.7, number_format($grand_total), 'TLBR', 0, 'R', 1);
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

    function tableHeader($me, $uname)
    {
        $me->SetFillColor(255,255,255);
        $me->SetTextColor(0,0,0);
        $me->SetFont('Arial','B', 10);
        $me->Cell(19, 0.7, strtoupper($uname), '', 0, 'L', 1);
        $me->Ln(0.7);

        $me->SetFillColor(0,0,0);
        $me->SetTextColor(255,255,255);
        $me->Cell(1, 1, 'NO' , 'LTBR', 0, 'C', 1);
        $me->Cell(2, 1, 'TANGGAL' , 'LTBR', 0, 'C', 1);
        $me->Cell(5, 1, 'CUSTOMER' , 'LTBR', 0, 'C', 1);
        $me->Cell(5, 1, 'NAMA ITEM' , 'LTBR', 0, 'C', 1);
        $me->Cell(2, 1, 'KOMISI' , 'LTBR', 0, 'C', 1);
        $me->Cell(1, 1, 'QTY' , 'LTBR', 0, 'C', 1);
        $me->Cell(3, 1, 'TOTAL KOMISI' , 'LTBR', 0, 'C', 1);

        $me->Ln(1);

        $me->SetFillColor(255,255,255);
        $me->SetTextColor(0,0,0);
        $me->SetFont('Arial','', 9);
    }

    function subTotal($me, $sub)
    {
        $me->SetFillColor(222,222,222);
        $me->SetTextColor(0,0,0);
        $me->Cell(16, 0.7, 'SUB TOTAL', 'LB', 0, 'C', 1);
        $me->Cell(3, 0.7, number_format($sub), 'LBR', 0, 'R', 1);
        $me->Ln(1);
    }
}
?>
