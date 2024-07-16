<?php

class R_report extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->table_name = "r_report";
        $this->table_key = "R_ReportID";
    }

    function search_groups()
    {
        $r = $this->db->query("SELECT a.*, CONCAT('[', GROUP_CONCAT(JSON_OBJECT('report_id', R_ReportID, 'report_name', R_ReportName, 'report_code', R_ReportCode, 'report_desc', R_ReportDesc, 'report_url', R_ReportUrl) SEPARATOR ','), ']') childs FROM
                            (SELECT a.R_ReportID report_id, a.R_ReportName report_name, 
                            a.R_ReportLeft report_left, a.R_ReportRight report_right, a.R_ReportIcon report_icon, a.R_ReportCode report_code,
                            a.R_ReportDesc report_desc
                            FROM r_report a
                            LEFT JOIN r_report b ON b.R_ReportLeft < a.R_ReportLeft AND b.R_ReportRight > a.R_ReportRight AND b.R_ReportIsActive = 'Y'
                            WHERE a.R_ReportIsActive = 'Y'
                            GROUP BY a.R_ReportID
                            HAVING COUNT(b.R_ReportID) < 1
                            ORDER BY a.R_ReportLeft) a
                            JOIN r_report c ON report_left < R_ReportLeft AND report_right > R_ReportRight AND R_ReportIsActive = 'Y'
                            GROUP BY report_id
                            ");
        if ($r)
        {               
            $r = $r->result_array();
            foreach ($r as $k => $v)
                $r[$k]['childs'] = json_decode($v['childs']);
            return ['records'=>$r];
        }

        return ['records'=>[]];
    }

    // Report Detail Penjualan Per Admin
    function One_sales_002($uid, $sdate, $edate)
    {
        $r = $this->GetMultipleQueryResult("CALL `sp_r_sales_002`('{$sdate}', '{$edate}', '{$uid}')", 2);
        return $r;
    }

    // Report Omzet Per Level
    function One_sales_003($uid, $sdate, $edate, $type = 'A')
    {
        $r = $this->GetMultipleQueryResult("CALL `sp_r_sales_003`('{$uid}', '{$sdate}', '{$edate}', '{$type}')", 2);
        return $r;
    }

    // Report Omzet Per Jenjang
    function One_sales_004($sdate, $edate, $level)
    {
        $r = $this->GetMultipleQueryResult("CALL `sp_r_sales_004`('{$sdate}', '{$edate}', '{$level}')", 2);
        return $r;
    }

    // Report Omzet Per Produk
    function One_sales_005($sdate, $edate, $type = "A")
    {
        $r = $this->GetMultipleQueryResult("CALL `sp_r_sales_005`('{$sdate}', '{$edate}', '{$type}')", 1);
        return $r;
    }

    // Report Omzet Per Produk
    // Per Kategori
    function One_sales_006($sdate, $edate)
    {
        $r = $this->GetMultipleQueryResult("CALL `sp_r_sales_006`('{$sdate}', '{$edate}')", 2);
        if (!isset($r[0]))
            $r[0] = [];

        $ctgrs = [];
        foreach ($r[0] as $k => $v)
            if (array_search($v['category_id'], $ctgrs) === false)
                $ctgrs[] = $v['category_id'];
        
        $categories = [];
        foreach ($r[1] as $k => $v)
            if (array_search($v['category_id'], $ctgrs) !== false)
                $categories[] = $v;
        $r[1] = $categories;

        return $r;
    }

    // Report Omzet last 3 month vs target
    // Per Customer
    function One_sales_007($date, $level)
    {
        $r = $this->GetMultipleQueryResult("CALL `sp_r_sales_007`('{$date}', '{$level}')", 2);
        if (!isset($r[1]))
            $r[1] = [];

        return $r;
    }

    // Report Detail Penjualan Per Jenjang
    function One_sales_008($level_id, $sdate, $edate)
    {
        $r = $this->GetMultipleQueryResult("CALL `sp_r_sales_008`('{$sdate}', '{$edate}', '{$level_id}')", 2);
        return $r;
    }

    // Report Detail Penjualan
    function One_sales_009($sdate, $edate)
    {
        $r = $this->GetMultipleQueryResult("CALL `sp_r_sales_009`('{$sdate}', '{$edate}')", 2);
        return $r;
    }

    // Report Detail Penjualan Per Admin Per Jenjang
    function One_sales_010($uid, $sdate, $edate, $levelid)
    {
        $r = $this->GetMultipleQueryResult("CALL `sp_r_sales_010`('{$sdate}', '{$edate}', '{$uid}', '{$levelid}')", 2);
        return $r;
    }

    // Report Omzet last 12 month
    function One_sales_011($uid = 0)
    {
        $r = $this->db->query("CALL sp_r_sales_011()")
                    ->result_array();
        $this->clean_mysqli_connection($this->db->conn_id);

        return $r;
    }
    
    // Report Invoice Penjualan
    function One_iv_001($id)
    {
        $r = $this->GetMultipleQueryResult("CALL `sp_r_iv_001`('{$id}')", 2);
        return $r;
    }

    // Report Fee / Komisi Per Admin
    function One_fin_001($uid, $sdate, $edate)
    {
        $r = $this->GetMultipleQueryResult("CALL `sp_r_fin_001`('{$uid}', '{$sdate}', '{$edate}')", 2);
        return $r;
    }

    // Report Fee / Komisi Semua Admin
    function One_fin_002($sdate, $edate)
    {
        $r = $this->db->query("CALL `sp_r_fin_002`(?,?)", [$sdate, $edate])
                    ->result_array();
        $this->clean_mysqli_connection($this->db->conn_id);
        return $r;
    }

    // Report COD Sukses / Penerimaan COD
    function One_fin_003($sdate, $edate, $exp_id = 0)
    {
        $r = $this->GetMultipleQueryResult("CALL `sp_r_fin_003`('{$sdate}', '{$edate}', '{$exp_id}')", 1);
        return $r;
    }

    // Report COD Gagal / Pengeluaran COD
    function One_fin_004($sdate, $edate, $exp_id = 0)
    {
        $r = $this->GetMultipleQueryResult("CALL `sp_r_fin_004`('{$sdate}', '{$edate}', '{$exp_id}')", 1);
        return $r;
    }

    // Report Laba Kotor
    function One_fin_005($sdate, $edate)
    {
        $r = $this->GetMultipleQueryResult("CALL `sp_r_fin_005`('{$sdate}', '{$edate}')", 1);
        return $r;
    }

    // Report Laporan Customer
    function One_master_001($uid, $provinceid = 0, $cityid = 0, $levelid = 0)
    {
        $r = $this->GetMultipleQueryResult("CALL `sp_r_master_001`('{$uid}', '{$provinceid}', '{$cityid}', '{$levelid}')", 2);
        return $r;
    }

    // Surat Jalan
    function One_wh_001($id)
    {
        $r = $this->GetMultipleQueryResult("CALL `sp_r_wh_001`('{$id}')", 2);
        return $r;
    }

    // Logbook Pengiriman
    function One_wh_002($sdate, $edate, $exp_id = 0)
    {
        $sdate = date('Y-m-d', strtotime($sdate));
        $edate = date('Y-m-d', strtotime($edate));
        $r = $this->GetMultipleQueryResult("CALL `sp_r_wh_002`('{$sdate}', '{$edate}', '{$exp_id}')", 1);
        return $r;
    }

    // Rekap Logbook Pengiriman
    function One_wh_003($sdate, $edate, $exp_id = 0)
    {
        $sdate = date('Y-m-d', strtotime($sdate));
        $edate = date('Y-m-d', strtotime($edate));
        $r = $this->GetMultipleQueryResult("CALL `sp_r_wh_003`('{$sdate}', '{$edate}', '{$exp_id}')", 1);
        return $r;
    }

    // Report Stock Card
    function One_wh_005($id, $sdate, $edate)
    {
        $r = $this->GetMultipleQueryResult("CALL `sp_r_wh_005`('{$id}', '{$sdate}', '{$edate}')", 2);
        return $r;
    }

    // Report Opname
    function One_wh_006($id)
    {
        $r = $this->GetMultipleQueryResult("CALL `sp_r_wh_006`('{$id}')", 2);
        return $r;
    }
}

?>