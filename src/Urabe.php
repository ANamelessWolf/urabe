<?php
include_once "ORACLEKanojoX.php";
include_once "PGKanojoX.php";
include_once "MYSQLKanojoX.php";
include_once "FieldDefinition.php";

/**
 * A Database connection manager
 * 
 * Urabe is the main protagonist in the Nazo no Kanojo X, this class manage and wraps all transactions to the database.
 * Given the Kanojo profile Urabe should be able to connect with ORACLE, PG and MySQL
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
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
     * Gets the current connection data
     *
     * @return Object current connection data
     */
    public function get_connection_data()
    {
        return array(
            "db_driver" => DBDriver::getName($this->connector->db_driver),
            "host" => $this->connector->host,
            "port" => $this->connector->port,
            "db_name" => $this->connector->db_name,
            "user_name" => $this->connector->user_name,
            //"password" = $this->connector->password
        );
    }

    /**
     * @var string $is_connected 
     * Check if there is an active connection to the database.
     */
    public $is_connected;
    /**
     * Gets the current parser
     *
     * @return MysteriousParser The current parser
     */
    public function get_parser()
    {
        return $this->connector->parser;
    }
    /**
     * Sets the current parser
     *
     * @param MysteriousParser $parser The current parser
     * @return void
     */
    public function set_parser($mys_parser)
    {
        $this->connector->parser = $mys_parser;
    }

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
     * Creates a cloned version of this instance, the connection is copied
     * without the parser
     *
     * @return Urabe The instance clone
     */
    public function get_clone()
    {
        //var_dump($this->connector->db_driver);
        $driver = $this->connector->db_driver;
        if (DBDriver::MYSQL == $driver)
            $connector = new MYSQLKanojoX();
        else if (DBDriver::ORACLE == $driver) {
            $connector = new ORACLEKanojoX();
            $connector->owner = $this->connector->owner;
        } else if (DBDriver::PG == $driver) {
            $connector = new PGKanojoX();
            $connector->schema = $this->connector->schema;
        }
        $connector->host = $this->connector->host;
        $connector->port = $this->connector->port;
        $connector->db_name = $this->connector->db_name;
        $connector->user_name = $this->connector->user_name;
        $connector->password = $this->connector->password;
        return new Urabe($connector);
    }
    /**
     * Execute an SQL selection query and parse the data as defined in the parser. 
     * If the parser is null uses the parser defined in the connector object KanojoX::parser
     *
     * @param string $sql The SQL statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement.
     * @param MysteriousParser $row_parser The row parser. 
     * @throws Exception An Exception is thrown if not connected to the database or if the SQL is not valid
     * @return UrabeResponse The SQL selection result
     */
    public function select($sql, $variables = null, $row_parser = null)
    {
        if ($this->is_connected) {
            $response = new UrabeResponse();
            //1: Select row parsing method
            if (isset($row_parser)) //&& is_callable($row_parser->parse_method))
                $this->connector->parser = $row_parser;
            //2: Executes the query and fetches the rows as an associative array
            $result = $this->connector->fetch_assoc($sql, $variables);
            //3: Formats response
            $result = $response->get_response(INF_SELECT, $result, $sql);
            return $result;
        } else
            throw new Exception($this->connector->error);
    }
    /**
     * Gets the first value found on the first row and firs column.
     * If no values are selected a default value is returned
     *
     * @param string $sql The SQL statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement.
     * @param string $default_val The default value
     * @return string The selected value taken from the first row and first column
     */
    public function select_one($sql, $variables = null, $default_val = null)
    {
        $result = $this->connector->fetch_assoc($sql, $variables);
        if (sizeof($result) > 0) {
            $result = $result[0];
            $columns = array_keys($result);
            return sizeof($columns) > 0 ? strval($result[$columns[0]]) : $default_val;
        } else
            return $default_val;
    }
    /**
     * Select all rows and returns just the values from the first selected column.
     * Used to select list of elements, no associatively
     *
     * @param string $sql The SQL statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement.
     * @return array The first column values inside an array.
     */
    public function select_items($sql, $variables = null)
    {
        $result = $this->connector->fetch_assoc($sql, $variables);
        $values = array();
        if (sizeof($result) > 0) {
            $columns = array_keys($result[0]);
            $sel_column = sizeof($columns) > 0 ? $columns[0] : null;
            if (isset($sel_column))
                for ($i = 0; $i < sizeof($result); $i++)
                    array_push($values, $result[$i][$sel_column]);
        }
        return $values;
    }
    /**
     * Selects all rows from a given table name, Calling select_all() is identical to calling select() with 
     * $sql = SELECT * FROM table_name
     *
     * @param string $table_name The name of the table
     * @param MysteriousParser $row_parser The row parser. 
     * @throws Exception An Exception is thrown if not connected to the database or if the SQL is not valid
     * @return UrabeResponse The query result as a JSON String or a query result.
     */
    public function select_all($table_name, $row_parser = null)
    {
        return $this->select(sprintf('SELECT * FROM %s', $table_name), null, $row_parser);
    }
    /**
     * Gets the table definition
     *
     * @param string $table_name The name of the table
     * @throws Exception An exception is thrown when the table doesn't exists.
     * @return array The row definition of the table fields.
     */
    public function get_table_definition($table_name)
    {
        if ((strpos($table_name, '.') !== false)) {
            $tn = explode('.', $table_name);
            $tn = $tn[1];
        } else
            $tn = $table_name;
        return get_table_definition($this->connector, $tn);
    }
    /**
     * Check if a table exists on the database
     *
     * @param string $table_name The name of the table
     * @return bool The query result
     */
    public function table_exists($table_name)
    {
        $result = $this->select($this->connector->get_table_definition_query($table_name), null, null);
        return $result->size > 0;
    }
    /**
     * This function is an alias of KanojoX::execute()
     *
     * @param string $sql The SQL statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement.
     * @throws Exception An Exception is raised if the connection is null or executing a bad query
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    public function query($sql, $variables = null)
    {
        return $this->connector->execute($sql, $variables);
    }
    /**
     * Performs an insertion query into a table
     *
     * @param string $table_name The table name.
     * @param object $values The values to insert as key value pair
     * Column names as keys and insert values as associated value, place holders can not be identifiers only values.
     * @throws Exception An Exception is raised if the connection is null or executing a bad query
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    public function insert($table_name, $values)
    {
        $query_format = "INSERT INTO " . $table_name . " (%s) VALUES (%s)";
        $columns = array();
        $insert_values = array();
        $params = array();
        $index = 0;
        //Build prepare statement
        foreach ($values as $column => $value) {
            array_push($columns, $column);
            array_push($insert_values, $this->connector->get_param_place_holder(++$index));
            array_push($params, $value);
        }
        $columns = implode(', ', $columns);
        $insert_values = implode(', ', $insert_values);
        $sql = sprintf($query_format, $columns, $insert_values);
        $response = $this->query($sql, $params);
        return $response;
    }
    /**
     * Performs a bulk insertion query into a table
     *
     * @param string $table_name The table name.
     * @param array $columns The columns as an array of strings
     * @param array $values The values to insert as key value pair array. 
     * Column names as keys and insert values as associated value, place holders can not be identifiers only values.
     * @throws Exception An Exception is raised if the connection is null or executing a bad query
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    public function insert_bulk($table_name, $columns, $values)
    {
        $query_format = "INSERT INTO " . $table_name . " (%s) VALUES %s";
        $value_format = "(%s)";
        $insert_rows = array();
        $params = array();
        $index = 0;
        //Build prepare statement
        for ($i = 0; $i < sizeof($values); $i++) {
            $insert_values = array();
            for ($c = 0; $c < sizeof($columns); $c++) {
                array_push($insert_values, $this->connector->get_param_place_holder(++$index));
                array_push($params, $values[$i]->{$columns[$c]});
            }
            array_push($insert_rows, sprintf($value_format, implode(', ', $insert_values)));
        }
        $columns = implode(', ', $columns);
        $insert_rows = implode(', ', $insert_rows);
        $sql = sprintf($query_format, $columns, $insert_rows);
        $response = $this->query($sql, $params);
        return $response;
    }
    /**
     * Performs an update query 
     *
     * @param string $table_name The table name.
     * @param array $values The values to update as key value pair array. 
     * Column names as keys and update values as associated value, place holders can not be identifiers only values.
     * @param string $condition The condition to match, this condition should not use place holders.
     * @throws Exception An Exception is raised if the connection is null or executing a bad query
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    public function update($table_name, $values, $condition)
    {
        $query_format = "UPDATE $table_name SET %s WHERE %s";
        $set_format = "%s = %s";
        $update_values = array();
        $params = array();
        $index = 0;
        //Build prepare statement
        foreach ($values as $column => $value) {
            array_push($update_values, sprintf($set_format, $column, $this->connector->get_param_place_holder(++$index)));
            array_push($params, $value);
        }
        $update_values = implode(', ', $update_values);
        $sql = sprintf($query_format, $update_values, $condition);
        $response = $this->query($sql, $params);
        return $response;
    }
    /**
     * Performs an update query by defining a condition
     * where the $column_name has to be equal to the given $column_value.
     *
     * @param string $table_name The table name.
     * @param array $values The values to update as key value pair array. 
     * Column names as keys and update values as associated value, place holders can not be identifiers only values.
     * @param string $column_name The column name used in the condition.
     * @param string $column_value The column value used in the condition.
     * @throws Exception An Exception is raised if the connection is null or executing a bad query
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    public function update_by_field($table_name, $values, $column_name, $column_value)
    {
        $query_format = "UPDATE $table_name SET %s WHERE $column_name = %s";
        $set_format = "%s = %s";
        $update_values = array();
        $params = array();
        $index = 0;
        //Build prepare statement
        foreach ($values as $column => $value) {
            array_push($update_values, sprintf($set_format, $column, $this->connector->get_param_place_holder(++$index)));
            array_push($params, $value);
        }
        array_push($params, $column_value);
        $update_values = implode(', ', $update_values);
        $sql = sprintf($query_format, $update_values, $this->connector->get_param_place_holder(++$index));
        $response = $this->query($sql, $params);
        return $response;
    }
    /**
     * Performs a deletion query by defining a condition
     * where the $column_name has to be equal to the given $column_value.
     *
     * @param string $table_name The table name.
     * @param string $condition The condition to match, this condition should not use place holders.
     * @throws Exception An Exception is raised if the connection is null or executing a bad query
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    public function delete($table_name, $condition)
    {
        $sql = "DELETE FROM $table_name WHERE $condition";
        return $this->query($sql);
    }
    /**
     * Performs a deletion query by defining a condition
     * where the $column_name has to be equal to the given $column_value.
     *
     * @param string $table_name The table name.
     * Column names as keys and update values as associated value, place holders can not be identifiers only values.
     * @param string $column_name The column name used in the condition.
     * @param string $column_value The column value used in the condition.
     * @throws Exception An Exception is raised if the connection is null or executing a bad query
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    public function delete_by_field($table_name, $column_name, $column_value)
    {
        $sql = "DELETE FROM $table_name WHERE $column_name = %s";
        $sql = sprintf($sql, $this->connector->get_param_place_holder(1));
        $variables = array($column_value);
        return $this->query($sql, $variables);
    }
    /**
     * Formats the bindable parameters place holders in to
     * the current driver place holder format
     *
     * @param string $sql The sql statement
     * @return string Returns the formatted sql statement
     */
    public function format_sql_place_holders($sql)
    {
        $matches = array();
        preg_match_all("/@\d+/", $sql, $matches);
        $search = array();
        $replace = array();
        for ($i = 0; $i < sizeof($matches[0]); $i++)
            if (!in_array($matches[0][$i], $search)) {
                $index = intval(str_replace('@', '', $matches[0][$i]));
                array_push($search, $matches[0][$i]);
                array_push($replace, $this->connector->get_param_place_holder($index));
            }
        return str_replace($search, $replace, $sql);
    }
    /**
     * Gets the database connector driver
     *
     * @return DBDriver The database driver
     */
    public function get_driver()
    {
        return $this->connector->db_driver;
    }
}
