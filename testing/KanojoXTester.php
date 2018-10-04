<?php
include_once "./utils/KanojoXTestUtils.php";
/**
 * This file test the functionality for the class KanojoX
 *  
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */

$response = new UrabeResponse();
$result = (object)array();
//0: Reads the body
$body = get_body_as_json();
if (isset($body)) {
    //1: Selects the driver connector
    $kanojo = pick_connector($body->driver, $body);
    //2: Initialize the connection data
    $kanojo->init($body->connection);
    //3: Connect to the Database
    $conn = $kanojo->connect();
} else
    $kanojo = null;
//4: Pick a test
$test = TEST_VAR_NAME . "_" . $_GET[TEST_VAR_NAME];
//5: Test
$result->{$_GET[TEST_VAR_NAME]} = $test($kanojo, $body);
//6: Close the connection
$conn = isset($kanojo) ? $kanojo->close() : null;
//7: Print result
echo json_encode($result);
?>