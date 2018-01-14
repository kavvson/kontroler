<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pojazdy
 *
 * @author Kavvson
 */
class Pojazdy extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function zs($do_pojazdu)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        $this->load->model("Pojazdypliki_model");
        $this->Pojazdypliki_model->zalacz_plik($do_pojazdu);
    }

    public function zalacz_skan($do_pojazdu)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        $data['id'] = $do_pojazdu;
        $this->load->view("widget/zalacz_ubezpieczenie",$data);
    }

    public function Dane($id) {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        if (!isset($id) || !is_numeric($id)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }
        $this->load->model("Pojazdy_model", "k");
        $this->load->model("Pojazdypliki_model", "s");

        $data = array(
            'title' => 'Podgląd pojazdu',
            'pages_now' => 'Pojazd podgląd',
            'pages_now_ex' => 'forms',
            'sublevel' => '',
        );

        $data['pojazd'] = $this->k->get_vehicle("getByID",$id)['responce'][0];
        $data['oc'] = $this->s->pobierz_ostatnie_pliki($id,1);
        $data['ac'] = $this->s->pobierz_ostatnie_pliki($id,2);
        $data['przeglad'] = $this->s->pobierz_ostatnie_pliki($id,3);
        $data['id'] = $id;
        if (empty($data['pojazd']->poj_id)) {
            show_error("Nie odnaleziono pojazdu");
        }

        $this->load->view('pojazdy/podglad', $data);

    }

    public function WydatkiDT($id)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        if (!isset($id) || !is_numeric($id)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }

        $this->load->model('customers_model', 'customers');

        $this->load->helper('url');
        $this->load->helper('form');

        $data['id'] = $id;

        $this->load->view('pojazdy/wydatki_dt', $data);
    }

    public function Podglad($id) {

        $this->output->enable_profiler(true);
        $this->load->model("Pojazdy_model", "k");

        $data = array(
            'title' => 'Podgląd pojazdu',
            'pages_now' => 'Pojazd podgląd',
            'pages_now_ex' => 'forms',
            'sublevel' => '',
        );

        $data['pojazd'] = $this->k->get_vehicle("getByID",$id)['responce'][0];
        $data['id'] = $id;
        if (empty($data['pojazd']->poj_id)) {
            show_error("Nie odnaleziono pojazdu");
        }

        $this->load->view('partial/header', $data);
        $this->load->view('pojazdy/landing', $data);
        $this->load->view('partial/footer');
    }

    public function Przebieg(){
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        $this->load->model("Pojazdy_model");
        $this->Pojazdy_model->dodaj_przebieg();
    }

    public function przebiegi($pojazd)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        $this->load->model("Pojazdy_model");
        echo $this->Pojazdy_model->przebiegi($pojazd);
    }

    public function wydatki($pojazd = null)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        $this->load->model("Pojazdy_model");
        $this->Pojazdy_model->wydatki($pojazd);
    }

    public function index() {

        $this->output->enable_profiler(true);

        // $this->load->model("Pracownicy_model", "pracownicy");
        // $p = $this->pracownicy->lista_pracownikow();

        $data = array(
            'title' => 'Pojazdy lista',
            'pages_now' => 'Pojazdy',
            'pages_now_ex' => 'forms',
            'sublevel' => '',
            // descriptions
            'pageName' => 'Pojazdy',
            'navMaster' => 'Dodaj',
            'navSecond' => '',
        );

        $body = array(
                //'pracownicy' => $p
        );

        $this->load->view('partial/header', $data);
        $this->load->view('pojazdy/lista_dt', $body);
        $this->load->view('partial/footer');
    }

    public function Dodaj() {

        $this->output->enable_profiler(true);

        // $this->load->model("Pracownicy_model", "pracownicy");
        // $p = $this->pracownicy->lista_pracownikow();

        $data = array(
            'title' => 'Pojazd',
            'pages_now' => 'pojazdy',
            'pages_now_ex' => 'forms',
            'sublevel' => '',
            // descriptions
            'pageName' => 'Pojazd',
            'navMaster' => 'Dodaj',
            'navSecond' => '',
        );

        $body = array(
                //'pracownicy' => $p
        );

        $this->load->view('partial/header', $data);
        $this->load->view('pojazdy/dodaj_pojazd', $body);
        $this->load->view('partial/footer');
    }

    public function s2_lista() {
        $this->load->model("Pojazdy_model", "p");
        $this->p->dropdown_pojazdy();
    }

    public function Dodaj_pojazd() {
        $this->load->model("Pojazdy_model", "p");
        $this->p->Dodaj();
    }

    public function Modyfikuj_pojazd() {
        $this->load->model("Pojazdy_model", "p");
        $this->p->Dodaj("modyfikacja");
    }
    public function ajax_list()
    {
        $this->load->model('customers_model', 'customers');
        //$this->output->enable_profiler(true);
        $list = $this->customers->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $customers) {
            $no++;
            $row = array();
            $row["nr"] = $customers->id_wydatku;
            $row["rejont"] = $customers->rejont;
            $row["kupujacy"] = $customers->kupujacy;
            $row["kwota_brutto"] = $customers->kwota_brutto;
            $row["kwota_netto"] = $customers->kwota_netto;
            $row["dokument"] = $customers->dokument;
            $row["data_zakupu"] = $customers->data_zakupu;
            $row["kontrahent"] = $customers->kontrah;
            $row["cel_zakupu"] = $customers->cel_zakupu;
            $row["dodal"] = $customers->dodal;
            $row["wartosc_vat"] = $customers->wartosc_vat;
            $row["procent_vat"] = $customers->procent_vat;
            $row["metoda_platnosci"] = $customers->metoda_platnosci;
            $row["kat"] = $customers->kat;
            $row["pozostala_kwota"] = $customers->pozostala_kwota;
            $row["zaplacona_kwota"] = $customers->zaplacona_kwota;
            $row["rozbita"] = $customers->rozbita;
            $row["termin"] = $customers->termin;
            $row["ddif"] = $customers->ddif;
            $row["priorytet"] = $customers->priorytet;
            $row["skan"] = $customers->skan_id;
            $row["prac"] = $customers->id_pracownika;
            $row["pro_forma"] = $customers->pro_forma;
            $data[] = $row;
        }
        $call = $this->customers->count_filtered();

        //var_dump($data);
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

    public function ajax_list_pdt() {
        $this->load->model("Pojazdy_dt_model");
        //$this->output->enable_profiler(true);
        $list = $this->Pojazdy_dt_model->get_datatables();
        $data = array();
        $no = $this->input->post('start');

        foreach ($list as $customers) {
            $no++;
            $row = array();
            $row["poj_id"] = $customers->poj_id;
            $row["model"] = $customers->model;
            $row["nr_rej"] = $customers->nr_rej;
            $row["ubezp_oc"] = $customers->ubezp_oc;
            $row["ubezp_ac"] = $customers->ubezp_ac;
            $row["marka"] = $customers->marka;
            $row["przeglad"] = $customers->przeglad;
            $row["przebieg"] = $customers->przebieg;
            $row["stawka_vat"] = $customers->stawka_vat;

            $data[] = $row;
        }
        $call = $this->Pojazdy_dt_model->count_filtered();


        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->Pojazdy_dt_model->count_all(),
            "recordsFiltered" => $call['count'],
            'respo' => $call['respo'],
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

}
