<?php
include_once("../KanojoX.php");
include_once("../Urabe.php");
$kanojo = new KanojoX();
$kanojo->host = "10.0.0.3";
$kanojo->user_name = "riviera";
$kanojo->password = "r4cks";
$connector = new Urabe($kanojo);
$result = $connector->get_table_definition("BASES");
$first_value = $connector->select("SELECT * FROM BASES", null, FALSE);
var_dump($first_value->result);
?>