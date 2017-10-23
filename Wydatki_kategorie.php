<?php

class Wydatki_kategorie extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Wydatki_kategorie_model');
        if (!$this->is_admin) {
            show_error("Niewystarczające uprawnienia do zarządzania", 404, 'Brak uprawnień');
        }
    }

    /*
     * Listing of wydatki_kategorie
     */

    function index() {

        $data = array(
            'title' => 'Lista kategorii wydatku',
            'wydatki_kategorie' => $this->Wydatki_kategorie_model->get_all_wydatki_kategorie()
        );
        $this->load->view('partial/header', $data);
        $this->load->view('wydatki_kategorie/index', $data);
        $this->load->view('partial/footer');
    }

    /*
     * Adding a new wydatki_kategorie
     */

    function add() {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('nazwa', 'Nazwa', 'required|max_length[50]|min_length[3]');
        $data = array(
            'title' => 'Dodawanie kategorii wydatku',
        );
        if ($this->form_validation->run()) {
            $params = array(
                'nazwa' => $this->input->post('nazwa'),
            );

            $wydatki_kategorie_id = $this->Wydatki_kategorie_model->add_wydatki_kategorie($params);
            redirect('wydatki_kategorie/index');
        } else {
            $this->load->view('partial/header', $data);
            $this->load->view('wydatki_kategorie/add', $data);
            $this->load->view('partial/footer');
        }
    }

    /*
     * Editing a wydatki_kategorie
     */

    function edit($id_kat) {
        // check if the wydatki_kategorie exists before trying to edit it

        $data = array(
            'title' => 'Edycja kategorii wydatku',
            'wydatki_kategorie' => $this->Wydatki_kategorie_model->get_wydatki_kategorie($id_kat)
        );
        if (isset($data['wydatki_kategorie']['id_kat'])) {
            $this->load->library('form_validation');

            $this->form_validation->set_rules('nazwa', 'Nazwa', 'required|max_length[50]|min_length[3]');

            if ($this->form_validation->run()) {
                $params = array(
                    'nazwa' => $this->input->post('nazwa'),
                );

                $this->Wydatki_kategorie_model->update_wydatki_kategorie($id_kat, $params);
                redirect('wydatki_kategorie/index');
            } else {
                $data['_view'] = '';
                $this->load->view('partial/header', $data);
                $this->load->view('wydatki_kategorie/edit', $data);
                $this->load->view('partial/footer');
            }
        } else
            show_error('Błąd.');
    }

}
