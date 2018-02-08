<?php
include_once "KanojoX.php";
include_once "Warai.php";
include_once "HasamiUtils.php";
include_once "FieldDefinition.php";
include_once "MysteriousParser.php";
include_once "QueryResult.php";
/**
 * A MySQL connector 
 * 
 * Urabe is the main protagonist in the Nazo no Kanajo X, this class manage all transaction to the MySQL database.
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class Urabe
{
    /**
     * @var string FIELD_COLUMN
     * The name of the field column name from the table INFORMATION_SCHEMA .
     */
    const FIELD_COLUMN = 'COLUMN_NAME';
    /**
     * @var string FIELD_DATA_TYPE
     * The name of the field data type from the table INFORMATION_SCHEMA .
     */
    const FIELD_DATA_TYPE = 'DATA_TYPE';
    /**
     * @var KanojoX $database_id 
     * Defines the connection id to the database.
     */
    private $database_id;
    /**
     * @var resource $connection 
     * The connection object type='oci8 connection'.
     */
    public $connection;
    /**
     * @var string $db_name 
     * The database name used when performing queries.
     */
    public $database_name;
    /**
     * @var string $error 
     * The last error description.
     */
    public $error;
    /**
     * @var string $is_connected 
     * Check if there is an active connection to the database.
     */
    public $is_connected;
    /**
     * __construct
     *
     * Initialize a new instance of the Urabe MySql connector.
     * @param KanojoX $database_id The database connection id.
     */
    function __construct($database_id)
    {
        $this->error = "";
        $this->database_id = $database_id;
        $this->connection = $this->database_id->create_connection();
        if ($this->connection) {
            $this->is_connected = true;
            $this->database_name = $this->database_id->service_name;
        } else {
            $this->is_connected = false;
            $this->error = $this->database_id->error;
        }
    }
    /**
     * Gets the table defintion on an array
     *
     * @param string $table_name The name of the table
     * @throws Exception An excpetion is thrown when the table doesn't exists.
     * @return FieldDefintion[] The row definition of the table fields.
     */
    function get_table_definition($table_name)
    {
        $format_query = "SELECT %s, %s, %s FROM all_tab_cols WHERE table_name = '%s'";
        $query = sprintf($format_query, FIELD_COL_NAME, FIELD_DATA_TP, FIELD_DATA_LEN, $table_name);
        $result = $this->select($query, FieldDefinition::get_table_def_parser(), false);
        if ($result->query_result)
            return FieldDefinition::parse_result($result->result);
        else
            return array();
    }
    /**
     * Gets a JSON object from an Oracle query. 
     *
     * @param string $query The query string. 
     * @param MysteriousParser $row_parser Defines the row parsing task. 
     * @param boolean $encode True if the value is returned as encoded JSON string, otherwise
     * the result is returned as a query result
     * @return QueryResult|string The query result as a JSON String or a query result.
     */
    function select($query, $row_parser = null, $encode = true)
    {
        $query_result = new QueryResult();
        $query_result->query = $query;
        try {
            if ($this->is_connected) {
                $stid = $query_result->oci_parse($this->connection);
                if ($stid)
                    $query_result->query_result = $query_result->fetch($stid, $row_parser);
                else
                    throw new Exception($query_result->error); //An error is found
            } else
                throw new Exception($this->connection->error);
        } catch (Exception $e) {
            $query_result->error = $e->getMessage();
        }
        if ($encode)
            return $query_result->encode();
        else
            return $query_result;
    }
    /**
     * Gets the first value found on the first column. 
     *
     * @param string $query The query string. 
     * @param string $default_val The default value to return as a string value.
     * @return string The first value.
     */
    function select_one($query, $default_val = "")
    {
        $result = $default_val;
        $query_result = new QueryResult();
        $query_result->query = $query;
        if ($this->is_connected) {
            $stid = $query_result->oci_parse($this->connection);
            oci_execute($stid);
            oci_fetch_all($stid, $query_result->result, 0, 1);
            if ($query_result->result)
                $result = (string)reset($query_result->result)[0];
        } else
            $this->error = $this->connection->error;
        return $result;
    }
    /**
     * Select all values taken from the first selected column.
     *
     * @param string $query The query string. 
     * @return mixed[] The first column values inside an array.
     */
    function select_items($query)
    {
        $result = array();
        $query_result = new QueryResult();
        $query_result->query = $query;
        if ($this->is_connected) {
            $stid = $query_result->oci_parse($this->connection);
            oci_execute($stid);
            oci_fetch_all($stid, $result);
            if ($query_result->result)
                $result = reset($query_result->result);
        } else
            $this->error = $this->connection->error;
        return $result;
    }
    /**
     * Gets the table names from the current schema
     *
     * @return string[] The table names inside a string array.
     */
    function select_table_names()
    {
        $query = "SELECT DISTINCT OBJECT_NAME FROM USER_OBJECTS WHERE OBJECT_TYPE = 'TABLE'";
        return $this->select_items($query);
    }
    /**
     * Gets a JSON object from an Oracle query, that selects all fields
     * from the table.
     *
     * @param MysteriousParser $row_parser Defines the row parsing task. 
     * @param boolean $encode True if the value is returned as encoded JSON string, otherwise
     * the result is returned as a query result
     * @return QueryResult|string The query result as a JSON String or a query result.
     */
    function select_all($table_name, $row_parser = null, $encode = true)
    {
        return $this->select(sprintf('SELECT * FROM `%s`', $table_name), $row_parser, $encode);
    }
    /**
     * Executes a query
     *
     * @param string $query The query string. 
     * @param boolean $encode True if the value is returned as encoded JSON string, otherwise
     * the result is returned as a query result
     * @return QueryResult|string The query result as a JSON String or a query result.
     */
    public function query($query, $encode = true)
    {
        $query_result = new QueryResult();
        $query_result->query = $query;
        try {
            if ($this->is_connected) {
                $stid = $query_result->oci_parse($this->connection);
                if ($stid)
                    $query_result->query_result = oci_execute($stid);
                else
                    throw new Exception($query_result->error); //An error is found
            } else
                throw new Exception($this->connection->error);
        } catch (Exception $e) {
            $query_result->error = $e->getMessage();
        }
        if ($encode)
            return $query_result->encode();
        else
            return $query_result;
    }
    /**
     * Performs an insertion query to the current scheme
     *
     * @param string $table_name The table name.
     * @param string[] $fields The insertion field names.
     * @param mixed[] $values The values to insert.
     * @param boolean $encode True if the value is returned as encoded JSON string, otherwise
     * the result is returned as a query result
     * @return QueryResult|string The query result as a JSON String or a query result.
     */
    function insert($table_name, $fields, $values, $encode = true)
    {
        $query_format = "INSERT INTO `%s` (%s) VALUES (%s)";
        $max = count($fields);
        $fields_str = "";
        $values_str = "";
        for ($i = 0; $i < $max; $i++) {
            $fields_str .= '`' . $fields[$i] . '`, ';
            $values_str .= $this->format_value($values[$i]) . ', ';
        }
        $fields_str = substr($fields_str, 0, strlen($fields_str) - 2);
        $values_str = substr($values_str, 0, strlen($values_str) - 2);
        $query = sprintf($query_format, $table_name, $fields_str, $values_str);
        return $this->query($query, $encode);
    }
    /**
     * Performs an insertion query to the current schema, inserting more than
     * one value.
     *
     * @param string $table_name The table name.
     * @param string[] $fields The insertion field names.
     * @param mixed[][] $array_values The collection of values to insert.
     * @param boolean $encode True if the value is returned as encoded JSON string, otherwise
     * the result is returned as a query result
     * @return QueryResult|string The query result as a JSON String or a query result.
     */
    function insert_bulk($table_name, $fields, $array_values, $encode = true)
    {
        $query_format = "INSERT INTO `%s` (%s) VALUES %s";
        $fields_count = count($fields);
        $array_length = count($array_values);
        $fields_str = "";
        $values_str = "";
        $values_coll = array();
        for ($i = 0; $i < $fields_count; $i++) {
            $fields_str .= '`' . $fields[$i] . '`, ';
            for ($j = 0; $j < $array_length; $j++) {
                if ($i == 0)
                    array_push($values_coll, "(" . $this->format_value($array_values[$j][$i]) . ", ");
                else if ($i < ($fields_count - 1))
                    $values_coll[$j] .= $this->format_value($array_values[$j][$i]) . ", ";
                else
                    $values_coll[$j] .= $this->format_value($array_values[$j][$i]) . "), ";
            }
        }
        $fields_str = substr($fields_str, 0, strlen($fields_str) - 2);
        foreach ($values_coll as &$value)
            $values_str .= $value;
        $values_str = substr($values_str, 0, strlen($values_str) - 2);
        $query = sprintf($query_format, $table_name, $fields_str, $values_str);
        return $this->query($query, $encode);
    }
    /**
     * Performs an update query on the database by defining a condition
     *
     * @param string $table_name The table name.
     * @param string[] $fields The field names to update.
     * @param mixed[] $values The values to update.
     * @param string $condition The condition to match
     * @param boolean $encode True if the value is returned as encoded JSON string, otherwise
     * the result is returned as a query result
     * @return QueryResult|string The query result as a JSON String or a query result.
     */
    function update($table_name, $fields, $values, $condition, $encode = true)
    {
        $query = 'UPDATE ' . $table_name . ' SET ';
        for ($i = 0; $i < $max; $i++) {
            $query .= '`' . $fields[$i] . '`= ';
            if (gettype($values[$i]) == 'integer' || gettype($values[$i]) == 'double')
                $query .= $values[$i] . ", ";
            else
                $query .= "'" . $values[$i] . "', ";
        }
        $query = substr($query, 0, strlen($query) - 2);
        $query .= ' WHERE ' . $condition;
        return $this->query($query);
    }
    /**
     * Performs an update query on the database by defining a condition
     * where the $field has to be equal to the $value.
     *
     * @param string $table_name The table name.
     * @param string[] $fields The fields names used on the update.
     * @param mixed[] $values The values to update.
     * @param string $field The field name used on the condition.
     * @param string $value The field value used on the condition.
     * @param boolean $encode True if the value is returned as encoded JSON string, otherwise
     * the result is returned as a query result
     * @return QueryResult|string The query result as a JSON String or a query result.
     */
    function update_by_field($table_name, $fields, $values, $field, $value, $encode = true)
    {
        $condition = $field . ' = ';
        if (gettype($value) == 'integer' || gettype($value) == 'double')
            $condition .= $value;
        else
            $condition .= "'" . $value . "'";
        return $this->update($query, $fields, $values, $condition, $encode);
    }
    /**
     * Performs a delete query on the database by defining a condition
     *
     * @param string $table_name The table name.
     * @param string $condition The condition to match
     * @param boolean $encode True if the value is returned as encoded JSON string, otherwise
     * the result is returned as a query result
     * @return QueryResult|string The query result as a JSON String or a query result.
     */
    function delete($table_name, $condition, $encode = true)
    {
        $query = 'DELETE FROM ' . $table_name . ' WHERE ' . $condition;
        return $this->query($query, $encode);
    }
    /**
     * Performs a delete query on the database by defining a condition
     * where the $field has to be equal to the $value.
     *
     * @param string $table_name The table name.
     * @param string $field The field name used on the condition.
     * @param string $value The field value used on the condition.
     * @param boolean $encode True if the value is returned as encoded JSON string, otherwise
     * the result is returned as a query result
     * @return QueryResult|string The query result as a JSON String or a query result.
     */
    function delete_by_field($table_name, $field, $value, $encode = true)
    {
        $condition = $field . ' = ';
        if (gettype($value) == 'integer' || gettype($value) == 'double')
            $condition .= $value;
        else
            $condition .= "'" . $value . "'";
        return $this->delete($table_name, $condition, $encode);
    }

    /**
     * Close the connection to the database
     * @return void
     */
    public function close()
    {
        if ($this->is_connected) {
            oci_close($this->connection);
            $this->is_connected = false;
            $this->error = ERR_CONNECTION_CLOSED;
        }
    }
    /**
     * Check if a table exists on the database
     *
     * @param string $table_name The name of the table
     * @return bool The query result
     */
    public function table_exists($table_name)
    {
        $query = "SELECT DISTINCT OBJECT_NAME FROM USER_OBJECTS WHERE OBJECT_TYPE = 'TABLE' AND OBJECT_NAME = " . "'" . $table_name . "'";
        return sizeof($this->select_items($query)) > 0;
    }
    /**
     * Test the current connection
     * 
     * Test the current connection an gets a message with current connection status.
     * @return string The result message
     */
    public function test_connection()
    {
        if ($this->is_connected)
            return "Connected..." . oci_client_version();
        else
            return "Not Connected..." . $this->error;
    }
    /**
     * Gets the format string from a given value
     *
     * @param object $value The value to format.
     * @return string Gets the format value.
     */
    private function format_value($value)
    {
        if (is_numeric($value))
            return strval($value);
        else if (is_null($value))
            return "NULL";
        else
            return sprintf("'%s'", $value);
    }
}
?>