<?php
ob_start();
include('plist.php');
ob_end_clean();

echo '<pre>';
print_r($objs);
echo '</pre>';//*/

?>