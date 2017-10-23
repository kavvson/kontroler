<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Rejon
 *
 * @author Kavvson
 */
class Rejon extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Rejon_model');
        $this->config->set_item('language', 'polish');
       
    }

    public function lista_rejonow() {

        $this->Rejon_model->populate();
    }

    /*
     * Listing of rejony
     */

    function index() {
         if(!$this->is_admin)
        {
            show_error("Niewystarczające uprawnienia do zarządzania", 404, 'Brak uprawnień');
        }
        $data['rejony'] = $this->Rejon_model->get_all_rejony();
        $data['title'] = "Przegląd rejonów";
        $this->load->view('partial/header', $data);
        $this->load->view('rejony/index', $data);
        $this->load->view('partial/footer');
    }

    /*
     * Adding a new rejony
     */

    function add() {
         if(!$this->is_admin)
        {
            show_error("Niewystarczające uprawnienia do zarządzania", 404, 'Brak uprawnień');
        }
        $this->load->library('form_validation');
        $data['title'] = "Dodawanie rejonu";
        $this->form_validation->set_rules('nazwa', 'Nazwa', 'required|min_length[3]|max_length[100]');

        if ($this->form_validation->run()) {
            $params = array(
                'nazwa' => $this->input->post('nazwa'),
            );

            $rejony_id = $this->Rejon_model->add_rejony($params);
            redirect('rejon/index');
        } else {
            $this->load->view('partial/header', $data);
            $this->load->view('rejony/add', $data);
            $this->load->view('partial/footer');
        }
    }

    /*
     * Editing a rejony
     */

    function edit($id_rejonu) {
         if(!$this->is_admin)
        {
            show_error("Niewystarczające uprawnienia do zarządzania", 404, 'Brak uprawnień');
        }
        // check if the rejony exists before trying to edit it
        $data['rejony'] = $this->Rejon_model->get_rejony($id_rejonu);
        $data['title'] = "Edycja rejonu";
        if (isset($data['rejony']['id_rejonu'])) {
            $this->load->library('form_validation');

            $this->form_validation->set_rules('nazwa', 'Nazwa', 'required|min_length[3]|max_length[100]');

            if ($this->form_validation->run()) {
                $params = array(
                    'nazwa' => $this->input->post('nazwa'),
                );

                $this->Rejon_model->update_rejony($id_rejonu, $params);
                redirect('rejon/index');
            } else {
                $this->load->view('partial/header', $data);
                $this->load->view('rejony/edit', $data);
                $this->load->view('partial/footer');
            }
        } else
            show_error('Błąd.');
    }

}
