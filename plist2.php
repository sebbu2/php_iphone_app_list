<?php
//header
$data=my_fread($fp,6);
assert($data=='bplist');
$data=my_fread($fp,2);
$version=ord($data[1]);
assert($data[0]=='0');
assert($data[1]>='0' && $data[1]<='9');

$pos=my_ftell($fp);
my_fseek($fp, -32, SEEK_END);
$size=my_ftell($fp)+32;

//trailer
$data=my_fread($fp,6);
assert($data=="\x00\x00\x00\x00\x00\x00");
//offsetIntSize
$nb_bytes_offset=ord(my_fread($fp,1));
assert($nb_bytes_offset>=1 && $nb_bytes_offset<=4);
//objectRefSize
$nb_bytes_objref=ord(my_fread($fp,1));
assert($nb_bytes_objref>=1 && $nb_bytes_objref<=2);
$data=my_fread($fp,4);
assert($data=="\x00\x00\x00\x00");
//numObjects
$data=my_fread($fp,4);
$data=unpack('N',$data);
$nb_obj=$data[1];
$data=my_fread($fp,4);
assert($data=="\x00\x00\x00\x00");
//topObject
$data=my_fread($fp,4);
$data=unpack('N',$data);
$rootref=$data[1];
$data=my_fread($fp,4);
assert($data=="\x00\x00\x00\x00");
//offsetTableOffset
$data=my_fread($fp,4);
$data=unpack('N',$data);
$offstart=$data[1];

assert($nb_obj >= 1);
assert($nb_obj > $rootref);
assert($offstart >= 9);
assert($size-32 > $offstart);

assert($offstart+($nb_obj*$nb_bytes_offset)+32 == $size);
assert($nb_bytes_objref>8 || (1<<(8*$nb_bytes_objref))>$nb_obj);
assert($nb_bytes_offset>8 || (1<<(8*$nb_bytes_offset))>$offstart);

echo 'offsetIntSize='.$nb_bytes_offset;
echo '<br/>'."\r\n";
echo 'objectRefSize='.$nb_bytes_objref;
echo '<br/>'."\r\n";
echo 'numObjects='.$nb_obj;
echo '<br/>'."\r\n";
echo 'topObject='.$rootref;
echo '<br/>'."\r\n";
echo 'offsetTableOffset='.$offstart;
echo '<br/>'."\r\n";

echo '<br/>'."\r\n";

function read_nb(&$fp, $nbsize=-1) {
	$nb=-1;
	switch($nbsize) {
		case 1:
			$data=my_fread($fp,1);
			$nb=ord($data);
			break;
		case 2:
			$data=my_fread($fp,2);
			$nb=ord($data[0])<<8|(ord($data[1]));
			break;
		case 3:
			$data=my_fread($fp,3);
			$nb=(ord($data[0])<<16)|(ord($data[1])<<8)|(ord($data[2]));
			break;
		case 4:
			$data=my_fread($fp,4);
			$nb=(ord($data[0])<<24)|(ord($data[1])<<16)|(ord($data[2])<<8)|(ord($data[3]));
			break;
		case 8:
			$data=my_fread($fp,8);
			$nb=(ord($data[0])<<56)|(ord($data[1])<<48)|(ord($data[2])<<40)|(ord($data[3])<<32)|(ord($data[4])<<24)|(ord($data[5])<<16)|(ord($data[6])<<8)|(ord($data[7]));
			break;
		default:
			die('error on line '.__LINE__.' : nbsize='.$nbsize);
	}
	return $nb;
}

//check ref
my_fseek($fp, $offstart, SEEK_SET);
for($i=0; $i<$nb_obj; ++$i) {
	$nb=read_nb($fp, $nb_bytes_offset);
	assert($offstart >= $nb);
}

my_fseek($fp, $offstart+($rootref*$nb_bytes_offset), SEEK_SET);
$offset=read_nb($fp, $nb_bytes_offset);
echo 'Offset='.$offset;
echo '<br/>'."\r\n";

my_fseek($fp, $offset, SEEK_SET);

$objs=array();

for($i=0; $i<$nb_obj; ++$i) {
	$data=my_fread($fp,1);
	$id=ord($data)>>4;
	switch($id) {
		case 0://singleton
			break;
		case 1://integer
			break;
		case 2://float
			break;
		case 3://date
			break;
		case 4://binary data
			break;
		case 5://single byte string
			break;
		case 6://double byte string
			break;
		case 8://UID
			break;
		case 10://array
			break;
		case 12://set
			break;
		case 13://dictionnary
			break;
		default:
			die('error on line '.__LINE__.' : id='.$id.' for the '.($i+1).' element');
	}
	$size=ord($data)&0x0F;
	if($id!=0 && $size==15) {
	//if($size==15) {
		$data=ord(my_fread($fp,1));
		assert( ($data&0xF0) == 0x10) or die('incorrect '.($i+1).' element');
		$length=$data-0x10;
		$length=pow(2,$length);
		$size=read_nb($fp, $length);
	}
	//var_dump($size);
	
	$size2=NULL;
	switch($id) {
		case 0:
			$size2=0;
			break;
		case 1:
			assert($size>=0 && $size<=3);
			$size2=pow(2,$size);
			break;
		case 2:
			assert($size>2 && $size<=3);
			$size2=pow(2,$size);
			break;
		case 3:
			assert($size==3);
			$size2=pow(2,$size);
			break;
		case 6:
			$size2=2*$size;
		case 10:
		case 12:
			$size2=$size*$nb_bytes_objref;
			break;
		case 13:
			$size2=2*$size*$nb_bytes_objref;
			break;
		default:
			$size2=$size;
	}
	
	/*if($size2>0) {
		$data=my_fread($fp,$size2);
	}//*/
	var_dump($i, $id, $size, $size2);
	
	unset($value);
	switch($id) {
		case 0:
			switch($size) {
				case 0:
					$value=NULL;
					break;
				case 8:
					$value=true;
					break;
				case 9:
					$value=false;
					break;
				case 15:
					$value='fill';
					break;
				default:
					die('error on line '.__LINE__.' : id='.$id.' for the '.($i+1).' element');
			}
			break;
		case 1:
			if($size2>0) {
				$value=read_nb($fp, $size2);
			}
			else {
				$value=0;
			}
			break;
		case 2:
			$value=read_nb($fp, $size2);
			break;
		case 3:
			$value=read_nb($fp, $size2);
			break;
		case 4:
			$value=my_fread($fp, $size2);
			break;
		case 5:
			if($size2>0) {
				$value=my_fread($fp, $size2);
			}
			else {
				$value='';
			}
			break;
		case 6:
			$value=my_fread($fp, $size2);
			break;
		case 8:
			$value=read_nb($fp, $size2);
			break;
		case 10:
		case 12:
			$value=array();
			for($j=0;$j<$size;++$j) {
				$value[]=read_nb($fp, $nb_bytes_objref);
			}//*/
			//$value=my_fread($fp, $size2);
			break;
		case 13:
			$k=array();
			for($j=0;$j<$size;++$j) {
				$k[]=read_nb($fp, $nb_bytes_objref);
			}
			$v=array();
			for($j=0;$j<$size;++$j) {
				$v[]=read_nb($fp, $nb_bytes_objref);
			}
			$value=array_combine($k,$v);
			unset($k,$v);//*/
			//$value=my_fread($fp, $size2);
			break;
		default:
			die('error on line '.__LINE__.' : id='.$id.' for the '.($i+1).' element');
	}
	$objs[$i]=$value;
	var_dump($value);
	echo '<br/>'."\r\n";
}

my_fclose($fp);
?>