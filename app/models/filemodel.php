<?php
class Filemodel extends CI_Model {

// public $buf = array();



function __construct(){
	parent::__construct();
	$this->load->database();
	$this->db->query('SET character_set_database = utf8;SET NAMES utf8;');
}

function addBillFile($filepath,$filename=false){
	$data = new stdClass();
	$data->hash		= hash_file('md5',$filepath);
	$data->title	= ($filename)?$filename:basename($filepath);
	$data->uploaded	= date('Y-m-d H:i:s');

	$this->db->trans_start();
	$this->db->insert('files',$data);
	$fileid = $this->db->insert_id();
	if(!$fileid)throw new Exception('Этот файл уже был залит.');

	$data	= $this->parse_xls_bill($filepath);
	foreach($data as $k=>$v)
		$data[$k]['fid'] = $fileid;
	$this->db->insert_batch('calls',$data);
	$this->db->trans_complete();
	return $this->db->affected_rows();
}
function bindCalls(&$calls){
	// Функция делает очевидные связи данные-номер
	// и возвращает неясные номера для дальнейшего анализа

	$scrs = $this->get_successors_cnt();

	$new = $multy = $plain = array();
	foreach($calls as $call){
		if(!isset($scrs[$call['source']])){
			// Номера без соответствия в data
			$new[] = $call['source'];
			$plain[] = $call;
		}elseif($scrs[$call['source']]==1){
			// Номера с одним соответствием в data
			$plain[] = $call;
		}else
			$multy[] = $call;
	}

	$output = new stdClass();
	$output->rows_created	= empty($new)	?0:$this->appendNumbers($new);
	$output->rows_binded	= empty($plain)	?0:$this->bindPlainCalls($plain);
	unset($plain,$new);

	$output->unbinded = $multy;
	return $output;
}
function bindCallByExt($callId,$ext){// Привязать данные по расширению
	$call = $this->getCallById($callId);
	$data = array('title' => $ext,'num' => $call->source);
	$query = $this->db
		->select('title,abonent')
		->get_where('data',$data);
	$descr = '';
	switch($query->num_rows()){
	case 1:
		$descr = $query->row()->abonent;
	case 0:
		// Создаем запись, если не существует.
		$this->db->insert('data',$data);
		break;
	default:
		throw new Exception($this->db->last_query());
	}
	return $this->bindSingleCallData($callId,$ext,$descr);
}
function getCallById($cid){// Звонок по id
	return $this->db
		->get_where('calls',array('id'=>$cid))
		->row();
}
private function bindSingleCallData($id,$ext,$descr){// Привязать данные к звонку вручную
	$this->db->insert('calls_data',array(
		'id'			=> $id,
		'title'			=> $ext,
		'description'	=> $descr,)
	);
	return $this->db->affected_rows();
}
private function appendNumberWithExt($num,$ext){// Новый номер с заголовком
	$this->db->insert_batch('data',array('num'=>$num,'title'=>$ext));
	return $this->db->affected_rows();
}
private function appendNumbers($nums){// Новые номера
	if(empty($nums))return false;
	foreach($nums as $num)
		$data[] = array('num'=>$num);
	$this->db->insert_batch('data',$data);
	return $this->db->affected_rows();
}
private function bindPlainCalls(&$calls){// Привязать ясные данные
	$abn = $this->getAbonentsPlain();
	$ids = $data = array();
	foreach($calls as $call){
		$data[] = array(
			'id'			=> $call['id'],
			'title'			=> $abn[$call['source']]['abonent'],
			'description'	=> $abn[$call['source']]['descr'],
		);
		$ids[] = $call['id'];
	}
	$this->db
		->where_in('id',$ids)
		->delete('calls_data');
	$this->db->insert_batch('calls_data',$data);
	return $this->db->affected_rows();
}
function getUnknownCalls(){ // Получить звонки без данных
	$query = $this->db
		->select('c.id,c.datetime,c.source,c.dest_number,c.dest_direction,c.duration')
		->join('calls_data d','c.id = d.id','left')
		->get_where('calls c','d.id IS NULL');
	return $query->result_array();
}
function getCallsByIds(&$ids){ // Звонки по id
	$query = $this->db
		->where_in('c.id',$ids)
		->get('calls c');
	return $query->result_array();
}


// Парсинг файла тарификации
function parse_xls_bill($inputFileName){
	$this->load->library('excel');
	$sheetnames = array('МГ и МН звонки','МС и сервисные услуги');
	$objPHPExcel = $this->excel->load($inputFileName,$sheetnames);	

	$output = array();
	// Городские звонки
	$tmp	= $this->excel->getSheetAsArrsy($objPHPExcel,0,true);
	foreach($tmp as $row)
		$output[] = array(
			'source'			=> $row[0],
			'dest_number'		=> $row[3],
			'dest_direction'	=> $row[4],
			'datetime'			=> gmdate('Y-m-d H:i:s',$this->excel->dateToUnix($row[1]+$row[2])),
			'duration'			=> (int)($row[5]*60*24),
			'cost'				=> $row[6],
		);
	// МГ и МН звонки
	$tmp	= $this->excel->getSheetAsArrsy($objPHPExcel,1,true);
	foreach($tmp as $row)
		$output[] = array(
			'source'			=> $row[0],
			'dest_number'		=> $row[3],
			'dest_direction'	=> $row[7],
			'datetime'			=> gmdate('Y-m-d H:i:s',$this->excel->dateToUnix($row[1]+$row[2])),
			'duration'			=> (int)($row[4]*60*24),
			'cost'				=> $row[8],
		);
	return $output;
}



function getAbonentsPlain(){// Инфа только об одиночных абонентах
	$query = $this->db->get('data');
	$output = array();
	foreach($query->result() as $row)
		$output[$row->num] = array(
			'abonent'	=> $row->title,
			'descr'		=> $row->abonent,
		);
	return $output;
}
// Кол-во внутренних номеров для каждого внешнего
function get_successors_cnt(){
	$query = $this->db
		->select('num,COUNT(id) cnt')
		->group_by('num')
		->get('data');
	$output = array();
	foreach($query->result() as $row)
		$output[$row->num] = (int)$row->cnt;
	return $output;
}
function get_numdata($limit=0,$offset=0,$sort='num',$order='ASC'){
	if(!$limit)
		return $this->db->count_all_results('data');
	$query = $this->db
		->order_by($sort,$order)
		->get('data',$limit,$offset);
	return $query->result_array();
}


function getFileList(){// Список последних файлов
	$query = $this->db->query(<<<SQL
SELECT
  f.id,
  f.title,
  f.uploaded,
  MAX(c.datetime) last_call,
  MIN(c.datetime) first_call,
  COUNT(c.id) cnt_calls,
  COUNT(d.id) cnt_calls_data,
  SUM(c.cost) cost
FROM files f
  LEFT JOIN calls c
    ON c.fid = f.id
  LEFT JOIN calls_data d
    ON c.id = d.id
GROUP BY f.id
ORDER BY f.uploaded DESC
SQL
	);
	return $query->result_array();
}
function getFiledata($fid){// Все звонки по файлу
	return $this->db
		->join('calls_data d','c.id = d.id')
		->where_in('c.fid',(array)$fid)
		->order_by('d.title ASC,c.datetime ASC')
		->get('calls c')->result_array();
}

// Ассоциативный массив из tsv
private function get_data($file){
	$data = file(APPPATH."db/$file.txt",FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
	$output = array();
	foreach($data as $row){
		$row = explode("\t",$row);
		$output[$row[0]] = $row[1];
	}
	return $output;
}


}//EOF