<?php
include_once "../src/ORACLEKanojoX.php";
include_once "../src/PGKanojoX.php";
include_once "../src/MYSQLKanojoX.php";
include_once "TestUtils.php";
/**
 * Writes a connection file in the tmp folder as conn_file.json"
 * @param object $body The request body
 * @return object The response message
 */
function test_write_connection_file($body)
{
    $kanojo = pick_connector($body->driver, $body);
    $kanojo->init($body->connection);
    save_connection("../tmp/conn_file.json", $kanojo);
    $response = new UrabeResponse();
    return $response->get_response("JSON file created", array());
}
/**
 * Reads a connection file and returns the connection file
 *
 * @param object $body The request body
 * @return KanojoX The database connector
 */
function test_read_connection_file($body)
{
    return get_KanojoX_from_file("../tmp/conn_file.json");
}
?>