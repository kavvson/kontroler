<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class EReader extends MY_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->database();
        $this->load->helper('url');




       
    }

    public function test() {
        header('Content-type: text/html; charset=utf-8');
        echo 'Begin ...<br />';
        for ($i = 0; $i < 10; $i++) {
            echo $i . '<br />';
            flush();
            ob_flush();
           
        }
        echo 'End ...<br />';
    }



}
