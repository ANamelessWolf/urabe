<?php 
include_once "Kanojo.php";
include_once "Warai.php";
include_once "IKanojo.php";
/**
 * A MySQL Connection object
 * 
 * Kanojo means girlfriend in japanase and this class saves the connection data structure used to connect to
 * an MySQL database.
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class MYSQLKanojoX extends KanojoX implements IKanojoX
{
    /**
     * Closes a connection
     *
     * @param mysqli $connection Procedural style only: A link identifier returned by mysqli_connect()
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function close($connection)
    {
        return $connection->close();
    }
    /**
     * Open a MySQL Database connection
     *
     * @param string $host Can be either a host name or an IP address. 
     * Passing the NULL value or the string "localhost" to this parameter, the local host is assumed.
     * @param string $username The database user name
     * @param string $passwd The database password, If not provided or NULL, 
     * the server will attempt to authenticate the user with no password only. 
     * @param string $dbname The database name
     * @param string $port The database port
     * @return stdClass The database connection object
     */
    public function connect($host, $username, $passwd, $dbname, $port)
    {
        return mysqli_connect($host, $username, $passwd, $dbname, $port);
    }
    /**
     * Get the last error message string of a connection
     *
     * @param mysqli $connection Procedural style only: A link identifier returned by mysqli_connect()
     * @param string|null $sql The last excecuted statement. Can be null
     * @return ConnectionError The connection error 
     */
    public function error($connection, $sql)
    {
        $error = new ConnectionError();
        $error->code = $connection->connect_errno;
        $error->message = $mysqli->connect_error;
        $error->sql = $sql;
        return $error;
    }
    /**
     * Sends a request to execute a prepared statement with given parameters, 
     * and waits for the result
     *
     * @param mysqli $link Procedural style only: A link identifier returned by mysqli_connect()
     * @param string $sql The SQL Statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement. 
     * @return resource Returns a statement object or FALSE if an error occurred. 
     */
    public function execute($connection, $sql, $variables = null)
    {
        try {
            $statement = $this->parse($connection, $sql);
            if (isset($variables) && is_array($variables))
                $this->bind($statement, $variables);
            $ok =  $statement->execute();
            return $ok ? $statement : error($connection, $sql);
        } catch (Exception $e) {
            return error($connection, sprintf(ERR_BAD_QUERY, $this->query, $e->getMessage()));
        }
    }
    /**
     * Prepares sql_text using connection and returns the statement identifier, 
     * which can be used with execute(). 
     *
     * @param mysqli $link Procedural style only: A link identifier returned by mysqli_connect()
     * @param string $sql The SQL text statement
     * @return mysqli Returns a statement handle on success, or FALSE on error. 
     */
    private function parse($link, $sql)
    {
        return $mysqli->prepare($sql);
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
        foreach ($variable as &$value) {
            if (is_int($value->variable))
                $tp = "i";
            else if (is_double($value->variable))
                $tp = "d";
            else if (is_string($value->variable))
                $tp = "s";
            else
                $tp = "b";
            return $stmt->bind_param($tp, $value->variable);
        }
    }
}
?>