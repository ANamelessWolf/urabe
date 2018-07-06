<?php 
include_once "Kanojo.php";
include_once "Warai.php";
include_once "IKanojo.php";
/**
 * A PostgreSQL Connection object
 * 
 * Kanojo means girlfriend in japanase and this class saves the connection data structure used to connect to
 * an PostgreSQL database.
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class PGKanojoX extends KanojoX implements IKanojoX
{
    /**
     * Closes a connection
     *
     * @param resource $connection PostgreSQL database connection resource. 
     * The default connection is the last connection made by pg_connect().
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function close($connection)
    {
        return pg_close($connection);
    }
    /**
     * Open a PostgreSQL Database connection
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
        $connString = $strConn = "host='$host' port='$port' dbname='$dbname' user='$username' ";
        if (isset($passwd) && strlen($passwd) > 0)
            $connString .= "password='$passwd'";
        return pg_connect($connString);
    }
    /**
     * Get the last error message string of a connection
     *
     * @param resource $connection PostgreSQL database connection resource. 
     * @param string|null $sql The last excecuted statement. Can be null
     * @return ConnectionError The connection error 
     */
    public function error($connection, $sql)
    {
        /**
         * Posssible errors
         * 0 = PGSQL_EMPTY_QUERY
         * 1 = PGSQL_COMMAND_OK
         * 2 = PGSQL_TUPLES_OK
         * 3 = PGSQL_COPY_TO
         * 4 = PGSQL_COPY_FROM
         * 5 = PGSQL_BAD_RESPONSE
         * 6 = PGSQL_NONFATAL_ERROR
         * 7 = PGSQL_FATAL_ERROR
         */
        $error = new ConnectionError();
        $error->code = pg_last_error($connection);
        $error->message = pg_result_status($connection);
        $error->sql = $sql;
        return $error;
    }
    /**
     * Sends a request to execute a prepared statement with given parameters, 
     * and waits for the result
     *
     * @param resource $connection PostgreSQL database connection resource. 
     * The default connection is the last connection made by pg_connect().
     * @param string $sql The SQL Statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement. 
     * @return resource A query result resource on success or FALSE on failure.
     */
    public function execute($connection, $sql, $variables = null)
    {
        try {
            if (isset($variables) && is_array($variables)) {
                $result = pg_prepare($connection, "", $sql);
                $vars = array();
                foreach ($variables as &$value)
                    array_push($vars, $value->variable);
                $result = pg_execute($connection, "", $vars);
            } else
                $result = pg_query($connection, $sql);
            return $result ? $result : error($connection, $sql);
        } catch (Exception $e) {
            return error($connection, sprintf(ERR_BAD_QUERY, $this->query, $e->getMessage()));
        }
    }
}
?>