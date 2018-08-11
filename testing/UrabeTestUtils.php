<?php
include_once "TestUtils.php";
include_once "../src/Urabe.php";
/**
 * This file defines the tests available for testing the 
 * Urabe class
 *  
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */

/**
 * This function is an example for testing a SQL selection query and
 * fetching the result via a defined parser
 *
 * @param Urabe $urabe The database data manager
 * @param object $body The request body decoded as an object from JSON data
 * @return UrabeResponse The selection result as a web service response
 */
function test_select($urabe, $body)
{
    $sql = $body->sql_simple;
    $result = $urabe->select($sql);
    $result->message = "Urabe test selection query with default parser";
    return $result;
}
?>