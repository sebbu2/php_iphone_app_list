<?php
error_reporting(E_ALL | E_STRICT | E_RECOVERABLE_ERROR | E_DEPRECATED | E_USER_DEPRECATED);
if(array_key_exists('plist',$_REQUEST) && substr($_REQUEST['plist'],-4)==='.ipa' && file_exists($_REQUEST['plist'])) {
	$zip = new ZipArchive;
	$res = $zip->open($_REQUEST['plist']);
	if(!$res) die('error opening .ipa file');
	$data = $zip->getFromName('iTunesMetadata.plist');
	$xml = simplexml_load_string($data);
	echo '<?xml version="1.0" encoding="UTF-8"?>'."\r\n";
?><!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>items</key>
	<array>
		<dict>
			<key>assets</key>
			<array>
				<dict>
					<key>kind</key>
					<string>software-package</string>
					<key>url</key>
					<string><?php echo 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).'/'.$_REQUEST['plist']; ?></string>
				</dict>
				<dict>
					<key>kind</key>
					<string>display-image</string>
					<key>needs-shine</key>
					<false/>
					<key>url</key>
					<string><?php
$res2 = $xml->xpath('//key[starts-with(text(),"softwareIcon57x57URL")]/following::node()[2]');
//foreach($res2 as $node) {
while(list( , $node) = each($res2)) {
	print((string)$node);
}
					?></string>
				</dict>
			</array>
			<key>metadata</key>
			<dict>
				<key>bundle-identifier</key>
				<string><?php
$res2 = $xml->xpath('//key[starts-with(text(),"softwareVersionBundleId")]/following::node()[2]');
//foreach($res2 as $node) {
while(list( , $node) = each($res2)) {
	print((string)$node);
}
				?></string>
				<key>bundle-version</key>
				<string><?php
$res2 = $xml->xpath('//key[starts-with(text(),"bundleVersion")]/following::node()[2]');
//foreach($res2 as $node) {
while(list( , $node) = each($res2)) {
	print((string)$node);
}
				?></string>
				<key>kind</key>
				<string>software</string>
				<key>title</key>
				<string><?php
$res2 = $xml->xpath('//key[starts-with(text(),"itemName")]/following::node()[2]');
//foreach($res2 as $node) {
while(list( , $node) = each($res2)) {
	print((string)$node);
}
				?></string>
			</dict>
		</dict>
	</array>
</dict>
</plist><?php
}
else {
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Liste des packages</title>
</head>
<body>

<p>Liste des packages :<br/><?php
$files=glob('*.ipa');
foreach($files as $file) {
	echo '<a href="itms-services://?action=download-manifest&url=';
	echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'?plist='.rawurlencode($file);
	echo '">'.htmlentities($file).'</a><br/>';
}
?></p>

</body>
</html><?php
}
?>