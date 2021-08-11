<?php 
namespace Urabe\DB;
use Urabe\Config\KanojoX;
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
     * @var resource The database connection object.
     */
    public $connection;
    /**
     * @var int The number of affected rows
     */
    public $affected_rows;

    /**
     * Initialize a new instance of the connection object
     * @param MysteriousParser $parser Defines how the data is going to be parsed if,
     * null the data is parsed associatively column value
     */
    public function __construct($parser = null)
    {
        $this->statementsIds = array();
        if (is_null($parser))
            $this->parser = new MysteriousParser();
        else
        $this->parser = $parser;
        KanojoX::start_urabe();
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
     * Initialize the class with a JSON object
     *
     * @param object $body_json The request body as JSON object
     * @throws Exception An Exception is raised when the body is null or missed one or more of the 
     * following variables: host, user_name, password, port, db_name
     * @return void
     */
    public function init($body_json)
    {
        $fields = array("host", "user_name", "password", "port", "db_name");
        if (isset($body_json)) {
            foreach ($fields as &$value) {
                if (isset($body_json->{$value}))
                    $this->{$value} = $body_json->{$value};
                else
                    throw new Exception(sprintf(ERR_INCOMPLETE_BODY, "initialize", join(', ', $fields)));
            }
        } else
            throw new Exception(ERR_BODY_IS_NULL);
    }
    /**
     * Gets the last executed error
     *
     * @return ConnectionError The last executed error
     */
    public function get_last_error()
    {
        $errors = KanojoX::$errors;
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
    /**
     * Gets the query for selecting the table definition
     *
     * @param string $table_name The table name
     * @return string The table definition selection query
     */
    abstract public function get_table_definition_query($table_name);
    /**
     * Gets the table definition parser for the database connector
     *
     * @return array The table definition fields as an array of FieldDefinition
     */
    abstract function get_table_definition_parser();
    /**
     * Gets the table definition mapper for the database connector
     *
     * @return array The table mapper as KeyValued<String,String> array
     */
    abstract function get_table_definition_mapper();
}

?>