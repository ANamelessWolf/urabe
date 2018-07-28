<?php
//include_once "../src/KanojoX.php";
include_once "../src/ORACLEKanojoX.php";
include_once "../src/PGKanojoX.php";
include_once "../src/MYSQLKanojoX.php";
function test_fetch_assoc_no_params($kanojo, $sql)
{
    $row = $kanojo->fetch_assoc($sql);
    $result = $result->get_response("KanojoX fetch_assoc Test with no params", $row, $sql);
    return $result;
}
function test_fetch_assoc_with_params($kanojo, $sql, $params)
{
    $row = $kanojo->fetch_assoc($sql, $params);
    $result = $result->get_response("KanojoX fetch_assoc Test with params", $row, $sql);
    return $result;
}
/**
 * This file test the functionality for the class KanojoX
 *  
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
//Test Response
$result = new UrabeResponse();
$response = (object)array(
    "fetch_assoc_params" => "",
    "fetch_assoc_no_params" => "",
    "table_definition" => "",
    "execute_result_no_params" => "",
    "execute_result_params" => ""
);
//0: Reads the body
$body = get_body_as_json();
//1: Selects the driver connector
if ($body->driver == "ORACLE") {
    $kanojo = new ORACLEKanojoX();
    $kanojo->owner = $body->owner;
} else if ($body->driver == "PG") {
    $kanojo = new PGKanojoX();
    $kanojo->schema = $body->schema;
} else if ($body->driver == "MYSQL")
    $kanojo = new MYSQLKanojoX();
else {
    $response->msg = "Driver " + (isset($body->driver) ? $body->driver . "not supported." : " not valid.");
    $response->status = false;
}
if (isset($kanojo)) {
    $sql = $body->sql;
    //2: Initialize the connection data
    $kanojo->init($body->connection);
    //3: Connect to the Database
    $conn = $kanojo->connect();

    //4: Fetch the result associatively
  //  $row = $kanojo->fetch_assoc($sql);
   // $response->fetch_assoc = $result->get_response("Selection Test Result", $row, $sql);
    $response->fetch_assoc_params = test_fetch_assoc_with_params($kanojo, $body->sql_params, array(1));
     //5: Get table definition test
    //$sql = $kanojo->get_table_definition_query($body->table_name);
    //$row = $kanojo->fetch_assoc($sql);
    //$response->table_definition = $result->get_response("Table definition", $row, $sql);

    //6: Test execute method
   // $sql = $body->update_sql_no_params;
   // $result = $kanojo->execute($sql);
    //$response->execute_result_no_params = $result;

    //$sql = $body->update_sql_params;
    //$result = $kanojo->execute($sql, array(249088.66, '1'));
   // $response->execute_result_params = $result;
     //Close the connection
    $conn = $kanojo->close();
        
    //Print test result
    echo json_encode($response);


}
?>