<?
$this->load->helper('mb');

// Дефолтные значения
if(empty($itemized))	$itemized = false;	// Подробный список
if(empty($global))		$global = false;	// МН/МГ звонки



$nl = "\r\n";


$month = array(
	1	=> 'Январь',
	2	=> 'Февраль',
	3	=> 'Март',
	4	=> 'Апрель',
	5	=> 'Май',
	6	=> 'Июнь',
	7	=> 'Июль',
	8	=> 'Август',
	9	=> 'Сентябрь',
	10	=> 'Октябрь',
	11	=> 'Ноябрь',
	12	=> 'Декабрь',
);

// Заголовок
$period = strtotime($calls[0]['datetime']);
printf(
	'РАСПЕЧАТКА %1$s ЗВОНКОВ ЗА %2$s г.'.$nl.'        (Велтон.Телеком.Харьков)'
	,(($global)?'МЕЖДУГОРОДНЫХ':'МЕСТНЫХ'),
	mb_strtoupper($month[date('n',$period)]).date(' Y',$period));
echo str_repeat($nl,4);

// Фильтр глобальных/местных звонков
$tmp = array();
for($i=0;isset($calls[$i]);$i++){
	if($global xor $calls[$i]['dest_direction']!=='МЕСТН')continue;
	else $tmp[] = $calls[$i];
}
$calls = $tmp;

$total_sum = 0;
for($i=0;isset($calls[$i]);$i++){
	$first = ($i===0				or $calls[$i-1]['title']!==$calls[$i]['title']);
	$last = (!isset($calls[$i+1])	or $calls[$i+1]['title']!==$calls[$i]['title']);

	if($first){	// Заголовок таблицы
		$sum = 0;
		echo "Абонент № {$calls[$i]['title']}  ({$calls[$i]['description']})".$nl;
		if($itemized)
			echo	mb_str_pad('номер',	13)
				,	mb_str_pad('направление',	31)
				,	mb_str_pad('дата',	13)
				,	mb_str_pad('время',	11)
				,	mb_str_pad('длит',	7)
				,	"сумма"
				,	$nl;
	}
	$sum+= $calls[$i]['cost'];

	if($itemized)
		echo	mb_str_pad('0'.$calls[$i]['dest_number'],	13)
			,	mb_str_pad($calls[$i]['dest_direction'],	31)
			,	mb_str_pad(date('d.m.Y   H:i:s',strtotime($calls[$i]['datetime'])),13+11)
			,	mb_str_pad(pdur($calls[$i]['duration']),	7)
			,	sprintf('%1$1.2f',$calls[$i]['cost'])
			,	$nl;

	if($last) printf('Общая сумма %1$1.2f грн.'.str_repeat($nl,3),$sum);

	$total_sum+= $calls[$i]['cost'];
}
echo str_repeat($nl,2).sprintf('Сумма к оплате по всем вызовам %1$1.2f грн.',$total_sum).$nl;


function pdur($seconds){

  // $hours = floor($seconds / 3600);	$seconds -= $hours * 3600;
  $minutes = floor($seconds / 60);	$seconds -= $minutes * 60;

  $duration = sprintf('%02d:%02d',$minutes,$seconds);
  return $duration;
}
