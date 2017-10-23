<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Przychody
 *
 * @author Kavvson
 */
class Wydatki extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function Edycja($id)
    {

        $this->output->enable_profiler(true);
        $this->load->model("Wydatki_model", "wy");
        $this->load->model("Kontrakt_model");
        $this->load->model("Kontrahent_model");
        $this->load->model("Js_parts");

        $data = array(
            'title' => 'Wydatki',
            'pages_now' => 'wydatki',
            'pages_now_ex' => 'forms',
            'sublevel' => '',
            // descriptions
            'pageName' => 'Wyceny',
            'navMaster' => 'Zgłoszenia',
            'navSecond' => '',
        );
        $data['wydatki'] = $this->wy->podglad_wydatku($id);


        $rozbicie = $this->wy->pobierz_faktury_powiazane($id);
        $data["powiazane"] = $rozbicie;


        $paliwoWpodkategori = array_filter(
            $rozbicie, function ($e) use (&$searchedValue) {
            return $e->knazw === "Paliwo";
        });

        $auto = array();
        $auto = $this->wy->pobierz_fakture_auto_edycja($id);
        $data["auto"] = $auto;


        if (!isset($id) || !is_numeric($id) || empty($data['wydatki'])) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }

        $this->load->view('partial/header', $data);
        $this->load->view('wydatki/edytuj_wydatek');
        $this->load->view('partial/footer');
    }

    public function Podglad($id = FALSE)
    {


        $this->output->enable_profiler(true);

        $this->load->model("Wydatki_model", "wy");
        $this->load->model("Statistic_model");
        $this->load->model("Kontrakt_model");
        $this->load->model("Kontrahent_model");
        $this->load->model("Adresy_model");
        $wydatek = $this->wy->podglad_wydatku($id);
        $pobierz_historie = $this->wy->pobierz_historie($id);
        if (!isset($id) || !is_numeric($id) || empty($wydatek)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }
        $data = array(
            'title' => 'Wydatki',
            'pages_now' => 'wydatki',
            'pages_now_ex' => 'forms',
            'sublevel' => '',
            // descriptions
            'pageName' => 'Wyceny',
            'navMaster' => 'Zgłoszenia',
            'navSecond' => '',
            'w' => $wydatek,
            'h' => $pobierz_historie
        );
        $rozbicie = array();

        //if ($wydatek->fk_rozbita) {
        $rozbicie = $this->wy->pobierz_faktury_powiazane($id);
        $data["rozbicie"] = $rozbicie;
        //}
        $paliwoWpodkategori = array_filter(
            $rozbicie, function ($e) use (&$searchedValue) {
            return $e->knazw === "Paliwo";
        });

        if ($wydatek->id_kat == 4) {
            $auto = $this->wy->pobierz_fakture_auto($id);
            $data["auto"] = $auto;
        } else if (count($paliwoWpodkategori) == 1) {
            $auto = $this->wy->pobierz_fakture_auto($paliwoWpodkategori[key($paliwoWpodkategori)]->do_wydatku);
            $data["auto"] = $auto;
        }


        $this->load->view('partial/header', $data);
        $this->load->view('wydatki/podglad_wydatku');
        $this->load->view('partial/footer');
    }

    public function Oplac()
    {
        $dp = $this->input->post("dot_platnosci__");

        if (!isset($dp) || !is_numeric($dp)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }

        $this->load->model("Wydatki_model", "wy");
        $this->wy->oplacWydatek($dp);
        // var_dump($_POST);
    }

    public function index()
    {
        $this->load->model('customers_model', 'customers');
       // $this->output->enable_profiler(true);
        $this->load->helper('url');
        $this->load->helper('form');

        $countries = $this->customers->get_list_countries();

        $opt = array('' => 'Wszystkie',
            '1' => 'Łódź',
            '2' => 'Wrocław',
            '3' => 'Kraj',
            '4' => 'Biuro');
        foreach ($countries as $country) {
            $opt[$country] = $country;
        }
        $data['title'] = "Wydatki";
        $data['form_country'] = form_dropdown('', $opt, '', 'id="s_rejon" class="form-control"');
        $data['widget'] = $this->load->view('wydatki/dodaj_wydatek', $data, TRUE);
        $this->load->view('partial/header', $data);
        $this->load->view('wydatki/lista_dt', $data);
        $this->load->view('partial/footer');
    }

    public function wydatek_modal()
    {
        $this->load->view('wydatki/dodaj_wydatek');
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

    /*
     * Select2 Kategoria wydatku
     */

    public function s2_kategorie_wydatku()
    {
        $this->load->model("Wydatki_model", "wyd");
        $this->wyd->kategorie_wydatku();
    }

    public function s2_wydatki_pracownika($id)
    {
        $this->load->model("Wydatki_model", "wyd");
        $this->wyd->wydatki_select($id);
    }

    /*
     * Ajax
     */

    public function edytuj_wydatek($id)
    {
        $this->load->model("Wydatki_model", "wyd");
        $this->wyd->modyfikacja_wydatku($id);
    }

    public function nowy_wydatek()
    {
        $this->load->model("Wydatki_model", "wyd");
        $this->wyd->dodaj_wydatek();
    }

}
