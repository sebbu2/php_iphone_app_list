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
	return $ar2;
}

$topObject=parse_array($topObject, true);

echo '<pre>';
var_dump($topObject);
echo '</pre>';//*/

?>