<?php

namespace Urabe\DB;

use Exception;
use Urabe\DB\DBKanojoX;
use Urabe\Config\DBDriver;
use Urabe\Config\ConnectionError;
use Urabe\Runtime\UrabeSQLException;
use Urabe\Service\UrabeResponse;

/**
 * A MySQL Connection object
 * 
 * Kanojo means girlfriend in japanese and this class saves the connection data structure used to connect to
 * an MySQL database.
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class ORACLEKanojoX extends DBKanojoX
{
    /**
     * @var string DEFAULT_CHAR_SET
     * The default char set, is UTF8
     */
    const DEFAULT_CHAR_SET = 'AL32UTF8';
    /**
     * @var string ERR_CODE
     * The OCI Error field for error code
     */
    const ERR_CODE = 'code';
    /**
     * @var string ERR_MSG
     * The OCI Error field for error message
     */
    const ERR_MSG = 'message';
    /**
     * @var string ERR_SQL
     * The OCI Error field for error SQL
     */
    const ERR_SQL = 'sqltext';
    /**
     * Initialize a new instance of the connection object for MySQL
     * @param KanojoX $connection The connection data
     * @param MysteriousParser $parser Defines how the data is going to be parsed if,
     */
    public function __construct($connection, $parser = null)
    {
        parent::__construct(DBDriver::ORACLE, $connection, $parser);
    }
    /**
     * Open a MySQL Database connection
     *
     * @return ConnectionError The database connection object
     */
    public function connect()
    {
        try {
            $host = $this->kanojo->host;
            $port = $this->kanojo->port;
            $dbname = $this->kanojo->db_name;
            $username = $this->kanojo->user_name;
            $passwd = $this->kanojo->password;
            if (!isset($host) || strlen($host) == 0)
                $host = "127.0.0.1";
            $connString = $this->buildConnectionString($host, $dbname, $port);
            $this->connection = oci_connect($username, $passwd, $connString, self::DEFAULT_CHAR_SET);
            if ($this->connection)
                $this->connection->set_charset(self::DEFAULT_CHAR_SET);
            return $this->connection;
        } catch (Exception $e) {
            $error_msg = sprintf(ERR_BAD_CONNECTION, $e->getMessage());
            return $this->error(null, $error_msg);
        }
    }
    /**
     * This function builds a connection string to connect to ORACLE
     * by default is connected via SID
     *
     * @return string The connection string
     */
    public function buildConnectionString($host, $dbname, $port)
    {
        return $this->create_SID_connection($host, $dbname, $port);
    }
    /**
     * Closes a connection
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function close()
    {
        $this->free_result();
        if (!$this->connection)
            throw new Exception(ERR_NOT_CONNECTED);
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
     * @param string|null $sql The last executed statement. Can be null
     * @param ConnectionError $error If the error exists pass the error
     * @return ConnectionError The connection error 
     */
    public function error($sql, $error = null)
    {
        if (is_null($error))
            $this->error = $this->get_error($this->connection);
        else
            $this->error = $error;
        return $this->error;
    }
    /**
     * Gets the error found in a ORACLE resource object could be a
     * SQL statement error or a connection error.
     *
     * @param resource $resource The SQL statement or SQL connection
     * @return ConnectionError The connection or transaction error 
     */
    private function get_error($resource)
    {
        $e = oci_error($resource);
        $this->error = new ConnectionError();
        $this->error->code = $e[self::ERR_CODE];
        $this->error->message = $e[self::ERR_MSG];
        return $this->error;
    }
    /**
     * Sends a request to execute a prepared statement with given parameters, 
     * and waits for the result
     *
     * @param string $sql The SQL Statement
     * @param array|null $variables The colon-prefixed bind variables placeholder used in the statement, can be null.
     * @throws Exception An Exception is raised if the connection is null or executing a bad query
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    public function execute($sql, $variables = null)
    {
        if (!isset($this->connection))
            throw new Exception(ERR_NOT_CONNECTED);
        $statement = $this->parse($this->connection, $sql);
        $class = get_resource_type($statement);
        if ($class == CLASS_ERR)
            throw (!is_null($statement->sql) ? new UrabeSQLException($this->error($sql)) : new Exception($statement->error, $statement->errno));
        else {
            if (isset($variables) && is_array($variables))
                $this->bind($statement, $variables);
            $ok = oci_execute($statement);
            if ($ok) {
                array_push($this->statementsIds, $statement);
                return (new UrabeResponse())->get_execute_response(true, oci_num_rows($statement), $sql);
            } else {
                $err = $this->error($sql, $this->get_error($statement));
                throw new UrabeSQLException($err);
            }
        }
    }
    /**
     * Returns an associative array containing the next result-set row of a 
     * query. Each array entry corresponds to a column of the row. 
     *
     * @param string $sql The SQL Statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement.
     * @throws Exception An Exception is thrown parsing the SQL statement or by connection error
     * @return array Returns an associative array. 
     * */
    public function fetch_assoc($sql, $variables = null)
    {
        $rows = array();
        if (!$this->connection)
            throw new Exception(ERR_NOT_CONNECTED);
        $statement = $this->parse($this->connection, $sql, $variables);
        $class = get_resource_type($statement);
        if ($class == CLASS_ERR)
            throw (!is_null($statement->sql) ? new UrabeSQLException($this->error($sql)) : new Exception($statement->error, $statement->errno));
        else {
            array_push($this->statementsIds, $statement);
            $ok = oci_execute($statement);
            if ($ok) {
                while ($row = oci_fetch_assoc($statement))
                    $this->parser->parse($rows, $row);
            } else {
                $err = $this->error($sql, $this->get_error($statement));
                throw new UrabeSQLException($err);
            }
        }
        return $rows;
    }
    /**
     * Prepares sql_text using connection and returns the statement identifier, 
     * which can be used with execute(). 
     * @param mysqli $link MySQL active connection
     * @param string $sql The SQL text statement
     * @return mysqli_stmt Returns a statement handle on success, 
     * or a connection Error. 
     */
    private function parse($link, $sql, $variables = null)
    {
        if (!$link)
            throw new Exception(ERR_NOT_CONNECTED);
        $statement = oci_parse($link, $sql);
        if ($statement && isset($variables) && is_array($variables))
            $this->bind($statement, $variables);
        $result = $statement ? $statement : $this->error($sql);
        if (is_a($result, 'Urabe\Config\ConnectionError'))
            throw new UrabeSQLException($result);
        return $result;
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
        foreach ($variables as &$value)
            oci_bind_by_name($statement, ":" . $value->bv_name, $value->variable);
    }
    /**
     * Creates a SID connection to an ORACLE database
     * @param string $host The connection host address
     * @param string $SID The database name or Service ID
     * @param string $port Oracle connection port
     * @return string The oracle connection string
     */
    private function create_SID_connection($host, $SID, $port)
    {
        $strConn = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = $host)(PORT = $port)))(CONNECT_DATA=(SID=$SID)))";
        return $strConn;
    }
}
