<?php

namespace Urabe\DB;

use Exception;
use Urabe\Service\UrabeResponse;

/**
 * The database selector
 * 
 * A quick selector
 * 
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class Selector
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
     * Execute an SQL selection query and parse the data as defined in the parser. 
     * If the parser is null uses the parser defined in the connector object KanojoX::parser
     *
     * @param string $sql The SQL statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement.
     * @throws Exception An Exception is thrown if not connected to the database or if the SQL is not valid
     * @return UrabeResponse The SQL selection result
     */
    public function select($sql, $variables = null)
    {
        if ($this->is_connected) {
            $response = new UrabeResponse();
            //1: Executes the query and fetches the rows as an associative array
            $result = $this->connector->fetch_assoc($sql, $variables);
            //2: Formats response
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
     * @param mixed $default_val The default value
     * @return mixed The selected value taken from the first row and first column
     */
    public function select_one($sql, $variables = null, $default_val = null)
    {
        if ($this->is_connected) {
            //1: Executes the query and fetches the rows as an associative array            
            $result = $this->connector->fetch_assoc($sql, $variables);
            if (sizeof($result) > 0) {
                //2: Gets the first column
                $column = $this->get_first_column($result);
                return isset($column) ? $result[0][$column] : $default_val;
            } else
                return $default_val;
        } else
            throw new Exception($this->connector->error);
    }
    /**
     * Select all rows and returns just the values from the first selected column.
     * Used to select list of elements, no associatively
     *
     * @param string $sql The SQL statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement.
     * @return array The rows values from the first column
     */
    public function select_items($sql, $variables = null)
    {
        $values = array();
        if ($this->is_connected) {
            //1: Executes the query and fetches the rows as an associative array   
            $result = $this->connector->fetch_assoc($sql, $variables);
            if (sizeof($result) > 0) {
                //2: Gets the first column
                $column = $this->get_first_column($result);
                if (isset($column))
                    for ($i = 0; $i < sizeof($result); $i++)
                        array_push($values, $result[$i][$column]);
            }
        } else
            throw new Exception($this->connector->error);
        return $values;
    }
    /**
     * Selects all rows from a given table name, Calling select_all() is identical to calling select() with 
     * $sql = SELECT * FROM table_name
     *
     * @param string $table_name The name of the table
     * @throws Exception An Exception is thrown if not connected to the database or if the SQL is not valid
     * @return UrabeResponse The SQL selection result
     */
    public function select_all($table_name)
    {
        $sql = sprintf('SELECT * FROM %s', $table_name);
        return $this->select($sql, null);
    }
    /**
     * Gets the first column
     *
     * @param array $result The selection result as an array
     * @return string The first column name
     */
    private function get_first_column($result)
    {
        $columns = array_keys($result[0]);
        $column = sizeof($columns) > 0 ? $columns[0] : null;
        return $column;
    }
}
