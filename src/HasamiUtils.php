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

    $dir = dirname($file_path);
    if (!file_exists($dir))
        mkdir($dir, 0755);

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
/**
 * Gets the table from a table and the default connector.
 *
 * @param KanojoX $connector The database connector
 * @param string $table_name The name of the table, without schema or owner
 * @return array The table definition column array
 */
function get_table_definition($connector, $table_name)
{
    $connector->connect();
    $parser = new MysteriousParser($connector->get_table_definition_parser());
    $connector->parser = $parser;
    $parser->parse_method = "parse_table_field_definition";
    $parser->column_map = $connector->get_table_definition_mapper();
    $sql = $connector->get_table_definition_query($table_name);
    $result = $connector->fetch_assoc($sql, null);
    return $result;
}
/**
 * Gets the table from a table and the default connector.
 *
 * @param KanojoX $connector The database connector
 * @param string $table_name The name of the table
 * @return array The table definition column array
 */
function load_table_definition($table_name)
{
    $file_path = KanojoX::$settings->table_definitions_path . "$table_name.json";
    if (file_exists($file_path)) {
        $json = open_json_file($file_path);

        $fields = array();
        foreach ($json->columns as $column_name => $field_data)
            $fields[$column_name] = FieldDefinition::create($field_data);
        return $fields;
    } else
        throw new Exception(ERR_SAVING_JSON);
}
/**
 * Saves the table definition in a JSON file
 *
 * @param string $table_name The table name
 * @param DBDriver $driver The database driver
 * @param string $content The table definition content
 * @throws Exception An Exception is thrown if theres an error saving the file
 * @return void
 */
function save_table_definition($table_name, $driver, $content)
{
    $file_path = KanojoX::$settings->table_definitions_path . "$table_name.json";
    $data = array("table_name" => $table_name, "driver" => DBDriver::getName($driver), "columns" => $content);
    if (file_put_contents($file_path, json_encode($data, JSON_PRETTY_PRINT)) == false)
        throw new Exception(ERR_SAVING_JSON);
}
/**
 * Check if a file of a table definition exists
 *
 * @param string $table_name The table name
 * @return boolean True if the table definition file exists
 */
function table_definition_exists($table_name)
{
    $file_path = KanojoX::$settings->table_definitions_path . "$table_name.json";
    return file_exists($file_path);
}
/**
 * Gets the table definition store path
 *
 * @return string The table definition store path
 */
function get_table_definition_store_path()
{
    if (is_null(KanojoX::$settings))
        KanojoX::$settings = require "UrabeSettings.php";
    return KanojoX::$settings->table_definitions_path;
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

// /******************************************
//  ************ Default queries *************
//  *****************************************/
// /**
//  * Select all fields from the table that matches the condition 
//  * where the primary key is equals to value. 
//  *
//  * @param HasamiWrapper $service The web service wrapper
//  * @param mixed $value The value to match in the condition
//  * @param boolean $encode True if the output is encoded as a JSON string
//  * @return QueryResult|string The query result or the JSON string
//  */
// function select_by_primary_key($service, $value, $encode = false)
// {
//     try {
//         $query = "SELECT * FROM %s WHERE %s = $value";
//         $query = sprintf($query, $service->table_name, $service->primary_key);
//         $response = $service->connector->select($query, $service->parser, $encode);
//     } catch (Exception $e) {
//         $response = get_error_response($e, $encode);
//     }
//     return $response;
// }
// /**
//  * Select all fields from the table.
//  *
//  * @param HasamiWrapper $service The web service wrapper
//  * @param boolean $encode True if the output is encoded as a JSON string
//  * @return QueryResult|string The query result or the JSON string
//  */
// function select_all($service, $encode = false)
// {
//     try {
//         $query = "SELECT * FROM %s";
//         $query = sprintf($query, $service->table_name);
//         $response = $service->connector->select($query, $service->parser, $encode);
//     } catch (Exception $e) {
//         $response = get_error_response($e, $encode);
//     }
//     return $response;

// }
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
 * @param stdClass $json The JSON data to format
 * @param JsonPrettyStyle $style The JSON pretty format
 * @param bool $bg_black True if a dark background is applied otherwise the background will be white
 * @return string The response encoded as a pretty HTML
 */
function pretty_print_format($json, $style, $bg_black = true)
{
    $bg_color = $bg_black ? '#394034' : '#B1D9D2';
    $html = "";
    $html .= '<html><head>' .
        '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">' .
        '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>' .
        '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>' .
        '<style>' .
        'body { background-color: ' . $bg_color . '} ' .
        //'div { padding:0; margin:0; display:inline-block; float:left; }' .
        '</style>' .
        '</head>' .
        '<body>';
    $format = new JsonPrettyPrint($style);
    $html .= $format->get_format($json);
    $html .= '</body></html>';
    return $html;
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
/**
 * Removes from this base array all of its elements that are contained by the given array keys. 
 * @param array $base_array The array to modify
 * @param array $array_keys The array keys to be removed.
 * @return void
 */
function array_remove(&$base_array, $array_keys)
{
    foreach ($array_keys as &$key)
        unset($base_array[$key]);
}
