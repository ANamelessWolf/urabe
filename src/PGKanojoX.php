<?php 
include_once "KanojoX.php";
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
class PGKanojoX extends KanojoX
{
    /**
     * Open a PostgreSQL Database connection
     *
     * @return resource The database connection object
     */
    public function connect()
    {
        try {
            $host = $this->host;
            $port = $this->port;
            $dbname = $this->db_name;
            $username = $this->user_name;
            $passwd = $this->password;
            if (!isset($this->host) || strlen($host) == 0)
                $host = "127.0.0.1";
            $connString = $strConn = "host='$host' port='$port' dbname='$dbname' user='$username' ";
            if (isset($passwd) && strlen($passwd) > 0)
                $connString .= "password='$passwd'";
            $this->connection = pg_connect($connString);
            return $this->connection;
        } catch (Exception $e) {
            return $this->error(sprintf(ERR_BAD_CONNECTION, $e->getMessage()));
        }
    }
    /**
     * Closes a PostgreSQL database connection resource. 
     * The connection is the last connection made by pg_connect().
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function close()
    {
        return pg_close($this->connection);
    }
    /**
     * Frees the memory associated with a result
     *
     * @return void
     */
    public function free_result()
    {
        foreach ($this->statementsIds as &$statementId)
            pg_free_result($statementId);
    }

    /**
     * Get the last error message string of a connection
     *
     * @param string|null $sql The last excecuted statement. Can be null
     * @return ConnectionError The connection error 
     */
    public function error($sql)
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
        $this->error = new ConnectionError();
        $this->error->code = pg_last_error($this->connection);
        $this->error->message = pg_result_status($this->connection);
        $this->error->sql = $sql;
        return $this->error;
    }
    /**
     * Sends a request to execute a prepared statement with given parameters, 
     * and waits for the result
     *
     * @param string $sql The SQL Statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement. 
     * @return resource A query result resource on success or FALSE on failure.
     */
    public function execute($sql, $variables = null)
    {
        try {
            if (isset($variables) && is_array($variables)) {
                $result = pg_prepare($this->connection, "", $sql);
                $vars = array();
                foreach ($variables as &$value)
                    array_push($vars, $value->variable);
                $result = pg_execute($this->connection, "", $vars);
            } else
                $result = pg_query($this->connection, $sql);
            return $result ? $result : $this-> error($sql);
        } catch (Exception $e) {
            return $this-> error(sprintf(ERR_BAD_QUERY, $this->query, $e->getMessage()));
        }
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
    public function fetch_assoc($sql, $variables = null)
    {
        $statement = $this->execute($this->connection, $sql, $variables);
        if (KanojoX::is_error($statement))
            return $statement;
        else {
            array_push($this->statementsIds, $statement);
            return pg_fetch_assoc($statement);
        }
    }
}
?>