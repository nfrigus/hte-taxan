<?
$this->load->helper('mb');

// Сортировка
ksort($abonents);

// Определение периода отчета
foreach($abonents as $a){
	if(isset($a['calls'][0]['time'])){
		$day = $a['calls'][0]['time'];
		break;
	}
}

// Дефолтные значения
if(empty($itemized))	$itemized = false;	// Подробный список
if(empty($global))		$global = false;	// МН/МГ звонки


define('NEWLINE',"\r\n");

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


printf(
	'РАСПЕЧАТКА %1$s ЗВОНКОВ ЗА %2$s г.'.NEWLINE.'        (Велтон.Телеком.Харьков)',
	(($global)?'МЕЖДУГОРОДНЫХ':'МЕСТНЫХ'),
	mb_strtoupper($month[gmdate('n',$day)]).gmdate(' Y',$day));
echo str_repeat(NEWLINE,4);
$total_sum = 0;
foreach($abonents as $abon_id => $abon){
	// Заголовок таблицы
	echo "Абонент № $abon_id  ($abon[descr])".NEWLINE;
	if($itemized)
	echo	mb_str_pad('номер',	13)
		,	mb_str_pad('направление',	31)
		,	mb_str_pad('дата',	13)
		,	mb_str_pad('время',	11)
		,	mb_str_pad('длит',	7)
		,	"сумма"
		,	NEWLINE;

	$sum = 0;
	foreach($abon['calls'] as $call){
		$sum+= $call['cost'];
		if($itemized)
		echo	mb_str_pad('0'.$call['num'],	13)
			,	mb_str_pad($call['dest'],	31)
			,	mb_str_pad(gmdate('d.m.Y   H:i:s',$call['time']),	13+11)
			,	mb_str_pad(gmdate('H:i',$call['dur']),	7)
			,	sprintf('%1$1.2f',$call['cost'])
			,	NEWLINE;
	}
	printf('Общая сумма %1$1.2f грн.'.str_repeat(NEWLINE,3),$sum);
	$total_sum+= $sum;
}
echo str_repeat(NEWLINE,2)."Сумма к оплате по всем вызовам $total_sum грн.".NEWLINE;



