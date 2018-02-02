<?php
include_once "KanojoX.php";
include_once "Warai.php";
include_once "HasamiUtils.php";
include_once "FieldDefintion.php";
include_once "MysteriousParser.php";
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
        $format_query = "SELECT column_name, data_type, data_length FROM all_tab_cols WHERE table_name = '%s'";
        $query = sprintf($format_query, $table_name);
        $result = $this->select($query);
        $json_result = json_decode($result);
        $result = array();
        if (has_result($json_result))
            foreach ($json_result->result as &$value) {
            $result[$value->{self::FIELD_COLUMN}] = new FieldDefintion($value->{self::FIELD_COLUMN}, $value->{self::FIELD_DATA_TYPE});
        } else
            throw new Exception(sprintf(ERR_MISS_TABLE, $table_name));
        return $result;
    }
    /**
     * Gets a JSON object from query against the database. 
     *
     * @param string $query The query string. 
     * @param MysteriousParser $row_parser Defines the row parsing task. 
     * @return string Returns the value encoded in a JSON string.
     */
    function select($query, $row_parser = null)
    {
        $result = $this->get_array_response();
        try {
            if ($this->is_connected) {
                $query_result = $this->connection->query($query);
                if ($query_result) {
                    while ($row = $query_result->fetch_assoc()) {
                        if (is_null($row_parser))
                            array_push($result[NODE_RESULT], $row);
                        else
                            array_push($result[NODE_RESULT], $row_parser->parse($row));
                    }
                    $result[NODE_QUERY_RESULT] = true;
                } else {
                    $result[NODE_QUERY] = $query;
                    throw new Exception($this->connection->error);
                }
            } else if ($this->error != "")
                throw new Exception($this->error);
            else
                throw new Exception(ERR_CONNECTION_CLOSED);
        } catch (Exception $e) {
            $result[NODE_ERROR] = $e->getMessage();
        }
        return json_encode($result);
    }
    /**
     * Gets the first value found on the result. 
     *
     * @param string $query The query string. 
     * @return string The first value.
     */
    function select_one($query)
    {
        $result = null;
        if ($this->is_connected) {
            $query_result = $this->connection->query($query);
            if ($query_result) {
                while (is_null($result) && $row = $query_result->fetch_assoc())
                    $result = $row;
            } else
                $this->error = $this->connection->error;
        }
        if (!is_null($result))
            $result = array_pop(array_reverse($result));
        return $result;
    }
    /**
     * Gets a list of items by selecting the values in the first row and then returns the values in an array with no keys.
     *
     * @param string $query The query string. 
     * @return mixed[] A list of items.
     */
    function select_items($query)
    {
        $result = array();
        if ($this->is_connected) {
            $query_result = $this->connection->query($query);
            if ($query_result) {
                while ($row = $query_result->fetch_assoc()) {
                    $arr = array_reverse($row);
                    $item = array_pop($arr);
                    array_push($result, $item);
                }
            } else
                $this->error = $this->connection->error;
        }
        return $result;
    }
    /**
     * Gets the table column definitions
     *
     * @return string Returns the value encoded in a JSON string.
     */
    function select_table_names()
    {
        $query_format = "SELECT column_name, data_type, data_length FROM all_tab_cols WHERE table_name = '%s'";
        return $this->select_items(sprintf($query_format, $this->database_name));
    }
    /**
     * Gets a JSON object from query that selects all rows
     * from a table. 
     *
     * @param string $table_name The table name. 
     * @param MysteriousParser $row_parser Defines the row parsing task. 
     * @return string Returns the value encoded in a JSON string.
     */
    function select_all($table_name, $row_parser = null)
    {
        return $this->select(sprintf('SELECT * FROM `%s`', $table_name), $row_parser);
    }
    /**
     * Performs an insert query on the database
     *
     * @param string $table_name The table name.
     * @param string[] $fields The fields names used on the insert.
     * @param mixed[] $values The values to insert.
     * @return The query result
     */
    function insert($table_name, $fields, $values)
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
        return $this->query($query);
    }
    /**
     * Performs an insert query on the database, that inserts more than one value.
     *
     * @param string $table_name The table name.
     * @param string[] $fields The fields names used on the insert.
     * @param mixed[] $array_values The collection of values to insert.
     * @return The query result
     */
    function insert_bulk($table_name, $fields, $array_values)
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
        return $this->query($query);
    }
    /**
     * Performs an update query on the database by defining a condition
     *
     * @param string $table_name The table name.
     * @param string[] $fields The fields names used on the update.
     * @param mixed[] $values The values to update.
     * @param string $condition The condition to match
     * @return The query result
     */
    function update($table_name, $fields, $values, $condition)
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
     * @return The query result
     */
    function update_by_field($table_name, $fields, $values, $field, $value)
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
        $query .= ' WHERE ' . $field . ' = ';
        if (gettype($value) == 'integer' || gettype($value) == 'double')
            $query .= $value;
        else
            $query .= "'" . $value;
        return $this->query($query);
    }
    /**
     * Performs a delete query on the database by defining a condition
     *
     * @param string $table_name The table name.
     * @param string $condition The condition to match
     * @return The query result
     */
    function delete($table_name, $condition)
    {
        $query = 'DELETE FROM ' . $table_name . ' WHERE ' . $condition;
        return $this->query($query);
    }
    /**
     * Performs a delete query on the database by defining a condition
     * where the $field has to be equal to the $value.
     *
     * @param string $table_name The table name.
     * @param string $field The field name used on the condition.
     * @param string $value The field value used on the condition.
     * @return The query result
     */
    function delete_by_field($table_name, $field, $value)
    {
        $query = 'DELETE FROM ' . $table_name . ' WHERE `' . $field . '` = ' . $value;
        $result = $this->connection->query($query);
        if (gettype($value) == 'integer' || gettype($value) == 'double')
            $query .= $value;
        else
            $query .= "'" . $value;
        return $this->query($query);
    }
    /**
     * Perfoms a query
     *
     * @param string $query The query string. 
     * @return string Returns the query result encoded in a JSON string.
     */
    public function query($query)
    {
        $result = $this->get_array_response();
        try {
            if ($this->is_connected) {
                $query_result = $this->connection->query($query);
                if ($query_result)
                    $result[NODE_QUERY_RESULT] = true;
                else
                    throw new Exception($this->connection->error);
            } else if ($this->error != "")
                throw new Exception($this->error);
            else
                throw new Exception(ERR_CONNECTION_CLOSED);
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $result[NODE_ERROR] = $e->getMessage();
        }
        return json_encode($result);
    }
    /**
     * Close the connection to the database
     * @return void
     */
    public function close()
    {
        if ($this->is_connected) {
            $this->connection->close();
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
        $query = "SELECT * FROM information_schema . tables WHERE table_schema = '" . $this->database_name . "' AND table_name = '" . $table_name . "' LIMIT 1";
        $query_result = $this->connection->query($query);
        $exists = false;
        if ($query_result)
            $exists = $query_result->num_rows > 0;
        $this->error = $this->connection->error;
        return $exists;
    }
    /**
     * Test current connection
     * 
     * Test the current connection an gets a message with the connection status.
     * @return string The result message
     */
    public function test_connection()
    {
        if ($this->is_connected)
            return "Connected..." . $this->connection->host_info;
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
     * Gets an array to save the server response
     *
     * @return mixed[] The response array structure.
     */
    private function get_array_response()
    {
        return array(NODE_RESULT => array(), NODE_QUERY_RESULT => false, NODE_ERROR => "");
    }
}
?>