<!DOCTYPE html>
<html>
<head>
	<meta charset="<?=$charset?>">
	<title><?=$title?></title>
	<link rel="icon" type="image/x-icon" href="/public/favicon.ico">
	<?=$globalCSS."\n"?>
	<?=$globalJS."\n"?>
</head>
<body>
<div id="container" class="row">
	<div class="span3 side">
		<ul class="nav nav-list">
			<li class="nav-header">Навигация</li>
			<li><a href="/">Главная (последние звонки)</a></li>
			<li><a href="/report">Создание/просмотр отчетов</a></li>
			<li><a href="/report/data">Управление данными</a></li>
		</ul>
	</div>
	<div class="page span7"><?=$content?></div>
	<!--div class="span3 side"><?=@$rsidebar?></div-->
</div>
</body>
</html>