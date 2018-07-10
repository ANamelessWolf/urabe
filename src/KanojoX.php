<?php 

include_once "Warai.php";
include_once "IKanojoX.php";
/**
 * Database connection model
 * 
 * Kanojo means girlfriend in japanase and this class saves the connection data structure used to connect to
 * an the database.
 * @version 1.0.0
 * @api Makoto Urabe database connector
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
abstract class KanojoX implements IKanojoX
{
    /**
     * @var ConnectionError $error 
     * The last found error.
     */
    public $error;
    /**
     * @var string $host Can be either a host name or an IP address.
     */
    public $host = "127.0.0.1";
    /**
     * @var string $port Connection port
     */
    public $port;
    /**
     * @var string $db_name The database name.
     */
    public $db_name;
    /**
     * @var string $user_name The database connection user name.
     */
    public $user_name;
    /**
     * @var string|NULL $password The password can be null.
     */
    public $password = "";
    /**
     * Returns the collections of statement handled
     *
     * @var array statementsIds The statements ids collection
     */
    public $statementsIds;
    /**
     * @var resource $connection 
     * The connection object.
     */
    public $connection;
    /**
     * Initialize a new instance of the connection object
     */
    public function __construct()
    {
        $this->statementsIds = array();
    }

    /**
     * Creates a connection object to or returns false if the connection fails.
     * Errors are save on $this->error property
     * @param string $params The connection parameters
     * @param boolean $throw_warnings if true warnings are thrown as exceptions errors
     * @return bool|resource The connection object or false if the connection could not be created
     */
    public function create_connection($params = null, $throw_warnings)
    {
        try {
            if ($throw_warnings)
                set_error_handler('KanojoX::error_handler');
            $conn = $this->init_connection($params);
            if (!$conn) {
                $err = $this->get_error();
                $this->error = $err['message'];
            }
            return $conn;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }
    /**
     * This functions converts oracle warnings in to exceptions
     *
     * @param int $err_no Contains the level of the error raised, as an integer. 
     * @param string $err_msg The error message, as a string. 
     * @param string  $err_file The filename that the error was raised in, as a string
     * @param int $err_line The line number the error was raised at, as an integer
     * @param array $err_context an array that points to the active symbol table at the point the error occurred. 
     * In other words, err_context will contain an array of every variable that existed in the scope the error was triggered in. 
     * User error handler must not modify error context. 
     * @return bool Returns a string containing the previously defined error handler.
     */
    public function error_handler($err_no, $err_msg, $err_file, $err_line, array $err_context)
    {
        if (0 === error_reporting()) {
            return false;
        } else
            throw new Exception($err_msg, $err_no);
    }
    /**
     * Check if the class is of type ConnectionError
     *
     * @param stdClass $class The Kanojo class
     * @return boolean return True if the class is of type ConnectionError
     */
    public static function is_error($class){
        return get_class($class) == 'ConnectionError';
    }
    /*********************
     * Interface Methods *
     *********************/
    /**
     * Closes a connection
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function close()
    {
        throw new Exception(sprintf(ERR_NOT_IMPLEMENTED, "close", "KanojoX"));
    }
    /**
     * Open a Database connection
     *
     * @return stdClass The database connection object
     */
    public function connect()
    {
        throw new Exception(sprintf(ERR_NOT_IMPLEMENTED, "connect", "KanojoX"));
    }
    /**
     * Get the last error message string of a connection
     *
     * @param string|null $sql The last excecuted statement. Can be null
     * @return ConnectionError The connection error 
     */
    public function error($sql)
    {
        throw new Exception(sprintf(ERR_NOT_IMPLEMENTED, "error", "KanojoX"));
    }
    /**
     * Sends a request to execute a prepared statement with given parameters, 
     * and waits for the result
     *
     * @param string $sql The SQL Statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement.
     * @return stdClass A query result resource on success or FALSE on failure.
     */
    public function execute($sql, $variables = null)
    {
        throw new Exception(sprintf(ERR_NOT_IMPLEMENTED, "execute", "KanojoX"));
    }
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
    public function fetch_assoc($sql, $variables)
    {
        throw new Exception(sprintf(ERR_NOT_IMPLEMENTED, "fetch_assoc", "KanojoX"));
    }
    /**
     * Frees the memory associated with a result
     *
     * @param stdClass $statement The statement result
     * @return void
     */
    public function free_result()
    {
        throw new Exception(sprintf(ERR_NOT_IMPLEMENTED, "free_result", "KanojoX"));
    }
}

?>