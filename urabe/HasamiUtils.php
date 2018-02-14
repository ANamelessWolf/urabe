<?php
include_once("HasamiWrapper.php");
include_once("QueryResult.php");
/**
 * Select all fields from the table that matches the condition 
 * where the primary key is equals to value. 
 *
 * @param HasamiWrapper $service The web service wrapper
 * @param mixed $value The value to match in the condition
 * @param boolean $encode True if the output is encoded as a JSON string
 * @return QueryResult|string The query result or the JSON string
 */
function select_by_primary_key($service, $value, $encode = false)
{
    try {
        $query = "SELECT * FROM %s WHERE %s = $value";
        $query = sprintf($query, $service->table_name, $service->primary_key);
        $response = $service->connector->select($query, $service->parser, $encode);
    } catch (Exception $e) {
        $response = get_error_response($e, $encode);
    }
    return $response;
}
/**
 * Select all fields from the table.
 *
 * @param HasamiWrapper $service The web service wrapper
 * @param boolean $encode True if the output is encoded as a JSON string
 * @return QueryResult|string The query result or the JSON string
 */
function select_all($service, $encode = false)
{
    try {
        $query = "SELECT * FROM %s";
        $query = sprintf($query, $service->table_name);
        $response = $service->connector->select($query, $service->parser, $encode);
    } catch (Exception $e) {
        $response = get_error_response($e, $encode);
    }
    return $response;

}
/**
 * Creates an error response from an exception error
 *
 * @param Exception $e The exception error
 * @param string $query The query that raises the error.
 * @param boolean $encode True if the output is encoded as a JSON string
 * @return QueryResult|string The error response as QueryResult or a JSON string
 */
function get_error_response($e, $query = "", $encode = false)
{
    $response = new QueryResult();
    $response->query_result = false;
    $response->query = $query;
    $response->error = $e->getMessage();
    if ($encode)
        $response = json_encode($response);
    return $response;
}
?>