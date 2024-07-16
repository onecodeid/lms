<?php

// Report Fee / Komisi Per Admin
//

class One_sales_008_excel extends RPT_Controller
{
    var $report_code;
    // var $pdf;

    function __construct()
    {
        parent::__construct();

        $this->load->library("Excel");
        $this->report_code = 'SALES-008';
    }

    function index() {
        // Get data
        $this->load->model('report/r_report');

        $sdate = date('Y-m-d');
        $edate = date('Y-m-d');
        $levelid = 0;
        if (isset($this->sys_input['sdate'])) $sdate = $this->sys_input['sdate'];
        if (isset($this->sys_input['edate'])) $edate = $this->sys_input['edate'];
// echo $sdate; echo $edate;echo 
        $r = $this->r_report->one_sales_008($this->sys_input['level_id'], $sdate, $edate );
        // echo $this->db->last_query();
        // //
        $grand_total = 0;
        $sub_total = 0;
        // print_r($r);
        $filename = "laporan_detail_penjualan_jenjang_{$r[0][0]['M_CustomerLevelName']}_".date('d.m.Y', strtotime($this->input->get('sdate')))."_".date('d.m.Y', strtotime($this->input->get('edate'))).".xls";

        if ($r)
        {
            // EXCEL Starts Here
            /** PHPExcel_IOFactory */
            // require_once dirname(__FILE__) . '/Classes/PHPExcel/IOFactory.php';

            $objReader = PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel = $objReader->load("./assets/template_sales_008.xls");

            $data = [];
            foreach ($r[1] as $k => $v)
            {
                $cl_name = '';
                if ($v['level_code'] == 'CUST.DISTRIBUTOR') $cl_name = 'Dist';
                if ($v['level_code'] == 'CUST.AGENCY') $cl_name = 'Agen';
                if ($v['level_code'] == 'CUST.RESELLER') $cl_name = 'Resl';
                if ($v['level_code'] == 'CUST.ENDUSER') $cl_name = 'User';
                if ($v['level_code'] == 'CUST.MEMBER') $cl_name = 'Member';
                if ($v['level_code'] == 'CUST.OTHER') $cl_name = 'Lain';
                if ($v['level_code'] == 'CUST.FAMILY') $cl_name = 'Family';

                $data[] = [$k+1, 
                        date('Y-m-d', strtotime($v['so_date'])),
                        $v['so_number'],
                        $v['customer_name'] . ' / ' . $cl_name,
                        $v['item_name'],
                        $v['item_price']-$v['item_disc_total'],
                        $v['item_qty'],
                        $v['item_subtotal']];
            }

            $objPHPExcel->getActiveSheet()->insertNewRowBefore(6,sizeof($data)); 
            $baseRow = 'A5';
            $as = $objPHPExcel->getActiveSheet();
            $as->fromArray($data, null, $baseRow);

            $as->setCellValue('A2', 'Periode : ' . date('d/m/Y', strtotime($this->input->get('sdate'))) . ' - ' .  date('d/m/Y', strtotime($this->input->get('edate'))));
            $as->setCellValue('E1', 'JENJANG : ' . $r[0][0]['M_CustomerLevelName']);
            

            //     ->setCellValue('I1', 'Periode ' . date('d/m/Y', strtotime($sdate)) . ' - ' . date('d/m/Y', strtotime($edate)));

            
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save("/home/one/Web/uploads/reports/".$filename);
            // $objWriter->save(str_replace('one-sales/api/application/controllers/report/', 'uploads/reports/', str_replace('.php', '.xls', __FILE__)));
            
            // 
            // echo json_encode(["status"=>"OK", 
            //     "data"=>"http://{$_SERVER['SERVER_NAME']}/pungkook/api/excel/".str_replace('.php', '.xls', pathinfo(__FILE__, PATHINFO_BASENAME))]);

        }   

        $this->sys_ok(["report_url"=>$this->REPORT_EXCEL_URL.$filename]);
    }
}
?>
