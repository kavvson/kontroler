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
class Przychody extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->is_admin) {
            show_error("Niewystarczające uprawnienia do przeglądania przychodów", 404, 'Brak uprawnień');
        }
    }

    public function UsunFakture($id)
    {
        if (!$this->is_admin) {
            show_error("Niewystarczające uprawnienia do przeglądania przychodów", 404, 'Brak uprawnień');
        }
        if (!isset($id) || !is_numeric($id)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }
        $this->load->model("Przychody_model", "p");
        $this->p->UsunFakture($id);

    }

    public function ZmienNrFaktury($id)
    {
        if (!isset($id) || !is_numeric($id)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }

        $this->load->model("Przychody_model", "p");
        $this->p->zmianaNrFaktury($id);
    }

    public function karta_rozlicz()
    {
        $this->load->model("Wydatki_model", "wy");
        $this->wy->rozlicz_wydatki("Przychod");
    }

    public function Oplac()
    {
        $dp = $this->input->post("dot_platnosci__");

        if (!isset($dp) || !is_numeric($dp)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }
        $this->load->model("Przychody_model", "p");
        $this->p->oplacFakture($dp);
    }

    public function Podglad($id = FALSE)
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $this->output->enable_profiler(true);

        $this->load->model("Przychody_model", "p");
        $this->load->model("Wydatki_model");
        $this->load->model("Statistic_model");
        $wydatek = $this->p->podglad_przychodu($id);
        $pobierz_platnosci = $this->p->pobierz_platnosci($id);
        $pobierz_historie = $this->p->pobierz_historie($id);

        $pobierz_pracownikow = $this->p->pobierz_pracownikow($id);

        $pobierz_korekty = $this->p->pobierz_korekty($id);


        if (!isset($id) || !is_numeric($id) || empty($wydatek)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }

        if (empty($wydatek)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }


        $data = array(
            'title' => 'Faktury',
            'pages_now' => 'wydatki',
            'pages_now_ex' => 'forms',
            'sublevel' => '',
            // descriptions
            'pageName' => 'Wyceny',
            'navMaster' => 'Zgłoszenia',
            'navSecond' => '',
            'w' => $wydatek,
            'p' => $pobierz_platnosci,
            'h' => $pobierz_historie,
            'prac' => $pobierz_pracownikow,
            'pobierz_korekty' => $pobierz_korekty,
        );

        $this->load->view('partial/header', $data);

        $this->load->view('przychody/podglad_przychodu');
        $this->load->view('partial/footer');
    }

    public function Monit($id = FALSE)
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        if (empty($id)) {
            die();
        }
        $this->load->model("Przychody_model", "p");
        $this->load->model("Statistic_model");
        $this->load->model("Kontrahent_model");
        $wydatek = $this->p->podglad_przychodu($id);

        $dane = $this->Kontrahent_model->pokaz_kontrahenta($id)[0];

        $data = array(
            'pageName' => 'Wyceny',
            'navMaster' => 'Zgłoszenia',
            'navSecond' => '',
            // 'w' => $wydatek,
            'id' => $id,
            'nabywca' => $dane
        );

        $html = $this->load->view('pdfy/monit', $data, true);

        $pdfFilePath = "Monit-" . $dane->nazwa . "-" . date("Y-m-d") . ".pdf";

        $this->load->library('M_pdf');

        $this->m_pdf->pdf->WriteHTML($html);

        //download it.
        $this->m_pdf->pdf->Output($pdfFilePath, "I");


    }

    public function index()
    {
        $this->load->model("Przychody_dt_model");
        $this->output->enable_profiler(true);
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->model("Przychody_model", "przy");
        $p = $this->przy->id_do_faktury();
        $countries = $this->Przychody_dt_model->get_list_countries();

        $opt = array('' => 'Wszystkie',
            '1' => 'Łódź',
            '2' => 'Wrocław',
            '3' => 'Kraj',
            '4' => 'Biuro');
        foreach ($countries as $country) {
            $opt[$country] = $country;
        }

        $data['form_country'] = form_dropdown('', $opt, '', 'id="s_rejon" class="form-control"');
        $data['title'] = "Faktury";
        $data['faktura'] = $p;
        $this->load->view('partial/header', $data);
        $data['widget'] = $this->load->view('przychody/nowa_faktura', $data, TRUE);
        $this->load->view('przychody/lista_dt', $data);
        $this->load->view('partial/footer');
    }

    public function gtfnr()
    {
        $this->load->model("Przychody_model");
        echo $this->Przychody_model->decode_json_f_nr($this->Przychody_model->get_custom_f_nr());
    }

    public function ajax_list_pdt()
    {
        $this->load->model("Przychody_dt_model");
        //$this->output->enable_profiler(true);
        $list = $this->Przychody_dt_model->get_datatables();
        $data = array();
        $no = $this->input->post('start');

        foreach ($list as $customers) {
            $no++;
            $row = array();
            $row["nr"] = $customers->id_przychodu;
            $row["rejont"] = $customers->rejont;
            // $row["kupujacy"] = $customers->kupujacy;
            $row["kwota_brutto"] = $customers->wartosc;
            $row["kwota_netto"] = $customers->netto;
            $row["dokument"] = $customers->numer;
            $row["data_zakupu"] = $customers->dodano;
            $row["kontrahent"] = $customers->kontrah;
            $row["dodal"] = $customers->dodal;
            $row["wartosc_vat"] = $customers->vat_lacznie;
            $row["z_dnia"] = $customers->z_dnia;
            $row["termin"] = $customers->termin_platnosci;
            $row["uwagi"] = $customers->uwagi;
            $row["ddif"] = $customers->ddif;
            $row["otrzymana_kwota"] = $customers->otrzymana_kwota;
            $row["pozostala_kwota"] = $customers->pozostala_kwota;
            $row["fk_link"] = $customers->fk_link;
            $row["kor_nazwa"] = $customers->kor_nazwa;
            $row["nvat"] = $customers->nvat;
            $row["nbrut"] = $customers->nbrut;
            $row["nnet"] = $customers->nnet;


            $data[] = $row;
        }
        $call = $this->Przychody_dt_model->count_filtered();


        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->Przychody_dt_model->count_all(),
            "recordsFiltered" => $call['count'],
            "agregacja" => $call['agregacja'],
            "data" => $data,
        );
        //output to json format
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($output));

    }


    public function Korekta($id_przychodu)
    {
        //die("Do zrobienia");
        $this->load->model('Przychody_model');

        $data = array(
            'title' => 'Korekta faktury',
            'pages_now' => 'wydatki',
            'pages_now_ex' => 'forms',
            'sublevel' => '',
            // descriptions
            'pageName' => 'Wyceny',
            'navMaster' => 'Zgłoszenia',
            'navSecond' => '',
        );
        $data['faktura'] = $this->Przychody_model->podglad_przychodu($id_przychodu);
        $data['wpisy'] = $this->Przychody_model->przedmioty_faktury($id_przychodu);

        if (!isset($id_przychodu) || !is_numeric($id_przychodu) || empty($data['faktura'])) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }
        $this->load->view('partial/header', $data);
        $this->load->view('przychody/edit', $data);
        $this->load->view('partial/footer');
    }

    function edit($id_przychodu)
    {
        //die("Do zrobienia");
        $this->load->model('Przychody_model');

        $data = array(
            'title' => 'Korekta faktury',
            'pages_now' => 'wydatki',
            'pages_now_ex' => 'forms',
            'sublevel' => '',
            // descriptions
            'pageName' => 'Wyceny',
            'navMaster' => 'Zgłoszenia',
            'navSecond' => '',
        );
        $data['faktura'] = $this->Przychody_model->podglad_przychodu($id_przychodu);
        $data['wpisy'] = $this->Przychody_model->przedmioty_faktury($id_przychodu);

        if (!isset($id_przychodu) || !is_numeric($id_przychodu) || empty($data['faktura'])) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }
        $this->load->view('partial/header', $data);
        $this->load->view('przychody/edit_old', $data);
        $this->load->view('partial/footer');
    }

    public function edytuj_fakture($id)
    {
        $this->load->model('Przychody_model');
        $this->Przychody_model->edycja($id);
    }

    public function krekta_faktury($id)
    {
        $this->load->model('Przychody_model');
        $this->Przychody_model->korekta($id);
    }


    public function PodgladFaktury($numer, $korekta = null)
    {
        $this->load->model("Przychody_model", "p");
        $this->load->model("Kontrahent_model", "k");
        $this->load->helpers("Helpers");
        $pf = $this->p->przedmioty_faktury($numer);
        $data = array(
            "item" => $pf,
            "nabywca" => $this->k->pokaz_kontrahenta($pf[0]->fk_kontrahent)[0],
            'title' => "Podgląd faktury",
        );
        $data['duplikat'] = "org";

        if ($korekta === "duplikat") {
            $data['duplikat'] = "duplikat";
        }

        if ($korekta) {
            $korekty = $this->p->pobierz_wpisy_korekty($korekta);
            $data['korekta'] = $korekty['re'];
            $data['nazwa'] = $korekty['nazwa'];
        }


        if ($korekta && is_numeric($korekta)) {
            if (empty($korekty)) {
                die("Brak korekty");
            }
            $html = $this->load->view('przychody/wzor_faktury', $data, true);
        } else {
            $html = $this->load->view('przychody/wzor_faktury_org', $data, true);
        }


        //this the the PDF filename that user will get to download
        $pdfFilePath = $pf[0]->numer . ".pdf";

        //load mPDF library
        $this->load->library('M_pdf');


        //  $this->m_pdf->pdf->SetDisplayMode('fullpage');

        // $this->m_pdf->pdf->list_indent_first_level = 0; // 1 or 0 - whether to indent the first level of a list
        //generate the PDF from the given html
        $this->m_pdf->pdf->WriteHTML($html);

        //download it.
        $this->m_pdf->pdf->Output($pdfFilePath, "I");
        // $this->load->view('przychody/wzor_faktury', $data);
    }

    public function Nowy()
    {
        die();

        $this->load->model("Przychody_model", "przy");
        $p = $this->przy->id_do_faktury();
        $data['faktura'] = $p;
        $this->load->view('przychody/nowa_faktura', $data);
    }

    /*
     * Ajax
     */

    public function nowy_przychod()
    {
        $this->load->model("Przychody_model", "przychody");
        $this->przychody->dodaj_przychod();
    }

}
