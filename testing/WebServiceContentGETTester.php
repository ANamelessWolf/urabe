<?php
include '../src/UrabeAPI.php';

use Urabe\Service\WebServiceContent;

/**
 * This file test the functionality for the class Web Service Content
 * When the request method is GET
 *  
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */

/**
 * Url parameters are set after the .php file
 * 
 */
$content = new WebServiceContent();
//1: Check if the variable id exists
$idExists = $content->get_vars->exists("id");
$nameExists = $content->get_vars->exists("name");
//2: Get variable values
$id = $content->get_vars->get("id");
$name = $content->get_vars->get("name");
$conVal = $content->get_vars->get("condition");
//3: Compare value
$idOk = $content->get_vars->compare("id", $conVal);
//4: Pick values
$values = $content->get_vars->pick_values("id", "user");
//5: Validate obligatory fields
$oblOk = $content->get_vars->validate_obligatory("id", "user");
//6: Condition
$condition = $content->build_simple_condition("id");

$result = array(
    "keys" => $content->get_vars->keys,
    "IdFieldExists" => $idExists, "NameExists" => $nameExists,
    "IdValue" => $id, "NameValue" => $name, "IdCompare" => $idOk, "values" => $values,
    "oblOk" => $oblOk, "condition" => $condition
);

//See the service request content
echo json_encode($result);
