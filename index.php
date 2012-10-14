<?php
include 'Phpjsondns.php';

$hostname = $_GET['q'];
$type = $_GET['t'];

$phpjsondns = new Phpjsondns($hostname, $type);
echo $phpjsondns->get();
