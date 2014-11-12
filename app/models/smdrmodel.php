<?php
class Smdrmodel extends CI_Model {

// public $buf = array();

public $kdb;// База данных коменданта

function __construct(){
	parent::__construct();
	$this->kdb = $this->load->database('smdr',true);
	// $this->db->query('SET character_set_database = utf8;SET NAMES utf8;');
}


function getLastCalls($limit=5){
	return $this->kdb->query(<<<SQL
SELECT
	datetime_comp	time,
	extention		ext,
	IF(
	  code_raw_in='',
	  IF(
	    code_raw_out='',
		'=','>'
	  ),'<')		type,
	CONCAT(code_raw_in,code_raw_out,ext_in) num,
	duration	dur
FROM arch_smdr
ORDER BY pkey DESC
LIMIT $limit;
SQL
	)->result_array();
}
function findExtention($num,$time,$dur=0,$isLocal=false){
	$dtime	= 300;	// Разброс по времени,с
	if($isLocal)$num=substr($num,2);
	else $num='0'.$num;
	$s    = sprintf('%-11s',$num);
	$time = strtotime($time);
	$ft   = date('Y-m-d H:i:s',$time-$dtime);
	$tt   = date('Y-m-d H:i:s',$time+$dtime);
	$q = $this->kdb
		->select('datetime_comp,datetime_call,duration,code_raw_out,extention')
		->where('code_raw_out',$s)
		->where('datetime_call >',$ft)
		->where('datetime_call <',$tt)
		->get('arch_smdr');
	// echo $this->kdb->last_query();
	switch($q->num_rows()){
	case 0:return null;
	case 1:return $q->row()->extention;
	}
	$q = $q->result_array();
	foreach($q as &$v){
		$cmp_t1     = strtotime($v['datetime_comp']);
		$cmp_t2     = strtotime($v['datetime_call']);
		$cmp_dur    = $v['duration'];
		$v['dvrst'] = (abs($time-$cmp_t1)+abs($time-$cmp_t2))/$dtime+abs($cmp_dur-$dur)/$dur;
	}
	$output = $q[0];
	foreach($q as &$v)
		if($v['dvrst']<$output['dvrst'])
			$output = $v;
	return $output['extention'];
}

function test(){}


}//EOF