<?php
namespace Urabe\DB;
use Urabe\Config\KanojoX;
use Urabe\Config\UrabeSettings;
use Urabe\Config\DBDriver;
use Urabe\Config\ConnectionError;
use Urabe\DB\MysteriousParser;
/**
 * Database connection model
 * 
 * Kanojo means girlfriend in japanese and this class saves the connection data structure used to connect to
 * an the database.
 * @version 1.0.0
 * @api Makoto Urabe DB Manager database connector
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
abstract class DBKanojoX
{
    /**
     * @var MysteriousParser The selection data parsed when the fetch function is called
     */
    public $parser;
    /**
     * @var DBDriver The database driver
     */
    public $db_driver;
    /**
     * @var KanojoX The database connection credentials
     */
    public $kanojo;
    /**
     * @var array statementsIds The collections of statement handled Ids
     */
    public $statementsIds;
    /**
     * @var int The number of affected rows
     */
    public $affected_rows;
    /**
     * @var object The database connection object.
     */
    public $connection;
    /**
     * @var string The last inserted id
     */
    public $insert_id;
    /**
     * Initialize a new instance of the connection object
     * null the data is parsed associatively column value
     * @param DBDriver $db_driver The type of database to connect
     * @param KanojoX $connection The connection data
     * @param MysteriousParser $parser Defines how the data is going to be parsed if,
     */
    public function __construct($db_driver, $connection, $parser)
    {
        $this->db_driver = $db_driver;
        $this->kanojo = $connection;
        $this->statementsIds = array();
        $this->parser = $parser;
        $this->affected_rows = 0;
        $this->connection = null;
    }
    /**
     * Destruct the Kanojo Instance and try to close and free memory if
     * is connected and had statement ids.
     */
    function __destruct()
    {
        if ($this->connection)
            $this->close();
    }
    /**
     * Gets the last executed error
     *
     * @return ConnectionError The last executed error
     */
    public function get_last_error()
    {
        $errors = UrabeSettings::$errors;
        $index = sizeof($errors) - 1;
        return $index >= 0 ? $errors[0] : null;
    }
    /*********************
     **** SQL Parsing ****
     *********************/
    /**
     * Gets the placeholders format for the original prepared query string. 
     * The number of elements in the array must match the number of placeholders. 
     *
     * @param int $index The place holder index if needed
     * @return string The place holder at the given position
     */
    public function get_param_place_holder($index = null)
    {
        return '?';
    }
    /************************
     * Shared functionality *
     ************************/
    /**
     * Closes a connection
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    abstract public function close();
    /**
     * Open a Database connection
     *
     * @return object The database connection object
     */
    abstract public function connect();
    /**
     * Get the last error message string of a connection
     *
     * @param string|null $sql The last executed statement. Can be null
     * @param ConnectionError $error If the error exists pass the error
     * @return ConnectionError The connection error 
     */
    abstract public function error($sql, $error = null);
    /**
     * Sends a request to execute a prepared statement with given parameters, 
     * and waits for the result
     *
     * @param string $sql The SQL Statement
     * @param array|null $variables The colon-prefixed bind variables placeholder used in the statement, can be null.
     * @throws Exception This method is not implemented in the abstract class
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    abstract public function execute($sql, $variables = null);
    /**
     * Returns an associative array containing the next result-set row of a 
     * query. Each array entry corresponds to a column of the row. 
     * This function is typically called in a loop until it returns FALSE, 
     * indicating no more rows exist.
     *
     * @param string $sql The SQL Statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement.
     * @return array Returns an associative array. If there are no more rows in the statement then the connection error is returned.
     * */
    abstract public function fetch_assoc($sql, $variables);
    /**
     * Frees the memory associated with a result
     *
     * @return void
     */
    abstract public function free_result();

}
