<?php

/**
 * Connetion functionality
 * Implements a database connection functionality wraps most used
 * PHP database connector functions
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
interface IKanojoX
{
    /**
     * Closes a connection
     *
     * @param stdClass $connection A Database connection identifier returned by connect(). 
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function close($connection);
    /**
     * Open a Database connection
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
    public function connect($host, $username, $passwd, $dbname, $port);
    /**
     * Get the last error message string of a connection
     *
     * @param stdClass $connection A Database connection identifier returned by connect().
     * @param string|null $sql The last excecuted statement. Can be null
     * @return ConnectionError The connection error 
     */
    public function error($connection, $sql);

    /**
     * Sends a request to execute a prepared statement with given parameters, 
     * and waits for the result
     *
     * @param stdClass $connection A Database connection identifier returned by connect()
     * @param string $sql The SQL Statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement.
     * @return stdClass A query result resource on success or FALSE on failure.
     */
    public function execute($connection, $sql, $variables = null);
}
?>