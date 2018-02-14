<?php
include_once("../urabe/KanojoX.php");
include_once("../urabe/Urabe.php");
include_once("../urabe/HasamiWrapper.php");
$kanojo = new KanojoX();
$kanojo->host = "10.0.0.3";
$kanojo->user_name = "riviera";
$kanojo->password = "r4cks";
$service = new HasamiWrapper("BASES", $kanojo, "ID_REVIT");
$result = $service->GET->get_response();
echo $result;
// var_dump($result);
?>