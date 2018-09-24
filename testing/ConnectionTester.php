<?php
/**
 * This file test the connection to a given database, specifying the data connection and
 * Kanojo driver.
 * 
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
include_once "../src/KanojoX.php";
include_once "../src/ORACLEKanojoX.php";
include_once "../src/PGKanojoX.php";
include_once "../src/MYSQLKanojoX.php";

//Test Response
$response = (object)array(
    "msg" => "",
    "status" => true,
    "error" => ""
);
//0: Reads the body
$body = get_body_as_json();
//1: Selects the driver connector
if ($body->driver == "ORACLE")
    $kanojo = new ORACLEKanojoX();
else if ($body->driver == "PG")
    $kanojo = new PGKanojoX();
else if ($body->driver == "MYSQL")
    $kanojo = new MYSQLKanojoX();
else {
    $response->msg = "Driver " + (isset($body->driver) ? $body->driver . "not supported." : " not valid.");
    $response->status = false;
}
if (isset($kanojo)) {
    //2: Initialize the connection data
    $kanojo->init($body);
    //3: Connect to the Database
    $conn = $kanojo->connect();

    if ($conn)
        $response->msg = "Connected to " . $body->driver;
    else {
        http_response_code(403);
        $response->msg = "Error connecting to " . $body->driver . ". See error for more details.";
        $response->error = $kanojo->get_last_error();//KanojoX::$errors;
        $response->status = false;
    }
    $response->{"settings"} = KanojoX::$settings;
    $kanojo->close();
}
echo json_encode($response);
?>