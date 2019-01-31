<?php 
include "UrabeSQLException.php";
include "HasamiUtils.php";
include "ConnectionError.php";
include "UrabeResponse.php";
include "MysteriousParser.php";
include "WebServiceContent.php";
require_once "resources/Warai.php";
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
abstract class KanojoX
{
    /**
     * Defines how the data is parsed while the result is fetch associatively
     *
     * @var MysteriousParser The selection data parser
     */
    public $parser;
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
     * @var int The http error code
     */
    public static $http_error_code;
    /**
     * @var DBDriver The database driver
     */
    public $db_driver;
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
     * @var string $password The connection password 
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
     * Returns the number of affected rows
     *
     * @var int The number of affected rows
     */
    public $affected_rows;
    /**
     * This function initialize Urabe error handling, settings
     * and exception handler. This method is called when an instance of KanojoX is created
     *
     * @return void
     */
    public static function start_urabe()
    {
        KanojoX::$errors = array();
        KanojoX::$settings = require "UrabeSettings.php";
        if (KanojoX::$settings->handle_errors)
            set_error_handler('KanojoX::error_handler');
        if (KanojoX::$settings->handle_errors)
            set_exception_handler('KanojoX::exception_handler');
    }
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
     * @param exception $exception The generated exception
     * @return void
     */
    public static function exception_handler($exception)
    {
        if (is_null(KanojoX::$http_error_code))
            http_response_code(400);
        else
            http_response_code(KanojoX::$http_error_code);
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
        $err = $response->get_exception_response(
            $exception->getMessage(),
            KanojoX::$settings->enable_stack_trace ? $exception->getTraceAsString() : null
        );

        $exc_response = $response->get_exception_response(
            $exception->getMessage(),
            KanojoX::$settings->enable_stack_trace ? $exception->getTraceAsString() : null
        );
        //If encoding fails means error context has resource objects that can not be encoded,
        //in that case will try the simple exception response
        $sql = $exc_response->error[NODE_QUERY];
        $exc_response = json_encode($exc_response);

        if (!$exc_response) {
            $exc_response = $response->get_simple_exception_response(
                $exception,
                KanojoX::$settings->enable_stack_trace ? $exception->getTraceAsString() : null
            );
            if (KanojoX::$settings->add_query_to_response)
                $exc_response->{NODE_SQL} = $sql;
            $exc_response->{NODE_SUCCEED} = false;
            $exc_response = json_encode($exc_response);
        }
        echo $exc_response;
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