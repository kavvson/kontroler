<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Place extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        //if (!$this->is_admin) {
        //   show_error("Niewystarczające uprawnienia do podglądu", 404, 'Brak uprawnień');
        // }
    }


    function base64_url_decode($input)
    {
        return base64_decode(strtr($input, '._-', '+/='));
    }

    public function Akceptacja_Premii($id_prac, $nakogo, $premia_id)
    {
        if (!$this->is_admin) {
            show_error("Niewystarczające uprawnienia do podglądu", 404, 'Brak uprawnień');
        }

        $data = array(
            'title' => 'Akceptacja premii',
        );

        $this->load->model("Premie_model", "p");

        $get = $this->p->test($this->base64_url_decode($id_prac), $this->base64_url_decode($nakogo), $this->base64_url_decode($premia_id));

        if (!empty($get->id_premii)) {
            $data["return"] = $this->p->updatePremia("accept", $this->base64_url_decode($premia_id));
        } else {
            $data["return"] = "Upewnij się, że link jest poprawny";
        }

        $this->load->view('partial/header', $data);
        $this->load->view('place/akceptacja_odmowa', $data);
        $this->load->view('partial/footer');
    }

    public function Odmowa_Premii($id_prac, $nakogo, $premia_id)
    {
        if (!$this->is_admin) {
            show_error("Niewystarczające uprawnienia do podglądu", 404, 'Brak uprawnień');
        }

        $this->load->model("Premie_model", "p");

        $get = $this->p->test($this->base64_url_decode($id_prac), $this->base64_url_decode($nakogo), $this->base64_url_decode($premia_id));
        $data = array(
            'title' => 'Odmowa premii',
        );
        if (!empty($get->id_premii)) {
            $data["return"] = $this->p->updatePremia("decline", $this->base64_url_decode($premia_id));
        } else {
            $data["return"] = "Upewnij się, że link jest poprawny";
        }


        $this->load->view('partial/header', $data);
        $this->load->view('place/akceptacja_odmowa', $data);
        $this->load->view('partial/footer');
    }

    public function Premie($id)
    {
        $this->load->model("Premie_model", "p");
        $this->p->Dodaj_Premie($id);
    }

    public function Lista_Premii()
    {
        if (!$this->is_admin) {
            show_error("Niewystarczające uprawnienia do podglądu", 404, 'Brak uprawnień');
        }
        $data['title'] = "Premie";
        $this->load->view('partial/header', $data);
        $this->load->view('place/premie', $data);
        $this->load->view('partial/footer');
    }

    public function premie_list()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        $this->load->model('Premie_model', 'customers');
        $list = $this->customers->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $customers) {
            $no++;
            $row = array();
            $row["id_premii"] = $customers->id_premii;
            $row["zlorzyl"] = $customers->first_name . " " . $customers->last_name;
            $row["na_rzecz"] = $customers->imie . " " . $customers->nazwisko;
            $row["kwota"] = $customers->kwota;
            $row["opis"] = $customers->opis;
            $row["dodano"] = $customers->dodano;
            $row["status"] = $customers->status;
            $data[] = $row;
        }
        $call = $this->customers->count_filtered();


        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->customers->count_all(),
            "recordsFiltered" => $call['count'],
            "agregacja" => $call['agregacja'],
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function Delegacje()
    {
        $this->load->model("Place_model");
        $this->Place_model->obliczDelegacje();
    }

    public function ListaUmow()
    {
        $data['title'] = "Umowy";
        $this->load->view('partial/header', $data);
        $this->load->view('place/umowy', $data);
        $this->load->view('partial/footer');
    }

    public function Lista()
    {
        $data['title'] = "Płace";
        $this->load->view('partial/header', $data);
        $this->load->view('place/place', $data);
        $this->load->view('partial/footer');
    }

    public function index()
    {
        $this->output->enable_profiler(true);
        $this->load->model("Kontrahent_model", "k");

        $data = array(
            'title' => 'Importowanie płac',
            'pages_now' => 'Pojazd podgląd',
            'pages_now_ex' => 'forms',
            'sublevel' => '',
            // descriptions
            'pageName' => 'Kontrahent',
            'navMaster' => 'Dodaj',
            'navSecond' => '',
        );


        $this->load->view('partial/header', $data);
        $this->load->view('pensje/wgraj', $data);
        $this->load->view('partial/footer');
    }

    public function ImportujUmowy()
    {
        //if (!$this->is_admin) {
        //    show_error("Niewystarczające uprawnienia do importu umów", 404, 'Brak uprawnień');
        // }
        $this->output->enable_profiler(true);


        $data = array(
            'title' => 'Importowanie umów',
            'pages_now' => 'Pojazd podgląd',
            'pages_now_ex' => 'forms',
            'sublevel' => '',
            // descriptions
            'pageName' => 'Kontrahent',
            'navMaster' => 'Dodaj',
            'navSecond' => '',
        );


        $this->load->view('partial/header', $data);
        $this->load->view('pensje/wgraj_umowy', $data);
        $this->load->view('partial/footer');
    }

    public function ImportujKarty()
    {
        //if (!$this->is_admin) {
        //    show_error("Niewystarczające uprawnienia do importu kart", 404, 'Brak uprawnień');
        // }
        $this->output->enable_profiler(true);


        $data = array(
            'title' => 'Importowanie kart',
            'pages_now' => 'Pojazd podgląd',
            'pages_now_ex' => 'forms',
            'sublevel' => '',
            // descriptions
            'pageName' => 'Kontrahent',
            'navMaster' => 'Dodaj',
            'navSecond' => '',
        );


        $this->load->view('partial/header', $data);
        $this->load->view('pensje/wgraj_karte', $data);
        $this->load->view('partial/footer');
    }

    public function ImportujKartyExcel()
    {
        // if (!$this->is_admin) {
        //      show_error("Niewystarczające uprawnienia do importu umów", 404, 'Brak uprawnień');
        // }
        $reponse = array(
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash()
        );
        $status = 0;

        $this->load->model("Karty_model", "gm");
        try {
            $file = $this->gm->Dodaj();


            if (strpos($file['msg'], 'files') !== false) {
                $data = $this->karta($file['msg'], $file['org']);//$file
                $status = 1;
            }

            if (empty($data["ex"])) {
                $message = "Zaimportowano";
            } else {
                $message = $data["ex"];
                $data['s'] = array();
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
            $data['s'] = null;
        }
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode(array("regen" => $reponse, "response" => array("status" => $status, "message" => $message, "pola" => $data['s']))));
    }

    public function ajax_list_karty()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        $this->load->model('Karty_model', 'customers');

        $list = $this->customers->get_datatables();
        $data = array();
        $no = $this->input->post('start');

        foreach ($list as $k => $customers) {
            $no++;
            $row = array();
            $row["id_transkacji"] = $customers->id_transakcji;
            $row["fk_pracownik"] = $customers->imie . " " . $customers->nazwisko;
            $row["id_pracownika"] = $customers->id_pracownika;
            $row["data_operacji"] = $customers->data_operacji;
            $row["typ_transakcji"] = $customers->typ_transakcji;
            $row["kwota"] = $customers->kwota;
            $row["rozliczona_kwota"] = $customers->rozliczona_kwota;
            $row["dokument"] = $customers->ExpenseNumber;
            $row["extra"] = $customers->ExpenseID;
            $row["data_waluty"] = $customers->data_waluty;
            $data[] = $row;
        }

        $call = $this->customers->count_filtered();


        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->customers->count_all(),
            "recordsFiltered" => $call['count'],
            "agregacja" => $call['agregacja'],
            "statystyka" =>$call['statystyka'],
            "data" => $data,

        );
        //output to json format
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($output));

    }

    public function ImportujUmowyExcel()
    {
        //  if (!$this->is_admin) {
        //     show_error("Niewystarczające uprawnienia do importu umów", 404, 'Brak uprawnień');
        //  }
        $this->load->model("Umowy_model", "gm");

        $file = $this->gm->Dodaj();

        $reponse = array(
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash()
        );
        $status = 0;
        if (strpos($file, 'files') !== false) {
            $data = $this->umowy_inne($file);//$file
            $status = 1;
        }

        if (empty($data["ex"])) {
            $message = "Zaimportowano";
        } else {
            $message = $data["ex"];
        }
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode(array("regen" => $reponse, "response" => array("status" => $status, "message" => $message, "pola" => $data['s']))));
    }

    public function Importuj()
    {
        // if (!$this->is_admin) {
        //    show_error("Niewystarczające uprawnienia do importu płac", 404, 'Brak uprawnień');
        //}
        $this->load->model("Gratyfikant_model", "gm");

        $file = $this->gm->Dodaj();

        $reponse = array(
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash()
        );
        $status = 0;
        if (strpos($file, 'files') !== false) {
            $data = $this->gratyfikant($file);
            $status = 1;
        }

        if (empty($data["ex"])) {
            $message = "Zaimportowano";
        } else {
            $message = $data["ex"];
        }
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode(array("regen" => $reponse, "response" => array("status" => $status, "message" => $message))));
    }

    public function delegacje_list()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        $this->load->model('Delegacje_model', 'customers');
        //$this->output->enable_profiler(true);
        $list = $this->customers->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $customers) {
            $no++;
            $row = array();
            $row["id_delegajci"] = $customers->id_delegajci;
            $row["fk_pracownik"] = $customers->imie . " " . $customers->nazwisko;
            $row["dstart"] = $customers->dstart;
            $row["dend"] = $customers->dend;
            $row["kwota"] = $customers->kwota;
            $row["opis"] = $customers->opis;
            $data[] = $row;
        }
        $call = $this->customers->count_filtered();


        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->customers->count_all(),
            "recordsFiltered" => $call['count'],
            "agregacja" => $call['agregacja'],
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function doreki_list()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        $this->load->model('Doreki_model', 'customers');
        //$this->output->enable_profiler(true);
        $list = $this->customers->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $customers) {
            $no++;
            $row = array();
            $row["id"] = $customers->id;
            $row["zarejestrowano"] = $customers->zarejestrowano;
            $row["kwota"] = $customers->kwota;
            $row["opis"] = $customers->opis;
            $data[] = $row;
        }
        $call = $this->customers->count_filtered();


        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->customers->count_all(),
            "recordsFiltered" => $call['count'],
            "agregacja" => $call['agregacja'],
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function patracenia_list()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        $this->load->model('Potracenia_model', 'customers');
        //$this->output->enable_profiler(true);
        $list = $this->customers->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $customers) {
            $no++;
            $row = array();
            $row["id"] = $customers->id;
            $row["kiedy"] = $customers->kiedy;
            $row["kwota"] = $customers->kwota;
            $row["opis"] = $customers->opis;
            $data[] = $row;
        }
        $call = $this->customers->count_filtered();


        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->customers->count_all(),
            "recordsFiltered" => $call['count'],
            "agregacja" => $call['agregacja'],
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function ajax_list_umowy()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        $this->load->model('Umowy_model', 'customers');
        //$this->output->enable_profiler(true);
        $list = $this->customers->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $customers) {
            $no++;
            $row = array();
            $row["id_umowy"] = $customers->id_umowy;
            $row["fk_pracownik"] = $customers->imie . " " . $customers->nazwisko;
            $row["data_zakonczenia"] = $customers->data_zakonczenia;
            $row["data_rozpoczecia"] = $customers->data_rozpoczecia;
            $row["zus_pracownik"] = $customers->zus_pracownik;
            $row["zus_pracodawca"] = $customers->zus_pracodawca;
            $row["do_wyplaty"] = $customers->do_wyplaty;
            $row["zus_lacznie"] = bcadd($customers->zus_pracownik, $customers->zus_pracodawca);
            $row["do_wyplaty"] = $customers->do_wyplaty;
            $row["brutto"] = $customers->brutto;
            $row["umowa"] = $customers->umowa;
            $data[] = $row;
        }
        $call = $this->customers->count_filtered();


        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->customers->count_all(),
            "recordsFiltered" => $call['count'],
            "agregacja" => $call['agregacja'],
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function ajax_list()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        $this->load->model('Place_model', 'customers');
        //$this->output->enable_profiler(true);
        $list = $this->customers->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $customers) {
            $no++;
            $row = array();
            $row["id_placy"] = $customers->id_placy;
            $row["fk_prac"] = $customers->imie . " " . $customers->nazwisko;
            $row["miesiac"] = $customers->miesiac;
            $row["data_wyplaty"] = $customers->data_wyplaty;
            $row["brutto"] = $customers->brutto;
            $row["zus_pracownik"] = $customers->zus_pracownik;
            $row["zus_pracodawca"] = $customers->zus_pracodawca;
            $row["zus_lacznie"] = bcadd($customers->zus_pracownik, $customers->zus_pracodawca);
            $row["do_wyplaty"] = $customers->do_wyplaty;
            $row["obciazenie"] = $customers->obciazenie;
            $data[] = $row;
        }
        $call = $this->customers->count_filtered();


        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->customers->count_all(),
            "recordsFiltered" => $call['count'],
            "agregacja" => $call['agregacja'],
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    protected function karta($file, $org)
    {

        ini_set('memory_limit', '-1');


        $this->load->library('PHPExcel');

        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objReader->setReadDataOnly(true);


        $pracownik = str_replace("./files/", "", $org);
        $pracownik = str_replace(".xls", "", $pracownik);
        $pracownik = str_replace("_", " ", $pracownik);

        //$encodedPath = iconv("UTF-8", "Windows-1250", $pracownik);
        $objPHPExcel = $objReader->load($file); //("./files/BAZA-KLIENT.xlsx");
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

        $sheetData = array_map('array_filter', $sheetData);
        $sheetData = array_filter($sheetData);

        $this->load->model("Karty_model", "gm");
        $data['s'] = array();
        try {
            $data['s'] = $this->gm
                ->read_data($sheetData)->setPracownik($pracownik)
                ->column_validation()
                ->get_sheet_data()
                ->display_errors()
                ->display_result();
        } catch (Exception $e) {

            $data['ex'] = $e->getMessage();
        }

        return $data;
    }

    protected function umowy_inne($file)
    {

        ini_set('memory_limit', '-1');


        $this->load->library('PHPExcel');

        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(true);


        $objPHPExcel = $objReader->load($file); //("./files/BAZA-KLIENT.xlsx");
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

        $sheetData = array_map('array_filter', $sheetData);
        $sheetData = array_filter($sheetData);

        $this->load->model("Umowy_model", "gm");
        $data['s'] = array();
        try {

            $data['s'] = $this->gm
                ->read_data($sheetData)
                ->column_validation()
                ->get_sheet_data()
                ->display_errors()
                ->display_result();
        } catch (Exception $e) {

            $data['ex'] = $e->getMessage();
        }

        return $data;

    }

    protected function gratyfikant($file)
    {

        ini_set('memory_limit', '-1');


        $this->load->library('PHPExcel');

        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(true);


        $objPHPExcel = $objReader->load($file); //("./files/BAZA-KLIENT.xlsx");
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);


        $this->load->model("Gratyfikant_model", "gm");
        $data['s'] = array();
        try {

            $data['s'] = $this->gm
                ->read_data($sheetData)
                ->column_validation()
                ->get_sheet_data()
                ->display_errors()
                ->display_result();
        } catch (Exception $e) {

            $data['ex'] = $e->getMessage();
        }

        return $data;
        //$this->load->view('pensje/podglad_xlsx', $data);
        //$this->load->view('partial/footer');
    }

}
