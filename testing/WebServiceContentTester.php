<?php
include_once "UrabeTestUtils.php";
/**
 * This file test the functionality for the class Web Service Content
 *  
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */

//Settings is loaded when Kanojo class is created, in this test the connector driver is not important
$kanojo = pick_connector('ORACLE', null);
/**
 * Url parameters are set after the .php file
 * 
 */
$content = new WebServiceContent();

//See the service request content
echo json_encode($content);
?>