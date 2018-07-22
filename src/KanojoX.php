<?php 
include "UrabeSQLException.php";
include "IKanojoX.php";
include "HasamiUtils.php";
include "ConnectionError.php";
include "UrabeResponse.php";
/**
 * Database connection model
 * 
 * Kanojo means girlfriend in japanese and this class saves the connection data structure used to connect to
 * an the database.
 * @version 1.0.0
 * @api Makoto Urabe database connector
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
abstract class KanojoX implements IKanojoX
{
    /**
     * @var array $error 
     * The application current errors
     */
    public static $errors;
    /**
     * @var array $settings 
     * Access the application settings.
     */
    public static $settings;
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
        KanojoX::$errors = array();
        KanojoX::$settings = require "UrabeSettings.php";
        if (KanojoX::$settings->handle_errors)
            set_error_handler('KanojoX::error_handler');
        if (KanojoX::$settings->handle_errors)
            set_exception_handler('KanojoX::exception_handler');
    }
    /**
     * Initialize the class with a JSON object
     *
     * @param stdClass $body_json The request body as JSON object
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

    /**
     * Handles application errors
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
    public static function error_handler($err_no, $err_msg, $err_file, $err_line, $err_context)
    {
        $error = new ConnectionError();
        $error->code = $err_no;
        $error->message = $err_msg;
        $error->file = $err_file;
        $error->line = $err_line;
        $error->set_err_context($err_context);
        array_push(KanojoX::$errors, $error);
    }
    /**
     * Handles application exceptions
     *
     * @param exception $exception The generated exeption
     * @return void
     */
    public static function exception_handler($exception)
    {
        http_response_code(400);
        $class = get_class($exception);

        $error = new ConnectionError();
        $error->code = $exception->getCode();
        $error->message = $exception->getMessage();
        $error->file = $exception->getFile();
        $error->line = $exception->getLine();
        if ($class == CLASS_SQL_EXC)
            $error->sql = $exception->sql;

        $response = new UrabeResponse();
        $response->error = $error->get_exception_error();
        echo json_encode($response->get_exception_response(
            $exception->getMessage(),
            KanojoX::$settings->enable_stack_trace ? $exception->getTraceAsString() : null
        ));
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
     * @param ConnectionError $error If the error exists pass the eror
     * @return ConnectionError The connection error 
     */
    public function error($sql, $error = null)
    {
        throw new Exception(sprintf(ERR_NOT_IMPLEMENTED, "error", "KanojoX"));
    }
    /**
     * Sends a request to execute a prepared statement with given parameters, 
     * and waits for the result
     *
     * @param string $sql The SQL Statement
     * @param array|null $variables The colon-prefixed bind variables placeholder used in the statement, can be null.
     * @throws Exception En Exception is raised if the connection is null
     * @return boolean|ConnectionError Returns TRUE on success or the connection error on failure. 
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
    /**
     * Gets the query for selecting the table definition
     *
     * @param string $table_name The table name
     * @return string The table definition selection query
     */
    public function get_table_definition_query($table_name)
    {
        throw new Exception(sprintf(ERR_NOT_IMPLEMENTED, "get_table_definition_query", "KanojoX"));
    }
}

?>