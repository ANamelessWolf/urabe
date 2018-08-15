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
/**
 * This function is an example for testing the table definition selection
 *
 * @param Urabe $urabe The database data manager
 * @param object $body The request body decoded as an object from JSON data
 * @return UrabeResponse The selection result as a web service response
 */
function test_get_table_definition($urabe, $body)
{
    $result = $urabe->get_table_definition($body->table_name);
    $result->message = "Urabe test get table definition";
    return $result;
}
/**
 * This function is an example for testing a SQL selection query that returns one
 * value. The first row and first column
 *
 * @param Urabe $urabe The database data manager
 * @param object $body The request body decoded as an object from JSON data
 * @return string Returns the selected value
 */
function test_select_one($urabe, $body)
{
    $sql = $body->sql_simple;
    $result = $urabe->select_one($sql);
    return $result;
}
/**
 * This function is an example for testing a SQL selection query that returns the values from
 * the first selected column.
 *
 * @param Urabe $urabe The database data manager
 * @param object $body The request body decoded as an object from JSON data
 * @return mixed[] Returns the selected values
 */
function test_select_items($urabe, $body)
{
    $sql = $body->sql_simple;
    $result = $urabe->select_items($sql);
    return $result;
}

?>