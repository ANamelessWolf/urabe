<?php
include_once "../src/Urabe.php";
//Test Response
$response = (object)array(
    "msg" => "",
    "status" => true,
    "result" => array(),
    "error" => ""
);
//0: Reads the body
$body = get_body_as_json();
//1: Selects the driver connector
if ($body->driver == "ORACLE") {
    $kanojo = new ORACLEKanojoX();
    $kanojo->owner = $body->owner;    
} else if ($body->driver == "PG")
    $kanojo = new PGKanojoX();
else if ($body->driver == "MYSQL")
    $kanojo = new MYSQLKanojoX();
else {
    $response->msg = "Driver " + (isset($body->driver) ? $body->driver . "not supported." : " not valid.");
    $response->status = false;
}
if (isset($kanojo)) {
    $urabe = new Urabe($kanojo);
    $response->result = $urabe->get_table_definition($body->table_name);
    $urabe->close();
}
echo json_encode($response);
?>