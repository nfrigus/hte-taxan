<?php if(!defined('BASEPATH'))exit('No direct script access allowed');

class Test extends CI_Controller {

function  __construct(){
	parent::__construct();
	$this->load->library('view');
	$this->view->registerCssFile('/public/core.css');
	$this->view->registerJsFile('/public/common.js');
	$this->view->registerAssets('jquery');
	header('Content-type: text/html; charset=utf-8');
	header('Cache-Control: max-age=0');
}

function index(){
	$this->load->model('filemodel');
	$this->load->model('smdrmodel');
	$d = $this->db->query(<<<SQL
SELECT * FROM calls c
LEFT JOIN calls_data d ON c.id=d.id AND d.id=NULL
WHERE c.dest_direction='МЕСТН'
AND c.source = '577588400'
ORDER BY c.datetime
LIMIT 2500,10;
SQL
	)->result_array();

	set_time_limit(0);
	var_dump(count($d));
	$bnd = 0;
	foreach($d as $t){
		$ext = $this->smdrmodel->findExtention($t['dest_number'],$t['datetime'],$t['duration'],($t['dest_direction']==='МЕСТН'));
		// $bnd = $this->filemodel->bindCallByExt($t['id'],$ext);
		var_dump($ext,$bnd,$t);flush();
	}
	// header('Content-type: text/plain; charset=utf-8');
	// $this->load->view('calls_print2',$pageData);
}




// Проверка соединения с БД
function db($db='default'){
	$tmp = $this->load->database($db);
	var_dump($this->db);}
// dump POST
function dump(){
	var_dump($_POST);}
// get rand()
function rand(){
	echo rand();}
// error 404
function _404(){
	show_404('test');}
// Проверка доступных PDO
function pdo(){
	print_r(PDO::getAvailableDrivers());}
function php(){
	phpinfo();}




}//EOF