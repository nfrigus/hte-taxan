<html>
<head>
<title>Форма загрузки</title>
</head>
<body>

<form action="/upload/putxls" method="post" accept-charset="utf-8" enctype="multipart/form-data">

<input type="file" name="userfile" size="20" />
<input type="submit" value="upload" />

<? if(isset($upload_data)): ?>
<h3>Файл успешно загружен!</h3>

<ul>
<? foreach($upload_data as $item => $value):?>
<li><?=$item;?>: <?=$value;?></li>
<? endforeach; ?>
</ul>

<? endif; ?>
</form>

</body>
</html>