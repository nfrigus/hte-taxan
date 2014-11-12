<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// PHPExcel integration class

require_once APPPATH."/vendors/PHPExcel.php";

class Excel extends PHPExcel {



function __construct(){
	parent::__construct();
	// $this->load->helper('excel');
}



function getSheetAsArrsy(&$objPHPExcel,$sheetIndex = false,$cropHeaders = false){
	if(is_numeric($sheetIndex))
		$objPHPExcel->setActiveSheetIndex($sheetIndex);
	$aSheet = $objPHPExcel->getActiveSheet();
	$result = array();
	$ri = $aSheet->getRowIterator();
	foreach($ri as $row){
		$ci = $row->getCellIterator();
		$tmp = array();
		foreach($ci as $cell)
			$tmp[] = $cell->getValue();
		$result[] = $tmp;
	}
	if($cropHeaders) // Crop table headers
		$result = array_slice($result,1);
	return $result;
}

// Load file
function load($inputFileName,$sheetnames,$dataOnly = true){
	// Query filetype
	$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
	$objReader = PHPExcel_IOFactory::createReader($inputFileType);
	$objReader->setReadDataOnly($dataOnly);
	$objReader->setLoadSheetsOnly($sheetnames);
	// @return $objPHPExcel
	return $objReader->load($inputFileName);
}





	
// $worksheetNames = $objReader->listWorksheetNames($inputFileName);var_dump($worksheetNames);
// $worksheetData = $objReader->listWorksheetInfo($inputFileName);var_dump($worksheetData);








// Helper function's
function dateToUnix($EXCEL_DATE){
	$UNIX_DATE = ($EXCEL_DATE - 25569) * 86400;
	return (int)$UNIX_DATE;
}
function gmdate($EXCEL_DATE,$format = 'Y-m-d H:i:s'){
	return gmdate($format,$this->dateToUnix($EXCEL_DATE));
}


}//EOF