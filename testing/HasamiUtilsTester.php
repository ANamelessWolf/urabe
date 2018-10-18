<?php
/**
 * This file test the functionality for the Hasami Utils
 *  
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */

include_once "./utils/HasamiUtilsTestUtils.php";
//Test Response
$response = (object)array(
    "msg" => "",
    "status" => true,
    "error" => ""
);
//0: Reads the body
$body = get_body_as_json();
//1: Pick a test
$test = TEST_VAR_NAME . "_" . $_GET[TEST_VAR_NAME];
//2: Test
$result->{$_GET[TEST_VAR_NAME]} = $test($body);
//Connection is closed automatically calling the kanojo destructor
echo json_encode($result,JSON_PRETTY_PRINT);
?>