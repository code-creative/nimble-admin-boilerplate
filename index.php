<?php 
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');
setlocale(LC_MONETARY, 'en_GB');
require("app/nimble.php");
$n = null;
$n = new nimble();
$n->environment = "live";
$n->init();
