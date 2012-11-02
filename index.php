<?php
define( '_NE', 1 );
define( '_UID',"test_class");

include 'modxclass.php';
include 'cacheclass.php';

$wwwpath=(empty($_GET['q'])) ? '' : htmlspecialchars($_GET['q']); // Active Link	

//$content = DB::getInstance();
$content = new DB ();
$htmlout=$content->getPageFromAlias($wwwpath);

printf ($htmlout );
?>