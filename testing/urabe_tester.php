<?php
include_once("../urabe/KanojoX.php");
include_once("../urabe/Urabe.php");
$kanojo = new KanojoX();
$kanojo->host = "10.0.0.3";
$kanojo->user_name = "riviera";
$kanojo->password = "r4cks";
$connector = new Urabe($kanojo);
$result = $connector->insert("BASES", array("A1"), array("12"), true);
echo $result;
?>