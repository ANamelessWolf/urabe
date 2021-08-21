<?php

namespace Urabe\DB;

use Exception;
use Urabe\Service\UrabeResponse;

/**
 * Database Manager Query Executor
 * 
 * Update, Insert, Delete and Execute SQL query
 * 
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class Executor
{
    /**
     * @var DBKanojoX The database manager
     */
    private $connector;
    /**
     * @var bool Check if there is an active connection to the database.
     */
    private $is_connected;
    /**
     * @var MysteriousParser The database parser
     */
    public $parser;
    /**
     * __construct
     *
     * Initialize a new instance of the Urabe Database manager.
     * The connection is opened in the constructor should be closed using close method.
     * @param DBKanojoX $connector The database connector
     * @param MysteriousParser $parser The database parser
     * @param int $is_connected The database driver
     */
    public function __construct($connector, $is_connected, $parser)
    {
        $this->is_connected = $is_connected;
        $this->connector = $connector;
        $this->parser = $parser;
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
     * @param array $values The values to insert as key value pair
     * Example: 
     * array("column1" => value1, "column2" => value2)
     * @throws Exception An Exception is raised if the connection is null or executing a bad query
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    public function insert($table_name, $values)
    {
        if ($this->is_connected) {
            $query_format = "INSERT INTO " . $table_name . " (%s) VALUES (%s)";
            $stmt = new InsertStatement($this->connector, $values);
            $sql = $stmt->build_sql($query_format);
            $response = $this->query($sql, $stmt->values);
            return $response;
        } else
            throw new Exception($this->connector->error);
    }
    /**
     * Performs a bulk insertion query into a table
     *
     * @param string $table_name The table name.
     * @param array $values The values to insert as key value pair array. 
     * Example: 
     * array(
     *  array("column1" => value1),
     *  array("column2" => value2)
     * )
     * @throws Exception An Exception is raised if the connection is null or executing a bad query
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    public function insert_bulk($table_name, $values)
    {
        if ($this->is_connected) {
            $query_format = "INSERT INTO " . $table_name . " (%s) VALUES %s";
            $stmt = new InsertBulkStatement($this->connector, $values);
            $sql = $stmt->build_sql($query_format);
            $response = $this->query($sql, $stmt->values);
            return $response;
        } else
            throw new Exception($this->connector->error);
    }
    /**
     * Performs an update query 
     *
     * @param string $table_name The table name.
     * @param array $values The values to update as key value pair array. 
     * Example: 
     * array("column1" => value1)
     * @param string $condition The condition to match, this condition should not use place holders.
     * @throws Exception An Exception is raised if the connection is null or executing a bad query
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    public function update($table_name, $values, $condition)
    {
        if ($this->is_connected) {
            $query_format = "UPDATE $table_name SET %s";
            $stmt = new UpdateStatement($this->connector, $values);
            $sql = $stmt->build_sql($query_format);
            $sql = sprintf("%s WHERE %s", $sql, $condition);
            $response = $this->query($sql, $stmt->values);
            return $response;
        } else
            throw new Exception($this->connector->error);
    }
    /**
     * Performs an update query by defining a condition
     * where the $column_name has to be equal to the given $column_value.
     *
     * @param string $table_name The table name.
     * @param array $values The values to update as key value pair array. 
     * Example: 
     * array("column1" => value1)
     * @param string $column_name The column name used in the condition.
     * @param string $column_value The column value used in the condition.
     * @throws Exception An Exception is raised if the connection is null or executing a bad query
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    public function update_by_field($table_name, $values, $column_name, $column_value)
    {
        $field = $this->parser->table_definition->get_field_definition($column_name);
        if ($field->data_type == PARSE_AS_INT || $field->data_type == PARSE_AS_LONG || $field->data_type == PARSE_AS_NUMBER)
            $format = "%s = %s";
        else
            $format = "%s = '%s'";
        $condition = sprintf($format, $column_name, $column_value);
        $this->update($table_name, $values, $condition);
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
        if ($this->is_connected) {
            $sql = "DELETE FROM $table_name WHERE $condition";
            return $this->query($sql);
        } else
            throw new Exception($this->connector->error);
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
        if ($this->is_connected) {
            $sql = "DELETE FROM $table_name WHERE $column_name = %s";
            $sql = sprintf($sql, $this->connector->get_param_place_holder(1));
            $variables = array($column_value);
            return $this->query($sql, $variables);
        } else
            throw new Exception($this->connector->error);
    }
}
