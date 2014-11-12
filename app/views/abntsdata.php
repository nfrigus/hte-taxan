<?
$table_id = 'jqgrid';

$pager = $table_id.'p'
?>
<table id="<?=$table_id?>"></table>
<div id="<?=$pager?>"></div>

<br>
<form class="form-inline" method="post" action="/report/addrow" onsubmit="return addRow();">
	<input type="text" name="num" class="input-medium" placeholder="индекс">
	<input type="text" name="title" class="input-mini" placeholder="номер">
	<input type="text" name="abonent" class="input-large" placeholder="описание">
	<button class="btn"><i class="icon-plus-sign"></i> добавить</button>
	<a class="btn" onclick="delSelecedRow();"><i class="icon-remove"></i></a>
</form>
Все данные: <a href="/report/exportAbnData/csv">CSV</a>, <a href="/report/exportAbnData/tsv">Текст</a>


<script>
var $grid = $("#<?=$table_id?>");

$grid.jqGrid({
	"url" : "\/report\/jqgrid",
	"datatype" : "json",
	"mtype" : "POST",
	"caption" : "\u0421\u043f\u0438\u0441\u043e\u043a \u043d\u043e\u043c\u0435\u0440\u043e\u0432 \u0438 \u043f\u043e\u043b\u044c\u0437\u043e\u0432\u0430\u0442\u0435\u043b\u0435\u0439",
	"colNames" : ["\u0418\u043d\u0434\u0435\u043a\u0441", "\u041d\u043e\u043c\u0435\u0440", "\u041e\u043f\u0438\u0441\u0430\u043d\u0438\u0435"],
	"colModel" : [{
			"name" : "num",
			"index" : "num",
			"align" : "center",
			"width" : 2,
			"sortable" : true
		}, {
			"name" : "title",
			"index" : "title",
			"align" : "center",
			"width" : 1,
			"editable" : true,
			"editoptions" : {"maxlength": 255},
			"editrules" : {"required": true},
			"sortable" : true
		}, {
			"name" : "abonent",
			"index" : "abonent",
			"width" : 7,
			"editable" : true,
			"editoptions" : {"maxlength": 255},
			"sortable" : true
		}
	],
	"rownumbers" : false,
	"rownumWidth" : 40,
	"sortname" : "title",
	"sortorder" : "ASC",
	"height" : 600,
	"autowidth" : true,
	"rowNum" : 25,
	"rowList" : [25, 75, 999999],
	"pager" : "<?=$pager?>",
	"cellEdit" : true,
	"cellsubmit" : "remote",
	"cellurl" : "/report\/editdata",
	afterSubmitCell:function(serverresponse, rowid, cellname, value, iRow, iCol){
		if(serverresponse.responseText != '')alert(serverresponse.responseText)},
	// onSelectRow: function(id){alert(+id);},
});

function delSelecedRow(){
	if(!confirm('Удалить выделенную строку?'))return false;
	var selectedRowId =  $grid.jqGrid ('getGridParam', 'selrow');
	$.post('/report/delrow',{id:+selectedRowId});
	$grid.trigger('reloadGrid');
}
function addRow(){
	var form = $('form');
	if(!confirm('Будет создана новая запись. Продолжить?'))return false;
	$.post('/report/addrow',form.serialize(),function(){
		$grid.trigger('reloadGrid');
	});
	return false;
}
</script>