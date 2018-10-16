<?php
include_once "./utils/UrabeTestUtils.php";
/**
 * This file test the functionality for the class Urabe
 *  
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
$result = (object)array();
//0: Reads the body
$body = get_body_as_json();
if (isset($body)) {
    //1: Creates a Kanojo Object and initialize it
    $kanojo = pick_connector($body->driver, $body);
    $kanojo->init($body->connection);
    //2: Open Urabe connector
    $urabe = new Urabe($kanojo);
} else
    $urabe = null;
//4: Pick a test
$test = TEST_VAR_NAME . "_" . $_GET[TEST_VAR_NAME];
//5: Test
$result->{$_GET[TEST_VAR_NAME]} = $test($urabe, $body);
//Connection is closed automatically calling the kanojo destructor
echo json_encode($result);
?>