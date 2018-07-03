<?php
include_once("EmployeeService.php");
//echo pretty_print_format($table_def);
$service = new EmployeeService();
$service->response_is_encoded = false;
echo $service->get_response(true);
// var_dump($result);
?>