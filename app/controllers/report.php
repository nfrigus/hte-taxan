<?php if(!defined('BASEPATH'))exit('No direct script access allowed');

class Report extends CI_Controller {


function __construct(){
	parent::__construct();
	$this->load->library('view');
	$this->load->model('filemodel');
	$this->view->registerAssets('jquery','1.10.2');
	$this->view->registerAssets('bootstrap');
	$this->view->registerAssets('ui');
	$this->view->registerAssets('jqGrid');
	$this->view->registerCssFile('/public/core.css');
	$this->view->registerJsFile('/public/common.js');
}

function index(){
	$data['files'] = $this->filemodel->getFileList();
	$this->view->render('report',$data);
}

function data(){
	$this->view->render('abntsdata');
}
function delete_file($fid){
	$this->db->delete('files',array('id'=>$fid));
	header('Location: /report');
}
function result(){
	$fileIds = $_POST['file'];
	$pageData['calls']  = $this->filemodel->getFileData($fileIds);
	if(empty($pageData['calls']))die('Нет данных, соответствующих запросу.');
	$pageData['global'] = (bool)$this->input->post('global');
	$action = $this->input->post('action');
/* 	if(!$this->input->post('foreign'))
		foreach($pageData['calls'] as $k=>$v)
			if($v['dest_direction']!=='МЕСТН')
				unset($pageData['calls'][$k]);
	if(!$this->input->post('local'))
		foreach($pageData['calls'] as $k=>$v)
			if($v['dest_direction']==='МЕСТН')
				unset($pageData['calls'][$k]); */
	// $pageData['calls'] = array_values($pageData['calls']);
	$pageData['itemized'] = (bool)$this->input->post('details');
	$content = $action=='load'?'attachment; ':'';
	$content.= 'filename="result.txt"';
	header('Content-disposition: '.$content);
	header('Cache-Control: max-age=0');
	if($action==='load'){
		header('Content-type: text/plain; charset=windows-1251');
		$tmp = $this->load->view('calls_print',$pageData,true);
		echo iconv('utf-8','windows-1251',$tmp);
	}else{
		header('Content-type: text/plain; charset=utf-8');
		$this->load->view('calls_print',$pageData);
	}
}

function parse(){
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
	header('Content-type: text/plain; charset=utf-8');
	echo date('Y-m-d H:i:s')." Инициализация...\n\n";
	$this->load->model('filemodel');
	$this->load->model('smdrmodel');
	$calls	= $this->filemodel->getUnknownCalls();
	echo 'Найдено номеров без данных: '.count($calls)."\n\n";
	$calls	= $this->filemodel->bindCalls($calls);

	if($tmp = $calls->rows_created)
	echo "Новых номеров: {$tmp}\n";
	if($tmp = $calls->rows_binded)
	echo "Распознаны: {$tmp}\n";
	if($tmp = count($calls->unbinded))
	echo "Осталось: {$tmp}\n";
	else die("\n\nАнализ завершен.");

	flush();
	set_time_limit(0);

	foreach($calls->unbinded as &$v){
		echo date('H:i:s')," $v[source]>$v[dest_number] $v[datetime] $v[duration]... ";
		$isLocal = $v['dest_direction']==='МЕСТН';
		$ext = $this->smdrmodel->findExtention($v['dest_number'],$v['datetime'],$v['duration'],$isLocal);
		$this->filemodel->bindCallByExt($v['id'],$ext);
		echo json_encode($ext)."\n";
		flush();
	}
	die('\nАнализ завершен. '.date('Y-m-d H:i:s'));
}


// ajax
function addrow(){
	($tmp = $this->input->post('num'))     and $data['num']     = $tmp;
	($tmp = $this->input->post('title'))   and $data['title']   = $tmp;
	($tmp = $this->input->post('abonent')) and $data['abonent'] = $tmp;
	$this->db->insert('data',$data);
	header('Location: /report/data');
}
function delrow(){
	$this->load->model('filemodel');
	$data['id'] = $this->input->post('id') or die;
	$this->db->delete('data',$data);
}


// jqgrid
function jqgrid(){
	$this->load->model('filemodel');

	$sidx = $this->input->post('sidx');
	$sord = $this->input->post('sord');
	$ppag = $this->input->post('rows');
	$page = $this->input->post('page');

	$data = new stdClass();
	$data->page		= $page;
	$data->records	= $this->filemodel->get_numdata();
	$data->total	= ceil($data->records/$ppag);

	$rows = $this->filemodel->get_numdata($ppag,($page-1)*$ppag,$sidx,$sord);

	for($i=0;$row = array_shift($rows);$i++){
	    $data->rows[$i]['id'] = $row['id'];
	    $data->rows[$i]['cell'] = array(
			$row['num'],
			$row['title'],
			$row['abonent'],
		);
	}
	header("Content-type: application/json; charset=utf-8");
	echo json_encode($data);
}
function editdata(){
	if('edit' != $this->input->post('oper'))
		die('Неизвестная операция');
	$id = (int)$this->input->post('id');
	if($tmp = $this->input->post('title'))
		$data['title'] = $tmp;
	if(($tmp = $this->input->post('abonent'))!==false)
		$data['abonent'] = $tmp;

	$this->db->update('data',$data,array('id'=>$id));
}

function exportAbnData($type){
	$data['data'] = $this->db->get('data')->result_array();
	switch($type){
		case 'csv':
			header('Content-disposition: attachment; filename="abntsdata.csv"');
			header('Content-type: text/plain; charset=windows-1251');
			echo iconv('utf-8','windows-1251',$this->load->view('csv',$data,true));
			break;
		case 'tsv':
			header('Content-type: text/plain; charset=utf-8');
			$data['delimiter'] = "\t";
			$data['enclosure'] = '';
			$this->load->view('csv',$data);
			break;
	}
}


function array2csv($array,$delimiter=";",$enclosure='"'){
	// output up to 5MB is kept in memory, if it becomes bigger it will automatically be written to a temporary file
	$csv = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');

	foreach($array as $row)
		fputcsv($csv, $row, $delimiter, $enclosure);

	rewind($csv);

	return stream_get_contents($csv);
}



}//EOF