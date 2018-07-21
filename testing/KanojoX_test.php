<?php
//include_once "../src/KanojoX.php";
include_once "../src/ORACLEKanojoX.php";
include_once "../src/PGKanojoX.php";
include_once "../src/MYSQLKanojoX.php";
/**
 * This file test the functionality for the class KanojoX
 *  
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
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
    $sql = $body->sql;
    //2: Initialize the connection data
 //   $kanojo->init($body);
    //3: Connect to the Database
  //  $conn = $kanojo->connect();
    
    while ($row = $kanojo->fetch_assoc($sql)) {
    
        foreach ($row as $item) {
    //        echo "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
        }
      
    }
    $response->error = $kanojo->get_last_error();
    
    //echo json_encode($response);
}
?>