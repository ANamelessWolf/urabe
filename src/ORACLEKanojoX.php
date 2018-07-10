<?php 
include_once "KanojoX.php";
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
class ORACLEKanojoX extends KanojoX
{
    /**
     * @var string DEFAULT_CHAR_SET
     * The default char set, is UTF8
     */
    const DEFAULT_CHAR_SET = 'AL32UTF8';
    /**
     * Open an ORACLE Database connection
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
            if (!isset($host) || strlen($host) == 0)
                $host = "127.0.0.1";
            $connString = $this->buildConnectionString($host, $dbname, $port);
            $this->connection = oci_connect($username, $passwd, $connString, DEFAULT_CHAR_SET);
            return $this->connection;
        } catch (Exception $e) {
            return $this->error(sprintf(ERR_BAD_CONNECTION, $e->getMessage()));
        }
    }
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
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function close()
    {
        $this->free_result();
        return oci_close($this->connection);
    }
    /**
     * Frees the memory associated with a result
     *
     * @return void
     */
    public function free_result()
    {
        foreach ($this->statementsIds as &$statementId)
            oci_free_statement($statementId);
    }
    /**
     * Get the last error message string of a connection
     *
     * @param string|null $sql The last excecuted statement. Can be null
     * @return ConnectionError The connection error 
     */
    public function error($sql)
    {
        $e = oci_error($this->connection);
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
     * @param string $sql The SQL Statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement. 
     * @return resource Returns a statement handle on success, or FALSE on error.
     */
    public function execute($sql, $variables = null)
    {
        try {
            $statement = $this->parse($this->connection, $sql);
            if (isset($variables) && is_array($variables))
                $this->bind($statement, $variables);
            $ok = oci_execute($statement);
            return $ok ? $statement : $this->error($sql);
        } catch (Exception $e) {
            return $this->error(sprintf(ERR_BAD_QUERY, $this->query, $e->getMessage()));
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
        $statement = $this->execute($sql, $variables);
        if (KanojoX::is_error($statement))
            return $statement;
        else {
            array_push($this->statementsIds, $statement);
            return oci_fetch_assoc($statement);
        }
    }
    /**
     * Prepares sql_text using connection and returns the statement identifier, 
     * which can be used with oci_execute(). 
     *
     * @param string $sql The SQL text statement
     * @return resource Returns a statement handle on success, or FALSE on error. 
     */
    private function parse($sql)
    {
        return oci_parse($this->connection, $sql);
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