<h1>Формирование отчетов по звонкам в сети КП "ХТС"</h1>

<form action="/main/upload" method="post" enctype="multipart/form-data">

<h2>Загрузка файла с данными</h2>
<p>
	<input style="width:70%;"
		type="file" name="userfile" class="btn"
		title="Указать xls-файл с данными для анализа"
		onchange="document.querySelector('form').submit()"/>
	<a href="/report/parse" target="_blank" class="btn btn-success"><i class="icon-cog"></i> Начать анализ</a>
</p>

</form>

<? if(!empty($files)): ?>
<form method="post" action="/report/result">
<p>
	<label class="radio"><input type="radio" name="global" value="1" checked>МН и МГ</label>
	<label class="radio"><input type="radio" name="global" value="0"        >Местные</label>
	<label class="checkbox"><input type="checkbox" name="details" checked>Подробно</label>
</p>
<table class="table table-hover table-select">
	<caption><h2>Отчеты</h2></caption>
	<tr>
		<th>Файл</th>
		<th>Период</th>
		<th>Звонков</th>
		<th>Стоимость</th>
		<th>Загружен</th>
		<th>Отчеты</th>
	</tr>
<? foreach($files as $file): $clear = $file['cnt_calls_data']==$file['cnt_calls']; ?>
	<tr>
		<td><?=$file['title']?></td>
		<td><?=substr($file['last_call'],0,7);?></td>
		<td style="text-align:center;color:<?=$clear?'#0f0':'#f00'?>;"><?=
			$clear?$file['cnt_calls']:
				$file['cnt_calls_data'].'/'.$file['cnt_calls']?></td>
		<td style="text-align:right;"><?=$file['cost']?></td>
		<td><?=$file['uploaded']?></td>
		<td>
			<input type="checkbox" value="<?=$file['id']?>" name="file[]">
			<a class="confirm" title="Удалить файл" href="/report/delete_file/<?=$file['id']?>"><i class="icon-remove"></i></a>
		</td>
	</tr>
<? endforeach ?>
</table>
<button name="action" class="btn" value="view">Смотреть отчет</button>
<button name="action" class="btn" value="load">Сохранить отчет</button>
</form>
<script>
$('a.confirm').click(function(){
	if(confirm($(this).attr('title')+'?'))return true;
	else return false;
});
$('table tr td:not(:last-child)').click(function(){
	var el = $(this).parent().find('input[name="file[]"]');
	el.prop("checked", !el.prop("checked"));
});
</script>
<style>
input[type=radio],input[type=checkbox]{position:relative;bottom:3px;}
</style>


<? endif ?>