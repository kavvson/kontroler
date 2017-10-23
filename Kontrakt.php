<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * if(!$this->is_admin)
        {
            show_error("Niewystarczające uprawnienia do zarządzania", 404, 'Brak uprawnień');
        }
 */

/**
 * Description of Rejon
 *
 * @author Kavvson
 */
class Kontrakt extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Kontrakt_model');
        //$this->config->set_item('language', 'polish');
        $this->load->config("pagination");
        $this->load->library("pagination");
        
    }

    public function lista_kontraktow() {

        $this->Kontrakt_model->populate();
    }

    /*
     * Listing of kontrakty
     */

    function index() {
        if(!$this->is_admin)
        {
            show_error("Niewystarczające uprawnienia do przeglądania kontraktów", 404, 'Brak uprawnień');
        }
        $params['limit'] = 25;
        $params['offset'] = ($this->input->get('per_page')) ? $this->input->get('per_page') : 0;

        $config = $this->config->item('pagination');
        $config['base_url'] = site_url('kontrakt/index?');
        $config['total_rows'] = $this->Kontrakt_model->get_all_kontrakty_count();
        $this->pagination->initialize($config);
        $data['title'] = "Przegląd kontraktów";
        $data['kontrakty'] = $this->Kontrakt_model->get_all_kontrakty($params);

        $this->load->view('partial/header', $data);
        $this->load->view('kontrakty/index', $data);
        $this->load->view('partial/footer');
    }

    /*
     * Adding a new kontrakty
     */

    function add() {
        if(!$this->is_admin)
        {
            show_error("Niewystarczające uprawnienia do przeglądania kontraktów", 404, 'Brak uprawnień');
        }
        $this->load->library('form_validation');

        $this->form_validation->set_rules('nazwa', 'Nazwa', 'max_length[250]|required|min_length[3]');
        $this->form_validation->set_rules('zakonczony','Zakonczony','required');
        $this->form_validation->set_rules('inputKontrahent', 'Kontrahent', 'required|alpha_numeric');
        $data['title'] = "Dodawanie kontraktów";
        if ($this->form_validation->run()) {
            $params = array(
                'nazwa' => $this->input->post('nazwa'),
                'zakonczony' => $this->input->post('zakonczony'),
                'kontrahent' => $this->input->post('inputKontrahent')
            );

            $kontrakty_id = $this->Kontrakt_model->add_kontrakty($params);
            redirect('kontrakt/index');
        } else {

            $this->load->view('partial/header', $data);
            $this->load->view('kontrakty/add', $data);
            $this->load->view('partial/footer');
        }
    }

    /*
     * Editing a kontrakty
     */

    function edit($id_kontraktu) {
        if(!$this->is_admin)
        {
            show_error("Niewystarczające uprawnienia do przeglądania kontraktów", 404, 'Brak uprawnień');
        }
        // check if the kontrakty exists before trying to edit it
        $data['kontrakty'] = $this->Kontrakt_model->get_kontrakty($id_kontraktu);
        $data['title'] = "Edycja kontraktów";
        if (isset($data['kontrakty']['id_kontraktu'])) {
            $this->load->library('form_validation');

            $this->form_validation->set_rules('nazwa', 'Nazwa', 'max_length[250]|required|min_length[3]');
            $this->form_validation->set_rules('inputKontrahent', 'Kontrahent', 'required|alpha_numeric');

            if ($this->form_validation->run()) {
                $params = array(
                    'nazwa' => $this->input->post('nazwa'),
                    'zakonczony' => $this->input->post('zakonczony'),
                    'kontrahent' => $this->input->post('inputKontrahent')
                );

                $this->Kontrakt_model->update_kontrakty($id_kontraktu, $params);
                redirect('kontrakt/index');
            } else {
                $this->load->view('partial/header', $data);
                $this->load->view('kontrakty/edit', $data);
                $this->load->view('partial/footer');
            }
        } else
            show_error('The kontrakty you are trying to edit does not exist.');
    }

}
