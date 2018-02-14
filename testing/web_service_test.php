<?php
include_once("../urabe/KanojoX.php");
include_once("../urabe/Urabe.php");
include_once("../urabe/HasamiWrapper.php");
$kanojo = new KanojoX();
$kanojo->host = "10.0.0.3";
$kanojo->user_name = "riviera";
$kanojo->password = "r4cks";
$table_def = open_json_file("table_test_definition.json");
$parser = FieldDefinition::parse_result($table_def);
//echo pretty_print_format($table_def);
 $service = new HasamiWrapper("BASES", $kanojo, "ID_REVIT",$parser);
 $service->response_is_encoded = false;
 $result = $service->GET->get_response();
 echo pretty_print_format($result);
// var_dump($result);
?>