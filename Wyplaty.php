<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Wyplaty extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        //$this->output->enable_profiler(true);

    }

    public function index()
    {

        $this->load->model("Pracownicy_model", "pracownicy");
        $this->load->model("Wyplaty_model");
        $this->load->model("Statistic_model");
        $p = $this->pracownicy->lista_pracownikow();
        $w = $this->Wyplaty_model->Dostepne_raporty();

        $data = array(
            'title' => 'Wypłaty',
            'pages_now' => 'lista_pracownikow',
            'pages_now_ex' => 'forms',
        );

        $body = array('pracownicy' => $p,'w'=>$w);

        $this->load->view('partial/header', $data);
        $this->load->view('wyplaty/lista_pracownikow', $body);
        $this->load->view('partial/footer');
    }

    public function Rozlicz()
    {
        if (!$this->is_admin) {
            show_error("Niewystarczające uprawnienia do opłacania wypłat", 404, 'Brak uprawnień');
        }
        $this->load->model("Wyplaty_model","w");
        $this->w->Rozlicz();
    }

    public function WyplatyAjax($m,$r){
        if (!$this->is_admin) {
            show_error("Niewystarczające uprawnienia do opłacania wypłat", 404, 'Brak uprawnień');
        }

        $this->load->model("Generatorpdf_model", "wyplaty");

        $p = $this->wyplaty->pobierz_wyplaty_pracownikow($m,$r);
        $body = array('wyplaty' => $p);
        $this->load->view('wyplaty/wyplatyAjax', $body);
    }


    public function Generuj_Wyplaty()
    {
        $this->load->model("Wyplaty_model","w");
        $this->w->Dodaj_wyplaty();

    }

    public function Potwierdzanie()
    {
        if (!$this->is_admin) {
            show_error("Niewystarczające uprawnienia do opłacania wypłat", 404, 'Brak uprawnień');
        }

        $this->load->model("Generatorpdf_model", "wyplaty");

        $p = $this->wyplaty->pobierz_wyplaty_pracownikow(1,2017);


        $data = array(
            'title' => 'Wypłaty - Potwierdzenia',
            'pages_now' => 'lista_pracownikow',
            'pages_now_ex' => 'forms',
        );

        $body = array('wyplaty' => $p);

        $this->load->view('partial/header', $data);
        $this->load->view('wyplaty/potwierdzenie', $body);
        $this->load->view('partial/footer');
    }

}
