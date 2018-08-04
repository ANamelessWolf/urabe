<?php
include_once "ORACLEKanojoX.php";
include_once "PGKanojoX.php";
include_once "MYSQLKanojoX.php";
include_once "FieldDefinition.php";
include_once "MysteriousParser.php";

/**
 * A Database connection manager
 * 
 * Urabe is the main protagonist in the Nazo no Kanojo X, this class manage and wraps all transactions to the database.
 * Given the Kanojo profile Urabe should be able to connect with ORACLE, PG and MySQL
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class Urabe
{
    /**
     * @var KanojoX $connector 
     * Defines the database connector
     */
    private $connector;

    /**
     * @var string $is_connected 
     * Check if there is an active connection to the database.
     */
    public $is_connected;

    /**
     * __construct
     *
     * Initialize a new instance of the Urabe Database manager.
     * The connection is opened in the constructor should be closed using close method.
     * @param KanojoX $connector The database connector.
     */
    public function __construct($connector)
    {
        if (isset($connector)) {
            $this->connector = $connector;
            $this->connector->connect();
            if ($this->connector) {
                $this->is_connected = true;
                $this->database_name = $this->connector->db_name;
            } else {
                $this->is_connected = false;
                $this->error = $this->connector;
            }
        } else
            throw new Exception(ERR_BAD_CONNECTION);
    }
    /**
     * Gets the database connection from the current
     * Kanojo object connector
     *
     * @return stdClass The database connection
     */
    private function get_db_connection()
    {
        return $this->connector->connector;
    }
    /**
     * Gets the table definition on an array
     *
     * @param string $table_name The name of the table
     * @throws Exception An exception is thrown when the table doesn't exists.
     * @return FieldDefinition[] The row definition of the table fields.
     */
    function get_table_definition($table_name)
    {
        $result = $this->select($this->connector->get_table_definition_query($table_name), null, false);
        if ($result->query_result)
            return $result;
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
                $query_result->query_result = $this->connector->fetch_assoc($sql, null);
            } else
                throw new Exception($this->connector->error);
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
            array_push($stid);
            oci_execute($stid);
            $query_result->query_result = oci_fetch_all($stid, $query_result->result, 0, 1);
            if ($query_result->query_result)
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
            array_push($stid);
            $query_result->query_result = $query_result->fetch($stid, $row_parser);
            if ($query_result->query_result)
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
                array_push($stid);
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
        $query_format = "INSERT INTO `%s` (%s) %s";
        $fields_count = count($fields);
        $array_length = count($array_values);
        $fields_str = "";
        $values_str = "";
        $values_coll = array();
        for ($i = 0; $i < $fields_count; $i++) {
            $fields_str .= '`' . $fields[$i] . '`, ';
            for ($j = 0; $j < $array_length; $j++) {
                if ($i == 0)
                    array_push($values_coll, "SELECT " . $this->format_value($array_values[$j][$i]) . ", ");
                else if ($i < ($fields_count - 1))
                    $values_coll[$j] .= $this->format_value($array_values[$j][$i]) . ", ";
                else
                    $values_coll[$j] .= $this->format_value($array_values[$j][$i]) . " FROM DUAL UNION ALL ";
            }
        }
        $cut_length = strlen(" FROM DUAL UNION ALL ");
        $fields_str = substr($fields_str, 0, strlen($fields_str) - 2);
        foreach ($values_coll as &$value)
            $values_str .= $value;
        $values_str = substr($values_str, 0, strlen($values_str) - $cut_length);
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
        return $this->update($query, $fields, $values, $this->create_field_condition($field, $value), $encode);
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
        return $this->delete($table_name, $this->create_field_condition($field, $value), $encode);
    }

    /**
     * Close the connection to the database
     * @return void
     */
    public function close()
    {
        if (isset($this->connector))
            $this->connector->close();
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
    /**
     * Creates a condition where a field name must be equals to a value
     *
     * @param string $field The field name
     * @param mixed $value The field value
     * @return string The SQL condition
     */
    private function create_field_condition($field, $value)
    {
        $condition = $field . ' = ';
        if (gettype($value) == 'integer' || gettype($value) == 'double')
            $condition .= $value;
        else
            $condition .= "'" . $value . "'";
        return $condition;
    }
}
?>