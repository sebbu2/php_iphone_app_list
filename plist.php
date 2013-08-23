<?php
if(array_key_exists('mode',$_REQUEST)) $mode=$_REQUEST['mode'];

if(!isset($mode)) $mode='f';

if($mode=='f') {
	if(array_key_exists('file',$_REQUEST)) $file=$_REQUEST['file'];
	if(!isset($file)) $file='Info.plist';
	require('ref.filesystem.php');
	$fp=my_fopen($file,'rb');
}
else if($mode=='d') {
	if(array_key_exists('data',$_REQUEST)) $data=$_REQUEST['data'];
	if(!isset($data)) $data=file_get_contents($file);
	require('ref.filesystem2.php');
	$fp=my_fopen($data,'rb');
}

require('plist2.php');
?>