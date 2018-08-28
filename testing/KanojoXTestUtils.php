<?php
include_once "../src/ORACLEKanojoX.php";
include_once "../src/PGKanojoX.php";
include_once "../src/MYSQLKanojoX.php";
include_once "TestUtils.php";

/**
 * This file defines the tests available for testing the 
 * KanojoX class
 *  
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */

/**
 * This function is an example for testing a SQL selection query and
 * fetching the result associatively
 *
 * @param KanojoX $kanojo The database connector
 * @param object $body The request body decoded as an object from JSON data
 * @return UrabeResponse The selection result as a web service response
 */
function test_fetch_assoc_no_params($kanojo, $body)
{
    $sql = $body->sql_no_params;
    $result = new UrabeResponse();
    $row = $kanojo->fetch_assoc($sql);
    $result = $result->get_response("KanojoX fetch assoc test with no params", $row, $sql);
    return $result;
}
/**
 * This function is an example for testing a SQL selection query that creates a prepared statement with the given parameters then 
 * fetches the result associatively
 *
 * @param KanojoX $kanojo The database connector
 * @param object $body The request body decoded as an object from JSON data
 * @return UrabeResponse The selection result as a web service response
 */
function test_fetch_assoc_with_params($kanojo, $body)
{
    $result = new UrabeResponse();
    $sql = $body->sql_params;
    $params = $body->params;
    $row = $kanojo->fetch_assoc($sql, $params);
    $result = $result->get_response("KanojoX fetch assoc test with params", $row, $sql);
    return $result;
}

/**
 * This function is an example for testing a SQL selection query that selects a table definition
 *
 * @param KanojoX $kanojo The database connector
 * @param object $body The request body decoded as an object from JSON data
 * @return UrabeResponse The selection result as a web service response
 */
function test_get_table_definition($kanojo, $body)
{
    $result = new UrabeResponse();
    $sql = $kanojo->get_table_definition_query($body->table_name);
    $row = $kanojo->fetch_assoc($sql);
    $result = $result->get_response("KanojoX get table definition test", $row, $sql);
    return $result;
}
/**
 * This function is an example for testing a SQL execution query.
 * This method is used if you are using INSERT, DELETE, or UPDATE SQL statements.
 *
 * @param KanojoX $kanojo The database connector
 * @param object $body The request body decoded as an object from JSON data
 * @return UrabeResponse The selection result as a web service response
 */
function test_execute_with_no_params($kanojo, $body)
{
    $result = new UrabeResponse();
    $sql = $body->update_sql_no_params;
    $result = $kanojo->execute($sql);
    return $result;
}
/**
 * This function is an example for testing a SQL execution query That creates a prepared statement with the given parameters. 
 * This method is used if you are using INSERT, DELETE, or UPDATE SQL statements.
 *
 * @param KanojoX $kanojo The database connector
 * @param object $body The request body decoded as an object from JSON data
 * @return UrabeResponse The selection result as a web service response
 */
function test_execute_with_params($kanojo, $body)
{
    $result = new UrabeResponse();
    $sql = $body->update_sql_params;
    $params = $body->params;
    $result = $kanojo->execute($sql, $params);
    return $result;
}
/**
 * This function is an example for testing an error triggering and managing errors using KanojoX
 *
 * @param KanojoX $kanojo The database connector
 * @param object $body The request body decoded as an object from JSON data
 * @return UrabeResponse The selection result as a web service response
 */
function test_send_error($kanojo, $body)
{
    trigger_error('Trigger Error', E_USER_WARNING);
    return $kanojo->get_last_error();
}
/**
 * This function list all available tests
 *
 */
function test_get_available_tests()
{
    $functions = get_defined_functions();
    $functions = $functions["user"];
    $test_func = array();
    for ($i = 0; $i < sizeof($functions); $i++) {
        if (substr($functions[$i], 0, 5) == TEST_VAR_NAME . "_")
            array_push($test_func, str_replace(array("test_"), array(), $functions[$i]));
    }
    $response = array("msg" => "Available functions", "tests" => $test_func, "size" => sizeof($test_func));
    return $response;
}
?>