<?php 
/**
 * A Database data struct 
 * 
 * Kanojo means girlfriend in japanase and this class saves the connection data structure used to connect to
 * an Oracle database.
 * @version 1.0.0
 * @api Makoto Urabe Oracle
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class KanojoX
{
    /**
     * @var string CONN_FORMAT_SERVICE_NAME
     * The default connection string
     */
    const CONN_FORMAT_SERVICE_NAME = '//%s/%s';
    /**
     * @var string DEFAULT_CHAR_SET
     * Oracle default char set, is UTF8
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
     * @var string $db_name The Oracle service name.
     */
    public $service_name = "pdb_orcl";
    /**
     * @var string $user_name The Oracle connection user name.
     */
    public $user_name = "root";
    /**
     * @var string|NULL $server The Oracle user password can be null.
     */
    public $password = "";
    /**
     * Creates a connection object to oracle or returns false if the connection fails.
     * Errors are save on $this->error property
     * @param string $conn_str The connection string if available
     * @param boolean $throw_warnings if true oracle warnings are thrown as exceptions errors
     * @return bool|resource The connection object type='oci8 connection' or false
     */
    public function create_connection($conn_str = null, $throw_warnings = true)
    {
        try {
            if ($throw_warnings)
                set_error_handler('KanojoX::error_handler');
            if (is_null($conn_str))
                $conn_string = sprintf(self::CONN_FORMAT_SERVICE_NAME, $this->host, $this->service_name);
            else
                $conn_string = $conn_str;
            $conn = oci_connect($this->user_name, $this->password, $conn_string, self::DEFAULT_CHAR_SET);
            if (!$conn) {
                $err = oci_error();
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