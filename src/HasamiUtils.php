<?php

/******************************************
 ********** Connection utils **************
 *****************************************/
/**
 * Saves the connection data extracted from a
 * KanojoX Object
 *
 * @param string $file_path The path where the file is going to be saved
 * @param KanojoX $kanojo The Kanojo connection object
 * @throws Exception An Exception is thrown if theres an error creating the file
 * @return void
 */
function save_connection($file_path, $kanojo)
{
    $data = array(
        "connection" =>
            array(
            "host" => $kanojo->host,
            "user_name" => $kanojo->user_name,
            "password" => $kanojo->password,
            "port" => $kanojo->port,
            "db_name" => $kanojo->db_name
        ),
        "driver" => DBDriver::getName($kanojo->db_driver)
    );
    if ($kanojo->db_driver == DBDriver::ORACLE)
        $data["owner"] = $kanojo->owner;
    else if ($kanojo->db_driver == DBDriver::PG)
        $data["schema"] = $kanojo->schema;
    if (file_put_contents($file_path, json_encode($data, JSON_PRETTY_PRINT)) == false)
        throw new Exception(ERR_SAVING_JSON);
}
/**
 * Reads a connection file and returns the database connector object as KanojoX Class
 *
 * @param string $file_path The path where the file is located
 * @throws Exception An Exception is thrown if theres a problem reading the file
 * @return KanojoX The connection object
 */
function get_KanojoX_from_file($file_path)
{
    $kanojoObj = open_json_file($file_path);
    $driver = $kanojoObj->driver;
    if ($driver == "ORACLE") {
        $kanojo = new ORACLEKanojoX();
        $kanojo->owner = $kanojoObj->owner;
    } else if ($driver == "PG") {
        $kanojo = new PGKanojoX();
        $kanojo->schema = $kanojoObj->schema;
    } else if ($driver == "MYSQL")
        $kanojo = new MYSQLKanojoX();
    else
        throw new Exception("Driver " + (isset($driver) ? $driver . "not supported." : " not valid."));
    $kanojo->init($kanojoObj->connection);
    return $kanojo;
}
/*************************************
 ************ File utils *************
 *************************************/
/**
 * Creates a JSON object from a JSON file
 *
 * @param string $file_path The JSON file path
 * @throws Exception An Exception is thrown if theres an error reading the file
 * @return object The JSON Object
 */
function open_json_file($file_path)
{
    if (file_exists($file_path)) {
        $file_string = file_get_contents($file_path);
        //Remove escaping characters
        $file_string = preg_replace('!/\*.*?\*/!s', '', $file_string);
        $file_string = preg_replace('/(\/\/).*/', '', $file_string);
        $file_string = preg_replace('/\n\s*\n/', "\n", $file_string);
        //Encode as UTF8
        $file_string = utf8_encode($file_string);
        $json_object = json_decode($file_string);
        if (is_null($json_object))
            throw new Exception(sprintf(ERR_READING_JSON_FILE, $file_path));
        else
            return $json_object;
    } else
        throw new Exception(sprintf(ERR_READING_JSON_FILE, $file_path));
}
/******************************************
 ************ Default queries *************
 *****************************************/
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
/******************************************
 ********* HTTP Response Result ***********
 *****************************************/
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
/************************************
 ********* Misc functions ***********
 ************************************/
/**
 * Creates a pretty json print from a JSON object, defining a pretty
 * print format. 
 *
 * @param stdClass $json The JSON object
 * @param JsonPrettyPrint $format The class
 * @param bool $background_dark True if a dark background is applied
 * @return string The json object in the pretty print format.
 */
function pretty_print_format($json, $format = null, $background_dark = true)
{
    if (is_null($format) && $background_dark)
        $format = new JsonPrettyPrint();
    else if (is_null($format))
        $format = new JsonPrettyPrintLight();
    if ($background_dark)
        $json_string = '<body bgcolor="#394034">';
    else
        $json_string = "";
    $json_string .= $format->get_format($json);
    if ($background_dark)
        $json_string .= '</body>';
    return $json_string;
}

/**
 * From the current request body create a JSON object
 *
 * @return stdClass The JSON body
 */
function get_body_as_json()
{
    $body = file_get_contents('php://input');
    $body = json_decode($body);
    return $body;
}


?>