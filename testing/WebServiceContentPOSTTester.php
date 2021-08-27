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


$result = array(
    "keys" => $content->get_vars->keys,
    "IdFieldExists" => $idExists, "NameExists" => $nameExists,
    "IdValue" => $id, "NameValue" => $name, "IdCompare" => $idOk, "values" => $values,
    "oblOk" => $oblOk, "condition" => $condition
);

//See the service request content
var_dump((array)$content->body->content->values);
echo json_encode($content->body->content);
