<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Przelewy extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        //$this->output->enable_profiler(true);

    }

    public function Import_przychodow(){
        $this->output->enable_profiler(true);

        $data = array(
            'title' => 'Importowanie przelewów',
        );


        $this->load->view('partial/header', $data);
        $this->load->view('przelewy/wgraj_z_banku', $data);
        $this->load->view('partial/footer');
    }

    public function Lista(){

        $data = array(
            'title' => 'Lista przelewów',
        );


        $this->load->view('partial/header', $data);
        $this->load->view('przelewy/lista', $data);
        $this->load->view('partial/footer');
    }

    public function Lista_ajax(){
        $this->load->model('Generic_DT_model', 'customers');

        $this->customers->table = "przychody_przelewy";
        $this->customers->agregacja = FALSE;
        $this->customers->main_field = "data_waluty";
        $this->customers->order = array('data_waluty' => 'asc');

        $this->customers->column_order = array(
            null,
            'data_operacji',
            'data_waluty',
            'typ',
            'kwota'
        );
        $list = $this->customers->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $customers) {
            $no++;
            $row = array();
            $row["id"] = $customers->id;
            $row["data_operacji"] = $customers->data_operacji;
            $row["data_waluty"] = $customers->data_waluty;
            $row["opis"] = $customers->typ;
            $row["kwota"] = $customers->kwota;
            $data[] = $row;
        }
        $call = $this->customers->count_filtered();

        //var_dump($data);
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->customers->count_all(),
            "recordsFiltered" => $call['count'],
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function Import_przychodow_ajax()
    {
        // if (!$this->is_admin) {
        //    show_error("Niewystarczające uprawnienia do importu płac", 404, 'Brak uprawnień');
        //}
        $this->load->model("Przelewy_model", "gm");

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

    protected function gratyfikant($file)
    {

        ini_set('memory_limit', '-1');


        $this->load->library('PHPExcel');

        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(true);


        $objPHPExcel = $objReader->load($file); //("./files/BAZA-KLIENT.xlsx");
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);


        $this->load->model("Przelewy_model", "gm");
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
