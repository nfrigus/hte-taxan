<?php defined('BASEPATH') or die('No direct script access allowed');

class Main extends CI_Controller {

function  __construct(){
	parent::__construct();
	$this->load->library('view');
	$this->view->registerAssets('jquery','1.10.2');
	$this->view->registerAssets('bootstrap');
	$this->view->registerAssets('ui');
	$this->view->registerAssets('jqGrid');
	$this->view->registerCssFile('/public/core.css');
	$this->view->registerJsFile('/public/common.js');
}

function index(){
	$this->load->model('smdrmodel');
	$d['call_list']=$this->smdrmodel->getLastCalls(100);

	$this->view->render('calls_list',$d);
}



function upload(){
	header('Content-type: text/plain; utf-8');
	if(count($_FILES)===0)die('No file');
	if($_FILES['userfile']['type'] !== 'application/vnd.ms-excel')
		die(<<<HTML
<h1>wrong file</h1>
expected: application/vnd.ms-excel<br>
got: {$_FILES['userfile']['type']}
HTML
);
// var_dump($_FILES);die;
try{
	$this->load->model('filemodel');
	$this->filemodel->addBillFile($_FILES['userfile']['tmp_name'],$_FILES['userfile']['name']);
}catch(Exception $e){die($e->getMessage());}
	header('location: /report');
}

function old_upload(){
	$config['upload_path'] = 'uploads/';
	$config['allowed_types'] = 'xls|xlsx';
	$config['max_size'] = 1024;//KB
	$this->load->library('upload', $config);

	$this->upload->do_upload() or die('<h1>error</h1>'.$this->upload->display_errors());
	$_SESSION['upload_data'] = $this->upload->data();
	header('Location: /report');
}



}//EOF