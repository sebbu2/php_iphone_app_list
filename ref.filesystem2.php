<?php

function my_fopen($data, $mode) {
	$fp=array(
		'data'=>&$data,
		'pos'=>0,
		'size'=>strlen($data),
	);
	return $fp;
}

function my_ftell(&$fp) {
	return $fp['pos'];
}

function my_fseek(&$fp, $pos, $mode=SEEK_SET) {
	switch($mode) {
		case SEEK_SET:
			$fp['pos']=$pos;
			break;
		case SEEK_END:
			$fp['pos']=$fp['size']+$pos;
			break;
		case SEEK_CUR:
			$fp['pos']+=$pos;
			break;
		default:
			die('unknow fseek mode');
			return false;
	}
}

function my_fread(&$fp, $size) {
	$res=substr($fp['data'], $fp['pos'], $size);
	$fp['pos']+=$size;
	return $res;
}

function my_fclose(&$fp) {
	$fp=NULL;
	unset($fp);
}

?>