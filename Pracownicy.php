<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pracownicy extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        //$this->output->enable_profiler(true);

    }

    public function index()
    {

        $this->load->model("Pracownicy_model", "pracownicy");
        $p = $this->pracownicy->lista_pracownikow();

        $data = array(
            'title' => 'Lista pracowników',
            'pages_now' => 'lista_pracownikow',
            'pages_now_ex' => 'forms',
            'sublevel' => '',
            // descriptions
            'pageName' => 'Wyceny',
            'navMaster' => 'Zgłoszenia',
            'navSecond' => '',
        );

        $body = array('pracownicy' => $p);

        $this->load->view('partial/header', $data);
        $this->load->view('pracownicy/lista_pracownikow', $body);
        $this->load->view('partial/footer');
    }

    public function Premie($id)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        if (!isset($id) || !is_numeric($id)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }

        $data['title'] = "Premie";
        $data['id'] = $id;
        $this->load->view('place/premie', $data);
    }

    public function Uzupelnij($id)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        if (!isset($id) || !is_numeric($id)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }
        if (!$this->is_admin) {
            show_error("Niewystarczające uprawnienia do podglądu", 404, 'Brak uprawnień');
        }
        $this->load->model("Karty_model");
        $this->Karty_model->pobierz_pozostale($id);
    }

    public function Aktywacja()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        if (!$this->is_admin) {
            show_error("Niewystarczające uprawnienia do tej czynności", 404, 'Brak uprawnień');
        }
        $this->load->model("Pracownicy_model");
        $this->Pracownicy_model->aktywacja_pracownika();
    }

    public function Karty($id)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        if (!isset($id) || !is_numeric($id)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }
        //if (!$this->is_admin) {
        //    show_error("Niewystarczające uprawnienia do podglądu", 404, 'Brak uprawnień');
        //}
        if (!isset($id) || !is_numeric($id)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }

        $this->load->model("Karty_model");

        $data['title'] = "Karty";
        $data['id'] = $id;

        $this->load->view('pracownicy/partial/karty', $data);

    }

    public function DodajZaliczke($id)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        if (!isset($id) || !is_numeric($id)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }
        $this->load->model("Karty_model");
        $this->Karty_model->dodaj_zaliczke($id);
    }

    public function RozliczKarty()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        $this->load->model("Karty_model");
        $this->Karty_model->rozlicz_karte();

    }

    public function OdepnijZKarty()
    {
        $this->load->model("Karty_model");
        $this->Karty_model->OdepnijZKarty();

    }

    public function KartyLista()
    {

        // if (!$this->is_admin) {
        //     show_error("Niewystarczające uprawnienia do podglądu", 404, 'Brak uprawnień');
        // }

        $data['title'] = "Karty";
        $this->load->model("Karty_model");
        $data['wydatki_karta'] = $this->Karty_model->Wydatki_do_karty();
        $this->load->view('partial/header', $data);
        $this->load->view('place/karty', $data);
        $this->load->view('partial/footer');

    }

    public function Umowy($id)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        if (!isset($id) || !is_numeric($id)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }
        // if (!$this->is_admin) {
        //     show_error("Niewystarczające uprawnienia do podglądu", 404, 'Brak uprawnień');
        // }
        if (!isset($id) || !is_numeric($id)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }
        $data['title'] = "Umowy";
        $data['id'] = $id;
        $this->load->view('place/umowy', $data);
    }

    public function Wydatki($id)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        if (!isset($id) || !is_numeric($id)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }
        //if (!$this->is_admin) {
        //    show_error("Niewystarczające uprawnienia do podglądu", 404, 'Brak uprawnień');
        //}
        $this->load->model('customers_model', 'customers');

        $this->load->helper('url');
        $this->load->helper('form');

        $data['id'] = $id;

        $this->load->view('pracownicy/partial/lista_dt', $data);
    }

    public function WszystkieDelegacje()
    {

        // if (!$this->is_admin) {
        //    show_error("Niewystarczające uprawnienia do podglądu", 404, 'Brak uprawnień');
        // }

        $data['title'] = "Lista delegacji";
        $this->load->view('partial/header', $data);
        $this->load->view('pracownicy/delegacje_wszyscy', $data);
        $this->load->view('partial/footer');

    }

    public function ListaDelegacji($id)
    {
        if (!isset($id) || !is_numeric($id)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        //  if (!$this->is_admin) {
        //     show_error("Niewystarczające uprawnienia do podglądu", 404, 'Brak uprawnień');
        // }

        $data['id'] = $id;

        $this->load->view('pracownicy/partial/delegacje', $data);
    }

    public function PotraconePlatnosci($id)
    {
        if (!isset($id) || !is_numeric($id)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        //  if (!$this->is_admin) {
        //     show_error("Niewystarczające uprawnienia do podglądu", 404, 'Brak uprawnień');
        // }

        $data['id'] = $id;

        $this->load->view('pracownicy/partial/patracenia', $data);
    }

    public function PlatnosciDoReki($id)
    {
        if (!isset($id) || !is_numeric($id)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        //  if (!$this->is_admin) {
        //     show_error("Niewystarczające uprawnienia do podglądu", 404, 'Brak uprawnień');
        // }

        $data['id'] = $id;

        $this->load->view('pracownicy/partial/doreki', $data);
    }


    public function Place($id)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        if (!isset($id) || !is_numeric($id)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }
        //if (!$this->is_admin) {
        //    show_error("Niewystarczające uprawnienia do podglądu", 404, 'Brak uprawnień');
        //}
        $this->load->model('Place_model', 'place_mdl');

        $this->load->helper('url');
        $this->load->helper('form');

        $data['id'] = $id;

        $this->load->view('pracownicy/partial/place', $data);
    }

    public function Podsumowaniejson()
    {
        $this->load->model("Pracownicy_model", "pracownicy");
        $this->pracownicy->raport_plac();
    }
    public function Podsumowanie()
    {
        die("Funkcjonalność przeniesiona do analityki");
        $data = array(
            'title' => 'Lista pracowników',
            'pages_now' => 'lista_pracownikow',
        );


        $this->load->view('partial/header', $data);
        $this->load->view('pracownicy/podsumowanie');
        $this->load->view('partial/footer');
    }

    public function PodsumowaniePartial()
    {
        $this->load->model("Statistic_model");
        $this->load->model("Pracownicy_model", "pracownicy");
        $p = $this->pracownicy->Wszyscy_pracownicy();
        $data = array(

            'p' => $p,
            //  'id' => $id
        );
        $this->load->view('pracownicy/podsumowanie_partial', $data);
    }

    public function Dane($id)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        if (!isset($id) || !is_numeric($id)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }
        // if (!$this->is_admin) {
        //    show_error("Niewystarczające uprawnienia do podglądu", 404, 'Brak uprawnień');
        // }
        $this->load->model("Statistic_model");
        $this->load->model("Pracownicy_model", "pracownicy");
        $p = $this->pracownicy->lista_pracownikow($id);

        if (!isset($id) || !is_numeric($id) || empty($p)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }


        $data = array(
            'title' => 'Lista pracowników',
            'pages_now' => 'lista_pracownikow',
            'p' => $p[0]
        );
        $this->load->view('pracownicy/partial/main', $data);
    }

    public function Podglad($id)
    {
        if (!isset($id) || !is_numeric($id)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }

        $this->load->model("Statistic_model");
        $this->load->model("Pracownicy_model", "pracownicy");


        $p = $this->pracownicy->lista_pracownikow($id);

        if (!isset($id) || !is_numeric($id) || empty($p)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }

        $data = array(
            'title' => 'Lista pracowników',
            'pages_now' => 'lista_pracownikow',
            'p' => $p[0],
            'id' => $id
        );


        $this->load->view('partial/header', $data);
        $this->load->view('pracownicy/podglad');
        $this->load->view('partial/footer');
    }

    public function DoReki($id)
    {
        if (!isset($id) || !is_numeric($id)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }
        $this->load->model("Doreki_model", "d");
        $this->d->DodajDoReki($id);

    }

    public function Potracenie($id)
    {
        if (!isset($id) || !is_numeric($id)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }
        $this->load->model("Potracenia_model", "d");
        $this->d->DodajPotracenie($id);

    }


    public function Dodaj()
    {
        if (!$this->is_admin) {
            show_error("Niewystarczające uprawnienia do dodawania", 404, 'Brak uprawnień');
        }
        $this->load->model("Pracownicy_model", "pracownicy");
        $this->pracownicy->dodaj_pracownika();
        //echo json_encode($_POST);
    }

    public function Edytuj($id,$adres)
    {
        if (!$this->is_admin) {
            show_error("Niewystarczające uprawnienia do dodawania", 404, 'Brak uprawnień');
        }
        if (!isset($id) || !is_numeric($id)) {
            show_error("Nie odnaleziono", 404, 'Wystąpił błąd');
        }
        $this->load->model("Pracownicy_model", "pracownicy");
        $this->pracownicy->dodaj_pracownika("edycja",$id,$adres);

    }

    /*
     * Select2
     */

    public function s2_lista()
    {
        $this->load->model("Pracownicy_model", "pracownicy");
        $this->pracownicy->populate();
    }

}
