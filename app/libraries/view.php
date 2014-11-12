<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class View {


private $layoutVars = array(
	'charset'=>'utf-8',
	'title'=>'КП "ХТС"',
);
private $vars = array();
private $layout = 'layout';
private $jsFiles = array();
private $cssFiles = array();
private $CI;

public function __construct(){
	$this->CI = & get_instance();
	$this->CI->config->load('jsFiles');
}

public function setLayout($template){
	$this->layout = $template;
}

/**
 *  Загружаем js и css файлы определенной версии плагина
 * @param string $file Название плагина(ключ массива в конфиге)
 * @param string $version Версия плагина
 */
public function registerAssets($file, $version = 'default'){
	$jsInfoArray = $this->CI->config->item($file);
	$returnString = '';
	if (empty($jsInfoArray[$version]))
		return false;
	//Подключаем js
	if (!empty($jsInfoArray[$version]['js']))
		foreach((array)$jsInfoArray[$version]['js'] as $file)
			$returnString .= $this->registerJsFile($file);
	//Подключаем css
	if (!empty($jsInfoArray[$version]['css']))
		foreach((array)$jsInfoArray[$version]['css'] as $file)
			$returnString .= $this->registerCssFile($file);
	return $returnString;
}
// Добавляем js файл в хедер
public function registerJsFile($path){
	$file = '<script type="text/javascript" src="' . $path . '"></script>';
	$this->jsFiles[] = $file;
	return $file;
}
// Добавляем css файл в хедер
public function registerCssFile($path){
	$file = '<link href="' . $path . '" rel="stylesheet" type="text/css" />';
	$this->cssFiles[] = $file;
	return $file;
}
// Устанавливаем значение переменной в шаблон
public function set($varName, $value){
	$this->vars[$varName] = $value;
}
// Устанавливаем массив переменных
public function setByArray($varArray){
	if (is_array($varArray)){
		foreach ($varArray as $key => $value){
			$this->vars[$key] = $value;
		}
	}
}
// Устанавливаем глобальную переменную
public function setGlobal($varName, $value){
	$this->layoutVars[$varName] = $value;
}
// Renders template to $content.
public function render($template,$varArray=array()){
	$this->setByArray($varArray);
	$this->layoutVars['content'] = $this->CI->load->view($template, $this->vars, true);

	if (count($this->jsFiles) > 0)
		$this->layoutVars['globalJS'] = implode("\n\t",$this->jsFiles);
	else
		$this->layoutVars['globalJS'] = "";

	if (count($this->cssFiles) > 0)
		$this->layoutVars['globalCSS'] = join("\n\t",$this->cssFiles);
	else
		$this->layoutVars['globalCSS'] = "";

	return $this->CI->load->view($this->layout, $this->layoutVars, false);
}

}//EOF