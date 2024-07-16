<?php

// Report Fee / Komisi Per Admin
//

class One_sales_003 extends RPT_Controller
{
    var $report_code;

    function __construct()
    {
        parent::__construct();

        $this->load->library("pdf");
        $this->report_code = 'SALES-003';
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
        $r = $this->r_report->one_sales_003( $this->input->get('uid'), $this->input->get('sdate'), $this->input->get('edate'), $this->input->get('type') );
        // print_r($r);
        //
        $grand_total = 0;
        $sub_total = 0;
        $level = 0;

        if ($r)
        {
            $data = $r[0];
            $this->pdf->SetMargins(0.7, 0.5, 0.5);
            $this->pdf->AddPage('P', 'A4');

            $hy = $this->pdf->GetY();
            $this->my_header($this, 
                'Laporan Omzet Per Level', 
                'Periode : ' . date('d/m/Y', strtotime($this->input->get('sdate'))) . ' - ' .  date('d/m/Y', strtotime($this->input->get('edate'))));

            $this->tableHeader($this->pdf);
            $this->pdf->SetFillColor(255,255,255);
            $this->pdf->SetTextColor(0,0,0);
            $this->pdf->SetFont('Arial','', 9);
            foreach ($data as $k => $v)
            {
                $this->pdf->Cell(1, 0.7, $k+1, 'LB', 0, 'C', 1);
                $this->pdf->Cell(5, 0.7, $v['level_name'], 'LB', 0, 'L', 1);
                $this->pdf->Cell(5, 0.7, number_format($v['omzet']), 'LBR', 0, 'R', 1);
                $this->pdf->Ln(0.7);

                $grand_total += $v['omzet'];
            }

            $this->pdf->SetFillColor(222,222,222);
            $this->pdf->SetTextColor(0,0,0);
            $this->pdf->Cell(6, 0.7, 'GRAND TOTAL', 'LB', 0, 'C', 1);
            $this->pdf->Cell(5, 0.7, number_format($grand_total), 'LBR', 0, 'R', 1);

            $this->pdf->Ln(1);

            $data = $r[1];
            
            $n = 0;
            foreach ($data as $k => $v)
            {
                if ($level != $v['level_id'])
                {
                    if ($level != 0)
                    {
                        // SUB TOTAL
                        $this->print_total($this, $sub_total);
                    }

                    $level = $v['level_id'];
                    $n = 0;
                    $sub_total = 0;
                    
                    $this->tableHeader2($this->pdf, $v);
                    $this->pdf->SetFillColor(255,255,255);
                    $this->pdf->SetTextColor(0,0,0);
                    $this->pdf->SetFont('Arial','', 9);
                }

                

                $this->pdf->Cell(1, 0.7, ++$n, 'LB', 0, 'C', 1);
                $this->pdf->Cell(8, 0.7, $v['customer_name'], 'LB', 0, 'L', 1);
                $this->pdf->Cell(5, 0.7, $v['level_name'], 'LB', 0, 'L', 1);
                $this->pdf->Cell(5, 0.7, number_format($v['omzet']), 'LBR', 0, 'R', 1);
                $this->pdf->Ln(0.7);

                $grand_total += $v['omzet'];
                $sub_total += $v['omzet'];
            }
            // SUB TOTAL
            $this->print_total($this, $sub_total);
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

  function print_total($me, $total)
  {
    $me->pdf->SetFillColor(222,222,222);
    $me->pdf->SetTextColor(0,0,0);
    $me->pdf->Cell(14, 0.7, 'SUB TOTAL', 'LB', 0, 'C', 1);
    $me->pdf->Cell(5, 0.7, number_format($total), 'LBR', 0, 'R', 1);
    $me->pdf->Ln(1);

    // $me->pdf->Ln(0.25);
    // $me->pdf->Cell(0.5, 0.7, '', '', 0);
    // $me->pdf->Cell(12.5, 0.7, 'TOTAL', 'LBT', 0, 'C', 1);
    // $me->pdf->Cell(6, 0.7, number_format($total, 0), 'RBTL', 0, 'R', 1);
    // $me->pdf->Ln(0.7);
  }

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
        $me->Cell(5, 1, 'LEVEL CUSTOMER' , 'LTBR', 0, 'C', 1);
        $me->Cell(5, 1, 'TOTAL OMZET' , 'LTBR', 0, 'C', 1);

        $me->Ln(1);
    }

    function tableHeader2($me, $data)
    {
        $me->SetFillColor(255,255,255);
        $me->SetTextColor(0,0,0);
        $this->pdf->SetFont('Arial','B', 10);
        $me->Cell(19, 1,  strtoupper($data['level_name']), '', 0, 'L', 1);
        $me->Ln(0.7);

        $me->SetFillColor(0,0,0);
        $me->SetTextColor(255,255,255);
        $this->pdf->SetFont('Arial','', 10);
        $me->Cell(1, 1, 'NO' , 'LTBR', 0, 'C', 1);
        $me->Cell(8, 1, 'NAMA CUSTOMER' , 'LTBR', 0, 'C', 1);
        $me->Cell(5, 1, 'LEVEL CUSTOMER' , 'LTBR', 0, 'C', 1);
        $me->Cell(5, 1, 'TOTAL OMZET' , 'LTBR', 0, 'C', 1);

        $me->Ln(1);
    }
}
?>
