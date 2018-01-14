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
class Statystyka extends MY_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->model("Statistic_model");
        //$this->output->enable_profiler(true);
        if (!$this->ion_auth->in_group(array(1, 2))) {
            die("Brak uprawnień");
        }
    }

    public function pojazdy()
    {

        $dane = $this->Statistic_model->pojazdy();
        $rok = $this->Statistic_model->Pojazdy_koszty_skalaroku();
        $header['title'] = "Analiza kosztów pojazdów";
        $out['rok'] = $rok;
        $out['d'] = $dane['baza'];
        $out['vs'] = $dane['odniesienie'];
        $this->load->view("partial/header", $header);
        $this->load->view("Analityka/pojazdy", $out);
        $this->load->view("partial/footer");

    }

    public function FCF()
    {

        // $dane =$this->Statistic_model->pojazdy();
        $p_dziennie_faktyczne = $this->Statistic_model->przychody_data_platnosci_dziennie_tenrok();
        $header['title'] = "Analiza FCF";
        $out['d'] = null;
        $out['p_dziennie_faktyczne'] = $this->Statistic_model->przelewy_dziennie_tenrok();
        $out['zweryfikowane_wydatki'] = $this->Statistic_model->s_wydatki_skalaroku();
        $out['zweryfikowane_przychody'] = $this->Statistic_model->s_Przychody_skalaroku();
        $this->load->view("partial/header", $header);
        $this->load->view("Analityka/cf", $out);
        $this->load->view("partial/footer");

    }

    public function wydatki()
    {
        $dane = $this->Statistic_model->s_wydatki();
        $rejony = $this->Statistic_model->s_wydatki_rejony();

        $header['title'] = "Analiza wydatków";
        $out['d'] = $dane; // procedura
        $out['r'] = $rejony;  // procedura
        $out['_ow_w'] = $this->Statistic_model->s_ostatnie_wydatki_ten_miesiac(); // wszystkie nieoplacone wydatki
        $out['srednia'] = $this->Statistic_model->s_wydatki_sredniczasplacenia(); // procedura
        $out['wydatki_faktury'] = $this->Statistic_model->Wydatki_faktury_statystyka(); // procedura

        $out['wykres'] = $this->Statistic_model->s_wydatki_skalaroku();
        $this->load->view("partial/header", $header);
        $this->load->view("Analityka/wydatki", $out);
        $this->load->view("partial/footer");

    }

    public function przychody()
    {
        // $dane = $this->Statistic_model->s_przychody(); unused


        $rejony = $this->Statistic_model->s_przychody_rejon(); // procedura
        $rejony_w = $this->Statistic_model->s_wydatki_rejony(); // procedura

        $wykres = $this->Statistic_model->s_przychody_wkres_lin();

        $header['title'] = "Analiza przychodów";
        // $out['d'] = $dane;
        $out['r'] = $rejony;
        $out['r_w'] = $rejony_w;

        $out['wykres'] = $wykres;

        $out['_ow_w'] = $this->Statistic_model->s_ostatnie_przychody_ten_miesiac();

        $out['srednia'] = $this->Statistic_model->s_przychody_sredniczasplacenia(); // procedura

        $out['klienci'] = $this->Statistic_model->s_przychody_klient();  // procedura

        $this->load->view("partial/header", $header);
        $this->load->view("Analityka/przychody", $out);
        $this->load->view("partial/footer");

    }

    public function FCF_korekta()
    {
        $this->Statistic_model->FCF_korekta_add();
    }

    public function pracownicy()
    {

        $p = $this->Statistic_model->s_pracownicy();
        $fcf = $this->Statistic_model->FCF_pracownik();

        $header['title'] = "Analiza pracowników";
        $out['fcf'] = $fcf['wartosci'];
        $out['pracownicy'] = $p['wyniki'];
        $out['obliczenia'] = $p['obliczenia'];
        $out['kp'] = $p['kp'];
        $this->load->view("partial/header", $header);
        $this->load->view("Analityka/pracownicy", $out);
        $this->load->view("partial/footer");
    }

    public function usun_korekte()
    {
        $this->Statistic_model->usun_korekte();
    }

    public function FCF2()
    {

        $p = $this->Statistic_model->przelewy_dziennie_tenrok();

        $p_dziennie_faktyczne = $this->Statistic_model->przychody_data_platnosci_dziennie_tenrok();
        $fcf2 = $this->Statistic_model->FCF2();

        $header['title'] = "Analiza pracowników";

        $out['przelewy_dziennie_tenrok'] = $p['return'];
        $out['fcf_obj'] =$fcf2;
        $out['Ppk'] = $p['suma'];
        $out['przychody_dziennie_tenrok'] = $p_dziennie_faktyczne;

        $this->load->view("partial/header", $header);
        $this->load->view("Analityka/nowyfcf", $out);
        $this->load->view("partial/footer");
    }

    public function fcf_dt()
    {

        $this->load->model('Generic_DT_model', 'customers');

        $this->customers->table = "forecast_correction";
        $this->customers->agregacja = FALSE;
        $this->customers->main_field = "year";
        $this->customers->order = array('month' => 'asc');

        $this->customers->column_order = array(
            null,
            'method',
            'month',
            'year',
            'value',
            'type',
            'opis'
        );
        $list = $this->customers->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $customers) {
            $no++;
            $row = array();
            $row["nr"] = $customers->PK_cor;
            $row["method"] = $customers->method;
            $row["month"] = $customers->month;
            $row["year"] = $customers->year;
            $row["value"] = $customers->value;
            $row["type"] = $customers->type;
            $row["opis"] = $customers->opis;

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
}
