<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Examples extends MY_Controller {

    public function te() {
       var_dump(strtotime('-3 months'));
       var_dump(date('Y-m-d', strtotime('first day of -3 month')));
      
    }
    public function __construct() {
        parent::__construct();

        $this->load->database();
        $this->load->helper('url');

        $this->load->library('grocery_CRUD');
        $this->output->enable_profiler(true);
    }

    public function _example_output($output = null) {
        $this->load->view('example.php', (array) $output);
    }

    public function offices() {
        $output = $this->grocery_crud->render();

        $this->_example_output($output);
    }

    public function index() {
        $this->_example_output((object) array('output' => '', 'js_files' => array(), 'css_files' => array()));
    }

    public function offices_management() {
        try {
            $crud = new grocery_CRUD();

            $crud->set_theme('datatables');
            $crud->set_table('offices');
            $crud->set_subject('Office');
            $crud->required_fields('city');
            $crud->columns('city', 'country', 'phone', 'addressLine1', 'postalCode');

            $output = $crud->render();

            $this->_example_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function employees_management() {
        $crud = new grocery_CRUD();

        $crud->set_theme('datatables');
        $crud->set_table('employees');
        $crud->set_relation('officeCode', 'offices', 'city');
        $crud->display_as('officeCode', 'Office City');
        $crud->set_subject('Employee');

        $crud->required_fields('lastName');

        $crud->set_field_upload('file_url', 'assets/uploads/files');

        $output = $crud->render();

        $this->_example_output($output);
    }

    public function customers_management() {
        $crud = new grocery_CRUD();
        //$crud->set_theme('f');

        $crud->set_table('przychody');
        //$crud->columns('customerName','contactLastName','phone','city','country','salesRepEmployeeNumber','creditLimit');
        $crud->display_as('id_rejonu', 'Rejon');
        $crud->display_as('fk_kontrahent', 'Kontrahent');

        $crud->set_relation('id_rejonu', 'rejony', 'nazwa');
        $crud->set_relation('fk_kontrahent', 'kontrahenci', 'nazwa');
        $crud->set_relation('dodal', 'users', '{username}');
        // ->display_as('customerName','Name')
        // ->display_as('contactLastName','Last Name');
        $crud->callback_column('numer', array($this, '_callback_webpage_url'));


        $crud->set_subject('przychody');

        $crud->callback_column('netto', array($this, 'valueToEuro'));
        $crud->callback_column('wartosc', array($this, 'valueToEuro'));

        //$crud->set_relation('salesRepEmployeeNumber','employees','lastName');
        $crud->unset_add();
        $crud->unset_delete();
        $output = $crud->render();

        $this->load->view('partial/header');
        $this->_example_output($output);
        $this->load->view('partial/footer');
    }

    public function _callback_webpage_url($value, $row) {

        return "<a href=\"" . site_url() . "Przychody/PodgladFaktury/" . $row->id_przychodu . "\">" . $value . "</a>";
    }

    public function wydatki() {
        $crud = new grocery_CRUD();

        $crud->set_table('wydatki');
        //$crud->columns('customerName','contactLastName','phone','city','country','salesRepEmployeeNumber','creditLimit');
        $crud->display_as('id_rejonu', 'Rejon');
        $crud->display_as('fk_kontrahent', 'Kontrahent');
        $crud->display_as('id_kupujacy', 'Kupujący');

        $crud->set_relation('id_rejonu', 'rejony', 'nazwa');
        $crud->set_relation('kontrahent', 'kontrahenci', 'nazwa');
        $crud->set_relation('dodal', 'users', '{username}');
        $crud->set_relation('id_kupujacy', 'pracownicy', '{imie} {nazwisko}');
        // ->display_as('customerName','Name')
        // ->display_as('contactLastName','Last Name');
        $crud->set_subject('wydatki');

        $crud->callback_column('netto', array($this, 'valueToEuro'));
        $crud->callback_column('wartosc', array($this, 'valueToEuro'));

        //$crud->set_relation('salesRepEmployeeNumber','employees','lastName');
        $crud->unset_add();
        $crud->unset_delete();
        $output = $crud->render();

        $this->load->view('partial/header');
        $this->load->view('wydatki/lista_wydatkow', (array) $output);
        $this->load->view('partial/footer');
    }

    public function products_management() {
        $crud = new grocery_CRUD();

        $crud->set_table('products');
        $crud->set_subject('Product');
        $crud->unset_columns('productDescription');
        $crud->callback_column('buyPrice', array($this, 'valueToEuro'));

        $output = $crud->render();

        $this->_example_output($output);
    }

    public function valueToEuro($value, $row) {
        return $value . ' zł';
    }

    public function film_management() {
        $crud = new grocery_CRUD();

        $crud->set_table('film');
        $crud->set_relation_n_n('actors', 'film_actor', 'actor', 'film_id', 'actor_id', 'fullname', 'priority');
        $crud->set_relation_n_n('category', 'film_category', 'category', 'film_id', 'category_id', 'name');
        $crud->unset_columns('special_features', 'description', 'actors');

        $crud->fields('title', 'description', 'actors', 'category', 'release_year', 'rental_duration', 'rental_rate', 'length', 'replacement_cost', 'rating', 'special_features');

        $output = $crud->render();

        $this->_example_output($output);
    }

    public function film_management_twitter_bootstrap() {
        try {
            $crud = new grocery_CRUD();

            $crud->set_theme('twitter-bootstrap');
            $crud->set_table('film');
            $crud->set_relation_n_n('actors', 'film_actor', 'actor', 'film_id', 'actor_id', 'fullname', 'priority');
            $crud->set_relation_n_n('category', 'film_category', 'category', 'film_id', 'category_id', 'name');
            $crud->unset_columns('special_features', 'description', 'actors');

            $crud->fields('title', 'description', 'actors', 'category', 'release_year', 'rental_duration', 'rental_rate', 'length', 'replacement_cost', 'rating', 'special_features');

            $output = $crud->render();
            $this->_example_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    function multigrids() {
        $this->config->load('grocery_crud');
        $this->config->set_item('grocery_crud_dialog_forms', true);
        $this->config->set_item('grocery_crud_default_per_page', 10);

        $output1 = $this->offices_management2();

        $output2 = $this->employees_management2();

        $output3 = $this->customers_management2();

        $js_files = $output1->js_files + $output2->js_files + $output3->js_files;
        $css_files = $output1->css_files + $output2->css_files + $output3->css_files;
        $output = "<h1>List 1</h1>" . $output1->output . "<h1>List 2</h1>" . $output2->output . "<h1>List 3</h1>" . $output3->output;

        $this->_example_output((object) array(
                    'js_files' => $js_files,
                    'css_files' => $css_files,
                    'output' => $output
        ));
    }

    public function offices_management2() {
        $crud = new grocery_CRUD();
        $crud->set_table('offices');
        $crud->set_subject('Office');

        $crud->set_crud_url_path(site_url(strtolower(__CLASS__ . "/" . __FUNCTION__)), site_url(strtolower(__CLASS__ . "/multigrids")));

        $output = $crud->render();

        if ($crud->getState() != 'list') {
            $this->_example_output($output);
        } else {
            return $output;
        }
    }

    public function employees_management2() {
        $crud = new grocery_CRUD();

        $crud->set_theme('datatables');
        $crud->set_table('employees');
        $crud->set_relation('officeCode', 'offices', 'city');
        $crud->display_as('officeCode', 'Office City');
        $crud->set_subject('Employee');

        $crud->required_fields('lastName');

        $crud->set_field_upload('file_url', 'assets/uploads/files');

        $crud->set_crud_url_path(site_url(strtolower(__CLASS__ . "/" . __FUNCTION__)), site_url(strtolower(__CLASS__ . "/multigrids")));

        $output = $crud->render();

        if ($crud->getState() != 'list') {
            $this->_example_output($output);
        } else {
            return $output;
        }
    }

    public function customers_management2() {
        $crud = new grocery_CRUD();

        $crud->set_table('customers');
        $crud->columns('customerName', 'contactLastName', 'phone', 'city', 'country', 'salesRepEmployeeNumber', 'creditLimit');
        $crud->display_as('salesRepEmployeeNumber', 'from Employeer')
                ->display_as('customerName', 'Name')
                ->display_as('contactLastName', 'Last Name');
        $crud->set_subject('Customer');
        $crud->set_relation('salesRepEmployeeNumber', 'employees', 'lastName');

        $crud->set_crud_url_path(site_url(strtolower(__CLASS__ . "/" . __FUNCTION__)), site_url(strtolower(__CLASS__ . "/multigrids")));

        $output = $crud->render();

        if ($crud->getState() != 'list') {
            $this->_example_output($output);
        } else {
            return $output;
        }
    }

}
