<?php 
include_once "Kanojo.php";
include_once "Warai.php";
include_once "IKanojo.php";
/**
 * An ORACLE Connection object
 * 
 * Kanojo means girlfriend in japanase and this class saves the connection data structure used to connect to
 * an Oracle database.
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class ORACLEKanojoX extends KanojoX implements IKanojoX
{
    /**
     * This function builds a connection string to connecto to ORACLE
     * by default is connected via SID
     *
     * @return string The connection string
     */
    public function buildConnectionString($host, $dbname, $port)
    {
        return create_SID_connection($host, $dbname, $port);
    }
    /**
     * Closes a connection
     *
     * @param resource $connection An Oracle connection identifier returned by oci_connect()
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function close($connection)
    {
        return oci_close($connection);
    }
    /**
     * Open an ORACLE Database connection
     *
     * @param string $host Can be either a host name or an IP address. 
     * Passing the NULL value or the string "localhost" to this parameter, the local host is assumed.
     * @param string $username The database user name
     * @param string $passwd The database password, If not provided or NULL, 
     * the server will attempt to authenticate the user with no password only. 
     * @param string $dbname The database name
     * @param string $port The database port
     * @return resource The database connection object
     */
    public function connect($host, $username, $passwd, $dbname, $port)
    {
        if (!isset($host) || strlen($host) == 0)
            $host = "127.0.0.1";
        $connString = $this->buildConnectionString($host, $dbname, $port);
        return oci_connect($username, $passwd, $connString);
    }
    /**
     * Get the last error message string of a connection
     *
     * @param resource $connection An Oracle connection identifier returned by oci_connect()
     * @param string|null $sql The last excecuted statement. Can be null
     * @return ConnectionError The connection error 
     */
    public function error($connection, $sql)
    {
        $e = oci_error($connection);
        $error = new ConnectionError();
        $error->code = $e['code'];
        $error->message = $e['message'];
        $error->sql = isset($sql) ? $sql : $e['sqltext'];
        return $error;
    }
    /**
     * Sends a request to execute a prepared statement with given parameters, 
     * and waits for the result
     *
     * @param resource $connection An Oracle connection identifier returned by oci_connect()
     * @param string $sql The SQL Statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement. 
     * @return resource Returns a statement handle on success, or FALSE on error.
     */
    public function execute($connection, $sql, $variables = null)
    {
        try {
            $statement = $this->parse($connection, $sql);
            if (isset($variables) && is_array($variables))
                $this->bind($statement, $variables);
            $ok = oci_execute($statement);
            return $ok ? $statement : error($connection, $sql);
        } catch (Exception $e) {
            return error($connection, sprintf(ERR_BAD_QUERY, $this->query, $e->getMessage()));
        }
    }
    /**
     * Prepares sql_text using connection and returns the statement identifier, 
     * which can be used with oci_execute(). 
     *
     * @param resource $connection An Oracle connection identifier returned by oci_connect()
     * @param string $sql The SQL text statement
     * @return resource Returns a statement handle on success, or FALSE on error. 
     */
    private function parse($connection, $sql)
    {
        return oci_parse($connection, $sql);
    }
    /**
     * Binds a PHP variable to an Oracle placeholder
     *
     * @param resource $statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement. 
     * @return void
     */
    private function bind($statement, $variables)
    {
        foreach ($variable as &$value)
            oci_bind_by_name($statement, ":" . $value->bv_name, $value->variable);
    }
}
/**
 * Creates a SID connection to an ORACLE database
 * @param string $host The connection host address
 * @param string $SID The database name or Service ID
 * @param string $port Oracle connection port
 * @return string The oracle connection string
 */
function create_SID_connection($host, $SID, $port)
{
    $strConn = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = $host)(PORT = $port)))(CONNECT_DATA=(SID=$SID)))";
    return $strConn;
}
?>