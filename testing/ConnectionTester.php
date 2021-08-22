<?php
include '../src/UrabeAPI.php';

use Urabe\Config\KanojoXFile;
use Urabe\Config\DBDriver;
use Urabe\DB\ORACLEKanojoX;
use Urabe\DB\MYSQLKanojoX;
use Urabe\DB\PGKanojoX;

/**
 * This file test the connection to a given database, specifying the data connection and
 * Kanojo driver.
 * 
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */

//Test Response
$response = (object)array(
    "msg" => "",
    "connect_status" => true,
    "error" => ""
);

//1: Create the connection from a JSON File
$conn_data = new KanojoXFile("C:\\xampp\\htdocs\\urabe\\testing\\json\\examples\\connection-example.json");

//2: Selects the driver connector
if ($conn_data->db_driver == DBDriver::ORACLE)
    $connector = new ORACLEKanojoX($conn_data);
else if ($conn_data->db_driver == DBDriver::PG)
    $connector = new PGKanojoX($conn_data);
else if ($conn_data->db_driver == DBDriver::MYSQL)
    $connector = new MYSQLKanojoX($conn_data);
else {
    $response->msg = "Driver " . (isset($conn_data->driver) ? $conn_data->driver . "not supported." : " not valid.");
    $response->connect_status = false;
}
if (isset($connector)) {
    //3: Connect to the Database
    try {
        $conn = $connector->connect();
    } catch (Exception) {
        $conn = false;
    }
    //4: Check connections
    if ($conn)
        $response->msg = "Connected to " . $conn_data->db_name;
    else {
        http_response_code(403);
        $response->msg = "Error connecting to " . $conn_data->db_name . ". See error for more details.";
        $response->error = $connector->get_last_error()->message;
        $response->connect_status = false;
    }
}
echo json_encode($response);
?>