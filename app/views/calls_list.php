<h1>Последние зафиксированные звонки</h1>

<table class="table">
<tr>
	<th>Время</th>
	<th>Соединение</th>
	<th>Продолжительность</th>
</tr>
<?php if($call_list) foreach($call_list as $call){?>
<tr>
	<td><?=$call['time'];?></td>
	<td><?=implode(' ',array(
		$call['ext'],
		str_replace(
			array('=','>','<'),
			array('↔','→','←'),
			$call['type']),
		$call['num']));?></td>
	<td><?=dur($call['dur']);?></td>
</tr>
<?php };?>
</table>


<?

function dur($durationInSeconds) {
	$hours = floor($durationInSeconds / 3600);
	$durationInSeconds -= $hours * 3600;
	$minutes = floor($durationInSeconds / 60);
	$seconds = $durationInSeconds - $minutes * 60;

	return sprintf('%01d:%02d:%02d',$hours,$minutes,$seconds);
}