<?php
define( '_NE', 1 );
include 'modxclass.php';
$wwwpath=(empty($_GET['q'])) ? '' : htmlspecialchars($_GET['q']); // Active Link	

$content = DB::getInstance();
$htmlout=$content->getPageFromAlias($wwwpath);

printf (preg_replace( "/\[\[time\]\]/", "2 queries, phptime  s".$time, $htmlout ));
?>