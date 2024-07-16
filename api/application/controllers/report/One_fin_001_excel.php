<?php

// Report Fee / Komisi Per Admin
//

class One_fin_001_excel extends RPT_Controller
{
    var $report_code;
    // var $pdf;

    function __construct()
    {
        parent::__construct();

        $this->load->library("Excel");
        $this->report_code = 'FIN-001';
    }

    function index() {
        // Get data
        $this->load->model('report/r_report');
        
        if (!isset($this->sys_input['uid']))
            $uid = $this->sys_user['user_id'];
        else
            $uid = $this->sys_input['uid'];

        $sdate = date('Y-m-d');
        $edate = date('Y-m-d');
        $levelid = 0;
        if (isset($this->sys_input['sdate'])) $sdate = $this->sys_input['sdate'];
        if (isset($this->sys_input['edate'])) $edate = $this->sys_input['edate'];

        $r = $this->r_report->one_fin_001( $uid, $sdate, $edate );
        //
        $grand_total = 0;
        $sub_total = 0;
        
        $filename = "laporan_komisi_{$r[0][0]['S_UserUsername']}_".date('d.m.Y', strtotime($this->input->get('sdate')))."_".date('d.m.Y', strtotime($this->input->get('edate'))).".xls";

        
// print_r($r);
        if ($r)
        {
            // EXCEL Starts Here
            /** PHPExcel_IOFactory */
            // require_once dirname(__FILE__) . '/Classes/PHPExcel/IOFactory.php';

            $objReader = PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel = $objReader->load("./assets/template_fin_001.xls");

            $data = [];
            foreach ($r[1] as $k => $v)
            {
                $cl_name = '';
                if ($v['M_CustomerLevelCode'] == 'CUST.DISTRIBUTOR') $cl_name = 'Dist';
                if ($v['M_CustomerLevelCode'] == 'CUST.AGENCY') $cl_name = 'Agen';
                if ($v['M_CustomerLevelCode'] == 'CUST.RESELLER') $cl_name = 'Resl';
                if ($v['M_CustomerLevelCode'] == 'CUST.ENDUSER') $cl_name = 'User';

                $data[] = [$k+1, 
                        date('Y-m-d', strtotime($v['F_FeeL_SoDate'])),
                        $v['L_SoNumber'],
                        $v['M_CustomerName'] . ' / ' . $cl_name,
                        $v['M_ItemName'] != null ? $v['M_ItemName'] : $v['M_PacketName'],
                        $v['F_FeeAmount'],
                        $v['F_FeeQty'],
                        $v['F_FeeTotal']];
            }

            $objPHPExcel->getActiveSheet()->insertNewRowBefore(6,sizeof($data)); 
            $baseRow = 'A5';
            $as = $objPHPExcel->getActiveSheet();
            $as->fromArray($data, null, $baseRow);

            $as->setCellValue('D1', 'Admin : '.$r[0][0]['S_UserUsername']);
            $as->setCellValue('A2', 'Periode : ' . date('d/m/Y', strtotime($this->input->get('sdate'))) . ' - ' .  date('d/m/Y', strtotime($this->input->get('edate'))));
            

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
