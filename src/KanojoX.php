<?php 
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
abstract class KanojoX
{
    /**
     * @var string DEFAULT_CHAR_SET
     * The default char set, is UTF8
     */
    const DEFAULT_CHAR_SET = 'AL32UTF8';
    /**
     * @var string $error 
     * The last error description.
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
     * Initialize a new instance for the database connector
     * @param string $params The parameters needed to initialize the connection object
     * @return stdClass The database connector
     */
    abstract protected function init_connection($params);


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
}
?>