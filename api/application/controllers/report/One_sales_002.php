<?php

// Report Fee / Komisi Per Admin
//

class One_sales_002 extends RPT_Controller
{
    var $report_code;

    function __construct()
    {
        parent::__construct();

        $this->load->library("pdf");
        $this->report_code = 'SALES-002';
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
        $r = $this->r_report->one_sales_002( $this->input->get('uid'), $this->input->get('sdate'), $this->input->get('edate') );

        if (isset($this->sys_input['type']))
        {
            if ($this->sys_input['type'] == 'csv')
            {
                $data = [];
                foreach ($r[1] as $k => $v)
                {
                    $data[] = ['TANGGAL_SO'=>$v['so_date'],
                        'NOMOR_SO'=>$v['so_number'],
                        'SUMBER LEAD'=>$v['lead_name'],
                        'NAMA_CUSTOMER'=>$v['customer_name'],
                        'NAMA_ITEM'=>$v['item_name'],
                        'HARGA_ITEM'=>$v['item_price'],
                        'QTY_ITEM'=>$v['item_qty'],
                        'SUBTOTAL'=>$v['item_subtotal'],
                        'KUPON'=>$v['coupon_amount'],
                        'KODE KUPON'=>$v['coupon_code'],
                        'TOTAL'=>$v['item_subtotal'] - $v['coupon_amount']];
                }
                $this->csv($data, 'detail_penjualan_per_admin_' .
                    (isset($r[0])?$r[0][0]['S_UserUsername']:'' ). '_' .
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
                'Laporan Detail Penjualan Per Admin', 
                'Admin : '.$r['S_UserUsername'].' | Periode : ' . date('d/m/Y', strtotime($this->input->get('sdate'))) . ' - ' .  date('d/m/Y', strtotime($this->input->get('edate'))), 'L');

            
            $this->tableHeader($this->pdf);

            $this->pdf->SetFillColor(255,255,255);
            $this->pdf->SetTextColor(0,0,0);
            $this->pdf->SetFont('Arial','', 9);
            foreach ($data as $k => $v)
            {
                $cl_name = '';
                if ($v['level_code'] == 'CUST.DISTRIBUTOR') $cl_name = 'Dist';
                if ($v['level_code'] == 'CUST.AGENCY') $cl_name = 'Agen';
                if ($v['level_code'] == 'CUST.RESELLER') $cl_name = 'Resl';
                if ($v['level_code'] == 'CUST.ENDUSER') $cl_name = 'User';
                if ($v['level_code'] == 'CUST.MEMBER') $cl_name = 'Member';
                if ($v['level_code'] == 'CUST.OTHER') $cl_name = 'Lain';
                if ($v['level_code'] == 'CUST.FAMILY') $cl_name = 'Family';
//                 UST.FAMILY	Keluarga	Y	N	0	0	0	NULL	Y	2020-03-03 22:43:11	2020-03-04 07:07:12
//  edit	3	CUST.AGENCY	Agen	Y	Y	3000000	6	18000000	Target kumulatif selama 6 bulan	Y	2020-03-03 22:43:21	2021-05-18 10:39:49
//  edit	4	CUST.RESELLER	Reseller	Y	Y	500000	12	6000000	Target kumulatif selama 12 bulan	Y	2020-03-03 22:43:34	2021-05-18 10:39:49
//  edit	5	CUST.ENDUSER	End User	N	Y	0	0	0	NULL	Y	2020-03-03 22:43:34	2020-03-04 07:07:12
//  edit	6	CUST.MEMBER	Member	N	Y	0	0	0	NULL	Y	2020-03-03 22:43:34	2020-03-04 07:07:12
//  edit	7	CUST.OTHER

                $this->pdf->Cell(1, 0.7, $k+1, 'LB', 0, 'C', 1);
                $this->pdf->Cell(2, 0.7, date('d-m-Y', strtotime($v['so_date'])), 'LB', 0, 'C', 1);
                $this->pdf->Cell(3, 0.7, $v['so_number'], 'LB', 0, 'L', 1);
                $this->pdf->Cell(6, 0.7, $v['customer_name'] . ' / ' . $cl_name, 'LB', 0, 'L', 1);
                $this->pdf->Cell(10, 0.7, $v['item_name'], 'LB', 0, 'L', 1);
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
        $me->Cell(6, 1, 'CUSTOMER' , 'LTBR', 0, 'C', 1);
        $me->Cell(10, 1, 'NAMA ITEM' , 'LTBR', 0, 'C', 1);
        $me->Cell(2, 1, 'HARGA' , 'LTBR', 0, 'C', 1);
        $me->Cell(1, 1, 'QTY' , 'LTBR', 0, 'C', 1);
        $me->Cell(3, 1, 'SUBTOTAL' , 'LTBR', 0, 'C', 1);

        $me->Ln(1);
    }
}
?>
