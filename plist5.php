<?php
ob_start();
include('plist.php');
ob_end_clean();

/*echo '<pre>';
print_r($objs);
echo '</pre>';//*/

$topObject = $objs[$rootref];

function parse_array($ar, $forcekey=false) {
	global $objs;
	$ar2=array();
	if(max(array_keys($ar))>count($ar) || $forcekey) {
		foreach($ar as $k=>$v) {
			$ar2[$objs[$k]]=$objs[$v];
		}
	}
	else {
		foreach($ar as $k=>$v) {
			$ar2[$k]=$objs[$v];
		}
	}
	foreach($ar2 as $k=>$v) {
		if(is_array($ar2[$k])) $ar2[$k]=parse_array($ar2[$k], false);
	}
	return $ar2;
}

$topObject=parse_array($topObject, true);

echo '<pre>';
var_dump($topObject);
echo '</pre>';//*/

?>