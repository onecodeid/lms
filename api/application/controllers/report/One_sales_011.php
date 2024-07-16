<?php

// Report Fee / Komisi Per Admin
//

class One_sales_011 extends RPT_Controller
{
    var $report_code;

    function __construct()
    {
        parent::__construct("P", "cm", array(21,29.7));

        $this->load->library("pdf");
        $this->report_code = 'SALES-011';
    }

    function index() {
        $this->pdf = new PDF("P", "mm", array(21,29.7));
        $this->pdf->SetAutoPageBreak(true,1);

        $this->pdf->rptclass = $this;
        $this->pdf->rptTitle = '-';
        // $this->pdf->header_func = "my_header";
        $this->pdf->footer_func = "my_footer";

        $this->pdf->SetFont('Arial','', 11);

        // Get data
        $this->load->model('report/r_report');
        $r = $this->r_report->one_sales_011();

        if (isset($this->sys_input['type']))
        {
            if ($this->sys_input['type'] == 'csv')
            {
                $data = [];
                foreach ($r[1] as $k => $v)
                {
                    $data[] = [
                        'NAMA_CUSTOMER'=>$v['customer_name'],
                        'KOTA_CUSTOMER'=>$v['city_name'],
                        'JUMLAH_TRX'=>$v['total_trx'],
                        'JUMLAH_ITEM'=>$v['total_qty'],
                        'JUMLAH_OMZET'=>$v['total']];
                }
                $this->csv($data, 'omzet_penjualan_per_jenjang_'.
                    date('d-m-Y', strtotime($this->input->get('sdate'))) . '_' .  
                    date('d-m-Y', strtotime($this->input->get('edate'))));
                return;
            }
        }

        // print_r($r);
        //
        $grand_total = 0;
        $grand_total_qty = 0;
        $grand_total_trx = 0;
        $sub_total = 0;

        if ($r)
        {
            $data = $r;
            $data_diagram = [];
            $this->pdf->SetMargins(7, 5, 5);
            $this->pdf->AddPage('P', 'A4');
            $this->pdf->SetMargins(7, 5, 5);

            $hy = $this->pdf->GetY();
            // $this->pdf->Image(base_url() . '/assets/images/logo-1-1.png', 0.8, 0.7, 3);
            $this->my_header($this, 
                'Laporan Omzet 12 Bulan Terakhir', 
                '', 'mm' );

            
            $this->tableHeader($this->pdf);

            $this->pdf->SetFillColor(255,255,255);
            $this->pdf->SetTextColor(0,0,0);
            $this->pdf->SetFont('Arial','', 9);
            foreach ($data as $k => $v)
            {
                $this->pdf->Cell(10, 7, $k+1, 'LB', 0, 'C', 1);
                // $this->pdf->Cell(2, 0.7, date('d-m-Y', strtotime($v['so_date'])), 'LB', 0, 'C', 1);
                $this->pdf->Cell(90, 7, date("M Y", strtotime($v['omzet_date'])), 'LB', 0, 'L', 1);
                // $this->pdf->Cell(5, 0.7, $v['total'], 'LB', 0, 'L', 1);
                $this->pdf->Cell(30, 7, number_format($v['total_trx']), 'LB', 0, 'R', 1);
                $this->pdf->Cell(30, 7, number_format($v['total_qty']), 'LB', 0, 'R', 1);
                $this->pdf->Cell(30, 7, number_format($v['total']), 'LBR', 0, 'R', 1);
                $this->pdf->Ln(7);

                $grand_total += $v['total'];
                $grand_total_qty += $v['total_qty'];
                $grand_total_trx += $v['total_trx'];

                $data_diagram[ date("M Y", strtotime($v['omzet_date'])) ] = $v['total'];
            }

            $this->pdf->SetFillColor(222,222,222);
            $this->pdf->SetTextColor(0,0,0);
            $this->pdf->Cell(100, 7, 'GRAND TOTAL', 'LB', 0, 'C', 1);
            $this->pdf->Cell(30, 7, number_format($grand_total_trx), 'LBR', 0, 'R', 1);
            $this->pdf->Cell(30, 7, number_format($grand_total_qty), 'LBR', 0, 'R', 1);
            $this->pdf->Cell(30, 7, number_format($grand_total), 'LBR', 0, 'R', 1);
        }

        $this->pdf->Ln(20);

        $data = array('Men' => 1510, 'Women' => 1610, 'Children' => 1400);



        //Bar diagram
        $this->pdf->SetFont('Arial', 'BIU', 12);
        $valX = $this->pdf->GetX();
        $valY = $this->pdf->GetY();
        $this->pdf->BarDiagram(190, 70, $data_diagram, '%l ', array(255,175,100));
        $this->pdf->SetXY($valX, $valY + 80);

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
        $me->Cell(10, 10, 'NO' , 'LTBR', 0, 'C', 1);
        // $me->Cell(2, 1, 'TANGGAL' , 'LTBR', 0, 'C', 1);
        $me->Cell(90, 10, 'PERIODE' , 'LTBR', 0, 'C', 1);
        // $me->Cell(5, 1, 'NAMA ITEM' , 'LTBR', 0, 'C', 1);
        $me->Cell(30, 10, 'JML TRANSAKSI' , 'LTBR', 0, 'C', 1);
        $me->Cell(30, 10, 'QTY ITEM' , 'LTBR', 0, 'C', 1);
        $me->Cell(30, 10, 'OMZET' , 'LTBR', 0, 'C', 1);

        $me->Ln(10);
    }
}
?>
