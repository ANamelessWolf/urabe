<?php

namespace Urabe\DB;

use Exception;
use Urabe\Utils\PGSQL_Result;
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
class PGKanojoX extends DBKanojoX
{
    /**
     * @var string DEFAULT_CHAR_SET
     * The default char set, is UTF8
     */
    const DEFAULT_CHAR_SET = 'utf8';
    /**
     * @var string DEFT_STMT_NAME
     * The default statement
     */
    const DEFT_STMT_NAME = "";
    /**
     * @var string $schema The pg database schema
     */
    public $schema = "";
    /**
     * Initialize a new instance of the connection object for MySQL
     * @param KanojoX $connection The connection data
     * @param MysteriousParser $parser Defines how the data is going to be parsed if,
     */
    public function __construct($connection, $parser = null)
    {
        parent::__construct(DBDriver::PG, $connection, $parser);
    }
    /**
     * Open a PG Database connection
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
            if (!isset($this->host) || strlen($host) == 0)
                $host = "127.0.0.1";
            $connString = "host='$host' port='$port' dbname='$dbname' user='$username' ";
            if (isset($passwd) && strlen($passwd) > 0)
                $connString .= "password='$passwd'";
            $this->connection = pg_connect($host, $username, $passwd, $dbname, $port);
            return $this->connection;
        } catch (Exception $e) {
            $error_msg = sprintf(ERR_BAD_CONNECTION, $e->getMessage());
            return $this->error(null, $error_msg);
        }
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
     * Gets the placeholders format for the original prepared query string. 
     * The number of elements in the array must match the number of placeholders. 
     *
     * @param int $index The place holder index if needed
     * @return string The place holder at the given position
     */
    public function get_param_place_holder($index = null)
    {
        return '$' . $index;
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
        if (is_null($error)) {
            $error = new ConnectionError();
            $error->message = pg_last_error($this->connection);
            $error->code = pg_result_status($this->connection);
            $error->sql = $sql;
            return $error;
        } else
            $this->error = $error;
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
        if (isset($variables) && is_array($variables)) {
            $result = pg_prepare($this->connection, self::DEFT_STMT_NAME, $sql);
            $sql = (object)(array(NODE_SQL => $sql, NODE_PARAMS => $variables));
            if ($result) {
                $vars = array();
                $statement = pg_execute($this->connection, self::DEFT_STMT_NAME, $variables);
            } else {
                $err = $this->error($sql, $this->get_error($result == false ? null : $result, $sql));
                throw new UrabeSQLException($err);
            }
        } else {
            $result = pg_send_query($this->connection, $sql);
            $statement = pg_get_result($this->connection);
        }
        if (!$statement || pg_result_status($statement) != PGSQL_Result::PGSQL_COMMAND_OK) {
            $err = $this->error($sql, $this->get_error($statement == false ? null : $statement, $sql));
            throw new UrabeSQLException($err);
        } else {
            array_push($this->statementsIds, $statement);
            return (new UrabeResponse())->get_execute_response(true, pg_affected_rows($statement), $sql);
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
        $result = null;
        if (!(pg_connection_status($this->connection) === PGSQL_CONNECTION_OK))
            throw new Exception(ERR_NOT_CONNECTED);
        if (isset($variables) && is_array($variables)) {
            $result = pg_prepare($this->connection, self::DEFT_STMT_NAME, $sql);
            $sql = (object)(array(NODE_SQL => $sql, NODE_PARAMS => $variables));
            if ($result) {
                $vars = array();
                foreach ($variables as &$value)
                    array_push($vars, $value);
                $ok = pg_execute($this->connection, self::DEFT_STMT_NAME, $vars);
            } else {
                $err = $this->error($sql, $this->get_error($result == false ? null : $result, $sql));
                throw new UrabeSQLException($err);
            }
        } else {
            $ok = pg_query($this->connection, $sql);
        }

        //fetch result
        if ($ok) {
            while ($row = pg_fetch_assoc($ok))
                $this->parser->parse($rows, $row);
        } else {
            $err = $this->error($sql, $this->get_error($ok == false ? null : $result, $sql));
            throw new UrabeSQLException($err);
        }
        return $rows;
    }
    /**
     * Gets the error found in a ORACLE resource object could be a
     * SQL statement error or a connection error.
     *
     * @param string $sql The SQL statement
     * @param resource $resource The SQL connection
     * @return ConnectionError The connection or transaction error 
     */
    private function get_error($resource, $sql)
    {
        $err_msg = pg_last_error($this->connection);
        $this->error = new ConnectionError();
        $this->error->code = is_null($resource) ? PGSQL_Result::PGSQL_BAD_RESPONSE : pg_result_status($resource);
        $this->error->message = $err_msg ? $err_msg : "";
        $this->error->sql = $sql;
        return $this->error;
    }
}
