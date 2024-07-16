<?php

// Report Fee / Komisi Per Admin
//

class One_fin_001 extends RPT_Controller
{
    var $report_code;

    function __construct()
    {
        parent::__construct();

        $this->load->library("pdf");
        $this->report_code = 'FIN-001';
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
        $r = $this->r_report->one_fin_001( $this->input->get('uid'), $this->input->get('sdate'), $this->input->get('edate') );

        if (isset($this->sys_input['out']))
        {
            if ($this->sys_input['out'] == 'csv')
            {
                $data = [];
                foreach ($r[1] as $k => $v)
                {
                    $cl_name = $this->levelName($v['M_CustomerLevelCode']);
                    $data[] = ['TANGGAL'=>date('d-m-Y', strtotime($v['F_FeeL_SoDate'])),
                        'NAMA_CUSTOMER'=>$v['M_CustomerName'],
                        'LEVEL_CUSTOMER'=>$cl_name,
                        'NAMA_ITEM_PACKET'=>($v['M_ItemName'] == null ? $v['M_PacketName'] : $v['M_ItemName']),
                        'KOMISI'=>$v['F_FeeAmount'],
                        'QTY'=>$v['F_FeeQty'],
                        'TOTAL'=>$v['F_FeeTotal']];
                }

                $this->csv($data, 'komisi_per_admin_' .
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
            $data = isset($r[1])?$r[1]:[];
            $r = $r[0][0];
            $this->pdf->SetMargins(0.7, 0.5, 0.5);
            $this->pdf->AddPage('P', 'A4');

            $hy = $this->pdf->GetY();
            // $this->pdf->Image(base_url() . '/assets/images/logo-1-1.png', 0.8, 0.7, 3);
            $this->my_header($this, 
                'Laporan Komisi Per Admin', 
                'Admin : '.$r['S_UserUsername'].' | Periode : ' . date('d/m/Y', strtotime($this->input->get('sdate'))) . ' - ' .  date('d/m/Y', strtotime($this->input->get('edate'))));

            
            $this->tableHeader($this->pdf);

            $this->pdf->SetFillColor(255,255,255);
            $this->pdf->SetTextColor(0,0,0);
            $this->pdf->SetFont('Arial','', 9);
            foreach ($data as $k => $v)
            {
                $cl_name = $this->levelName($v['M_CustomerLevelCode']);

                $this->pdf->Cell(1, 0.7, $k+1, 'LB', 0, 'C', 1);
                $this->pdf->Cell(2, 0.7, date('d-m-Y', strtotime($v['F_FeeL_SoDate'])), 'LB', 0, 'C', 1);
                $this->pdf->Cell(5, 0.7, $v['M_CustomerName'] . ' / ' . $cl_name, 'LB', 0, 'L', 1);
                $this->pdf->Cell(5, 0.7, $v['M_ItemName'] == null ? $v['M_PacketName'] : $v['M_ItemName'], 'LB', 0, 'L', 1);
                $this->pdf->Cell(2, 0.7, number_format($v['F_FeeAmount']), 'LB', 0, 'R', 1);
                $this->pdf->Cell(1, 0.7, number_format($v['F_FeeQty']), 'LB', 0, 'R', 1);
                $this->pdf->Cell(3, 0.7, number_format($v['F_FeeTotal']), 'LBR', 0, 'R', 1);
                $this->pdf->Ln(0.7);

                $grand_total += $v['F_FeeTotal'];
            }

            $this->pdf->SetFillColor(222,222,222);
            $this->pdf->SetTextColor(0,0,0);
            $this->pdf->Cell(16, 0.7, 'GRAND TOTAL', 'LB', 0, 'C', 1);
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
        $me->Cell(5, 1, 'CUSTOMER' , 'LTBR', 0, 'C', 1);
        $me->Cell(5, 1, 'NAMA ITEM' , 'LTBR', 0, 'C', 1);
        $me->Cell(2, 1, 'KOMISI' , 'LTBR', 0, 'C', 1);
        $me->Cell(1, 1, 'QTY' , 'LTBR', 0, 'C', 1);
        $me->Cell(3, 1, 'TOTAL KOMISI' , 'LTBR', 0, 'C', 1);

        $me->Ln(1);
    }
}
?>
