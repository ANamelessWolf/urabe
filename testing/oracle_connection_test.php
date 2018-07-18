<?php
include_once "../src/ORACLEKanojoX.php";

$kanojo = new ORACLEKanojoX();
$kanojo->init(get_body_as_json());
$conn = $kanojo->connect();
$response = (object)array("msg" => "", "status" => false, "error" => "");
if ($conn)
    $response->msg = "Connected to ORACLE";
else {
    http_response_code(403);
    $response->msg = "Error connecting to ORACLE. See error for more details.";
    $response->error = $kanojo->error;
}
$kanojo->close();
echo json_encode($response);
?>