<?php
include_once "TestUtils.php";
include_once "../src/Urabe.php";
/**
 * This file defines the tests available for testing the 
 * Urabe class
 *  
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
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
    $sql = $body->sql_select;
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
/**
 * This functions is an example for testing an execute SQL statement. The
 * test returns a flag indicating if the result succeed and the number of affected rows
 *
 * @param Urabe $urabe The database data manager
 * @param object $body The request body decoded as an object from JSON data
 * @return UrabeResponse The execute result as a web service response
 */
function test_query($urabe, $body)
{
    $sql = $body->update_sql;
    return $urabe->query($sql);
}
/**
 * This functions is an example for testing an insert SQL statement. The
 * test returns a flag indicating if the result succeed and the number of inserted rows
 *
 * @param Urabe $urabe The database data manager
 * @param object $body The request body decoded as an object from JSON data
 * @return UrabeResponse The execute result as a web service response
 */
function test_insert($urabe, $body)
{
    $insert_params = $body->insert_params;
    if ($body->driver == "PG")
        $table_name = $body->schema . "." . $body->table_name;
    else
        $table_name = $body->table_name;
    return $urabe->insert($table_name, $insert_params);
}
/**
 * This functions is an example for testing an insert bulk SQL statement. The
 * test returns a flag indicating if the result succeed and the number of inserted rows
 *
 * @param Urabe $urabe The database data manager
 * @param object $body The request body decoded as an object from JSON data
 * @return UrabeResponse The execute result as a web service response
 */
function test_insert_bulk($urabe, $body)
{
    $bulk = $body->insert_bulk;
    if ($body->driver == "PG")
        $table_name = $body->schema . "." . $body->table_name;
    else
        $table_name = $body->table_name;
    return $urabe->insert_bulk($table_name, $bulk->columns, $bulk->values);
}
/**
 * This functions is an example for testing an update SQL statement. The
 * test returns a flag indicating if the result succeed and the number of affected rows
 *
 * @param Urabe $urabe The database data manager
 * @param object $body The request body decoded as an object from JSON data
 * @return UrabeResponse The execute result as a web service response
 */
function test_update($urabe, $body)
{
    $values = $body->update_params;
    $column_name = $body->column_name;
    $column_value = $body->column_value;
    if ($body->driver == "PG")
        $table_name = $body->schema . "." . $body->table_name;
    else
        $table_name = $body->table_name;
    return $urabe->update($table_name, $values, "$column_name = $column_value");
}
/**
 * This functions is an example for testing an update SQL statement. The
 * test returns a flag indicating if the result succeed and the number of affected rows
 *
 * @param Urabe $urabe The database data manager
 * @param object $body The request body decoded as an object from JSON data
 * @return UrabeResponse The execute result as a web service response
 */
function test_update_by_field($urabe, $body)
{
    $values = $body->update_params;
    $column_name = $body->column_name;
    $column_value = $body->column_value;
    if ($body->driver == "PG")
        $table_name = $body->schema . "." . $body->table_name;
    else
        $table_name = $body->table_name;
    return $urabe->update_by_field($table_name, $values, $column_name, $column_value);
}
/**
 * This functions is an example for testing a delete SQL statement. The
 * test returns a flag indicating if the result succeed and the number of affected rows
 *
 * @param Urabe $urabe The database data manager
 * @param object $body The request body decoded as an object from JSON data
 * @return UrabeResponse The execute result as a web service response
 */
function test_delete($urabe, $body)
{
    if ($body->driver == "PG")
        $table_name = $body->schema . "." . $body->table_name;
    else
        $table_name = $body->table_name;
    $column_name = $body->column_name;
    $column_value = $body->column_value;
    return $urabe->delete($table_name, "$column_name = $column_value");
}
/**
 * This functions is an example for testing a delete SQL statement. The
 * test returns a flag indicating if the result succeed and the number of affected rows
 *
 * @param Urabe $urabe The database data manager
 * @param object $body The request body decoded as an object from JSON data
 * @return UrabeResponse The execute result as a web service response
 */
function test_delete_by_field($urabe, $body)
{
    if ($body->driver == "PG")
        $table_name = $body->schema . "." . $body->table_name;
    else
        $table_name = $body->table_name;
    $column_name = $body->column_name;
    $column_value = $body->column_value;
    return $urabe->delete_by_field($table_name, $column_name, $column_value);
}
/**
 * This functions is an example for testing the sql place holders formatter
 *
 * @param Urabe $urabe The database data manager
 * @param object $body The request body decoded as an object from JSON data
 * @return UrabeResponse The execute result as a web service response
 */
function test_format_sql_place_holders($urabe, $body)
{
    $sql = $body->sql_common;
    return $urabe->format_sql_place_holders($sql);
}
/**
 * This function list all available tests
 *
 * @param KanojoX $kanojo The database connector
 * @return UrabeResponse The selection result as a web service response
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