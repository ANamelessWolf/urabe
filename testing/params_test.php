<?php
include_once("../urabe/ParameterCollection.php");
$params = new ParameterCollection();
$params->get_url_parameters();
$params->get_variables();
echo $params->exists("id");
var_dump($params);
?>