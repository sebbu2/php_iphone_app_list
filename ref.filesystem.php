<?php

function my_fopen($data, $mode) {
	return fopen($data, $mode);
}

function my_ftell(&$fp) {
	return ftell($fp);
}

function my_fseek(&$fp, $pos, $mode=SEEK_SET) {
	return fseek($fp, $pos, $mode);
}

function my_fread(&$fp, $size) {
	return fread($fp, $size);
}

function my_fclose(&$fp) {
	return fclose($fp);
}

?>