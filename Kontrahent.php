<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Kontrahent
 *
 * @author Kavvson
 */
class Kontrahent extends MY_Controller {


    public function __construct() {
        parent::__construct();
    }

    public function index() {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $this->output->enable_profiler(true);

        // $this->load->model("Pracownicy_model", "pracownicy");
        // $p = $this->pracownicy->lista_pracownikow();

        $data = array(
            'title' => 'Kontrahent',
            'pages_now' => 'Kontrahent',
            'pages_now_ex' => 'forms',
            'sublevel' => '',
            // descriptions
            'pageName' => 'Kontrahent',
            'navMaster' => 'Dodaj',
            'navSecond' => '',
        );

        $body = array(
                //'pracownicy' => $p
        );

        $this->load->view('partial/header', $data);
        $this->load->view('kontrahent/lista_dt', $body);
        $this->load->view('partial/footer');
    }

    public function Podglad($id) {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $this->output->enable_profiler(true);
        $this->load->model("Kontrahent_model", "k");

        $data = array(
            'title' => 'Kontrahent',
            'pages_now' => 'Kontrahent',
            'pages_now_ex' => 'forms',
            'sublevel' => '',
            // descriptions
            'pageName' => 'Kontrahent',
            'navMaster' => 'Dodaj',
            'navSecond' => '',
        );

        $data['kontrahent'] = $this->k->pokaz_kontrahenta($id)[0];
        if (empty($data['kontrahent'])) {
            show_error("Nie odnaleziono kontrahenta");
        }

        $this->load->view('partial/header', $data);
        $this->load->view('kontrahent/podglad', $data);
        $this->load->view('partial/footer');
    }

    public function Dodaj() {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $this->output->enable_profiler(true);

        // $this->load->model("Pracownicy_model", "pracownicy");
        // $p = $this->pracownicy->lista_pracownikow();

        $data = array(
            'title' => 'Kontrahent',
            'pages_now' => 'Kontrahent',
            'pages_now_ex' => 'forms',
            'sublevel' => '',
            // descriptions
            'pageName' => 'Kontrahent',
            'navMaster' => 'Dodaj',
            'navSecond' => '',
        );


        $this->load->view('partial/header', $data);
        $this->load->view('kontrahent/dodaj_kontrahenta');
        $this->load->view('partial/footer');
    }

    public function lista_kontrahentow() {
        $this->load->model("Kontrahent_model", "rejon");
        $this->rejon->populate();
    }

    // ajax

    public function Edytuj_Kontrahenta() {
        $this->load->model("Kontrahent_model", "k");
        $this->k->Edytuj_kontrahenta();
    }

    public function Dodaj_Kontrahenta() {
        $this->load->model("Kontrahent_model", "k");
        $this->k->Dodaj_kontrahenta();
    }

    public function kontrahent_edytuj_modal($id) {
        $this->load->model("Kontrahent_model", "k");
        $data['kontrahent'] = $this->k->pokaz_kontrahenta($id)[0];
        if (empty($data['kontrahent'])) {
            show_error("Nie odnaleziono kontrahenta");
        }
        $this->load->view('kontrahent/edytuj_modal', $data);
    }

    public function ajax_list_pdt() {
        $this->load->model("Kontrahent_dt_model");
        //$this->output->enable_profiler(true);
        $list = $this->Kontrahent_dt_model->get_datatables();
        $data = array();
        $no = $this->input->post('start');

        foreach ($list as $customers) {
            $no++;
            $row = array();
            $row["id_kontrahenta"] = $customers->id_kontrahenta;
            $row["nazwa"] = $customers->nazwa;
            $row["nip"] = $customers->nip;
            $row["regon"] = $customers->regon;
            $row["krs"] = $customers->krs;
            $row["spec"] = $customers->spec;
            $row["phone"] = $customers->phone;
            $row["char_prawny"] = $customers->char_prawny;
            $data[] = $row;
        }
        $call = $this->Kontrahent_dt_model->count_filtered();


        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->Kontrahent_dt_model->count_all(),
            "recordsFiltered" => $call['count'],
            'respo' => $call['respo'],
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

}
