<?php

class Expedition extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('master/m_expedition');
    }

    function search()
    {
        $search = isset($this->sys_input['search']) ? $this->sys_input['search'] . '%' : '%';
        $page = isset($this->sys_input['page']) ? $this->sys_input['page'] : 1;
        $limit= isset($this->sys_input['limit']) ? $this->sys_input['limit'] : 10;
        
        if (isset($this->sys_input['cod']))
            $r = $this->m_expedition->search(['expedition_name'=>$search, 'page'=>$page, 'limit'=>$limit, 'cod'=>$this->sys_input['cod']]);
        else
            $r = $this->m_expedition->search(['expedition_name'=>$search, 'page'=>$page, 'limit'=>$limit]);
        $this->sys_ok($r);
    }

    function search_service()
    {
        $this->load->model('system/s_system');
        $d = $this->s_system->get_default();

        // Get Company City ID
        $co = $this->db->query("SELECT M_DistrictROID FROM m_district WHERE M_DistrictID = ?", [$d->S_SystemCompanyM_DistrictID])
                        ->row();

        // Get Company City ID
        $to = $this->db->query("SELECT M_DistrictROID FROM m_district WHERE M_DistrictID = ?", [$this->sys_input['to']])
                        ->row();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->RO_URL."cost",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "origin={$co->M_DistrictROID}&destination={$to->M_DistrictROID}&weight={$this->sys_input['weight']}&courier={$this->sys_input['courier']}&originType=subdistrict&destinationType=subdistrict",
            CURLOPT_HTTPHEADER => array(
            "content-type: application/x-www-form-urlencoded",
            "key: ".$this->RO_KEY
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $this->sys_ok($response);
        }
    }

    function search_service_kecamatan()
    {
        $this->load->model('system/s_system');
        $d = $this->s_system->get_default();

        // Get Company City ID
        $co = $this->db->query("SELECT M_DistrictROID FROM m_district WHERE M_DistrictID = ?", [$d->S_SystemCompanyM_DistrictID])
                        ->row();

        // Get Company City ID
        $to = $this->db->query("SELECT M_DistrictROID FROM m_district WHERE M_DistrictID = ?", [$this->sys_input['to']])
                        ->row();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->RO_URL."cost",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "origin={$co->M_DistrictROID}&destination={$to->M_DistrictROID}&weight={$this->sys_input['weight']}&courier={$this->sys_input['courier']}&originType=subdistrict&destinationType=subdistrict",
            CURLOPT_HTTPHEADER => array(
            "content-type: application/x-www-form-urlencoded",
            "key: ".$this->RO_KEY
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $this->sys_ok($response);
        }
    }

    function waybill()
    {
        $r = $this->db->select('M_ExpeditionROCode, W_CourierReceiptNo', false)
                ->join('l_so', 'W_CourierL_SoID = L_SoID')
                ->join('m_expedition', 'L_SoM_ExpeditionID = M_ExpeditionID')
                ->where('W_CourierID', $this->sys_input['id'])
                ->get('w_courier')
                ->row();
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->RO_URL."waybill",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "waybill={$r->W_CourierReceiptNo}&courier={$r->M_ExpeditionROCode}",
            CURLOPT_HTTPHEADER => array(
            "content-type: application/x-www-form-urlencoded",
            "key: ".$this->RO_KEY
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $this->sys_ok(json_decode($response));
        }

    }
    
    function save()
    {
        $this->sys_input['user_id'] = $this->sys_user['user_id'];
        if (isset($this->sys_input['expedition_id']))
            $r = $this->m_expedition->save( $this->sys_input, $this->sys_input['expedition_id'] );
        else
            $r = $this->m_expedition->save( $this->sys_input );
        
        if ($r->status == "OK")
            $this->sys_ok($r->data);
        else
            $this->sys_error('ERROR');
    }

    function del()
    {
        $r = $this->m_expedition->del( $this->sys_input );
        $this->sys_ok($r);
    }
}

?>