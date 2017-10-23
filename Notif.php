<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notif extends MY_Controller {

	function __construct(){	
        parent::__construct();
		$this->load->library('Postageapp');
    }
	
}