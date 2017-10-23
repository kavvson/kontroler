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

    public function Podglad($id) {

        $this->output->enable_profiler(true);
        $this->load->model("Kontrahent_model", "k");

        $data = array(
            'title' => 'Pojazd podgląd',
            'pages_now' => 'Pojazd podgląd',
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
        $this->load->view('pojazdy/podglad', $data);
        $this->load->view('partial/footer');
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
