<?

function mb_str_pad(
$input,
$pad_length,
$pad_string=" ",
$pad_style=STR_PAD_RIGHT,
$encoding="UTF-8")
{
    return str_pad(
	$input,
	strlen($input)-mb_strlen($input,$encoding)+$pad_length,
	$pad_string,
	$pad_style);
}