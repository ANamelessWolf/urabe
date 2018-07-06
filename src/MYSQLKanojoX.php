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
        return mysqli_close($connection);
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
}
?>