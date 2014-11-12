<?

function fgettsv($filepath){
	$data = file($filepath,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
	$output = array();
	foreach($data as $row){
		$[] = explode("\t",$row);
	}
	return $output;
}