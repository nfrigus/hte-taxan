<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends CI_Controller {

function  __construct(){
	parent::__construct();
	$this->load->library('view');
	$this->view->registerAssets('jquery','1.10.2');
	$this->view->registerAssets('ui');
	$this->view->registerAssets('bootstrap');
	$this->view->registerAssets('jqGrid');
	$this->view->registerCssFile('/public/core.css');
	$this->view->registerJsFile('/public/common.js');
}

}
