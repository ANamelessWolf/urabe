<?php 
include_once "KanojoX.php";
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
class MYSQLKanojoX extends KanojoX
{
    /**
     * @var string DEFAULT_CHAR_SET
     * The default char set, is UTF8
     */
    const DEFAULT_CHAR_SET = 'utf8';
    /**
     * Initialize a new instance of the connection object
     */
    public function __construct()
    {
        parent::__construct();
        $this->db_driver = DBDriver::MYSQL;
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
        return mysqli_close($this->connection);
    }
    /**
     * Frees the memory associated with a result
     *
     * @return void
     */
    public function free_result()
    {
        foreach ($this->statementsIds as &$statementId)
            mysqli_free_result($statementId);
    }
    /**
     * Open a MySQL Database connection
     *
     * @return stdClass The database connection object
     */
    public function connect()
    {
        try {
            $host = $this->host;
            $port = $this->port;
            $dbname = $this->db_name;
            $username = $this->user_name;
            $passwd = $this->password;
            $this->connection = mysqli_connect($host, $username, $passwd, $dbname, $port);
            if ($this->connection)
                $this->connection->set_charset(self::DEFAULT_CHAR_SET);
            return $this->connection;

        } catch (Exception $e) {
            return error(sprintf(ERR_BAD_CONNECTION, $e->getMessage()));
        }
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
            $error->code = $this->connection->connect_errno;
            $error->message = $this->connection->error;
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
        if (!$this->connection)
            throw new Exception(ERR_NOT_CONNECTED);
        $statement = $this->parse($this->connection, $sql, $variables);
        $class = get_class($statement);
        if ($class == CLASS_ERR)
            throw (!is_null($statement->sql) ? new UrabeSQLException($this->error($sql)) : new Exception($statement->error, $statement->errno));
        else {
            $ok = $statement->execute();
            if ($ok) {
                array_push($this->statementsIds, $statement);
                return (new UrabeResponse())->get_execute_response(true, $statement->affected_rows, $sql);
            } else
                throw new UrabeSQLException($this->error($sql));
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
        $class = get_class($statement);
        if ($class == CLASS_ERR)
            throw (!is_null($statement->sql) ? new UrabeSQLException($this->error($sql)) : new Exception($statement->error, $statement->errno));
        else {
            array_push($this->statementsIds, $statement);
            $ok = $statement->execute();
            if ($ok) {
                $result = $statement->get_result();
                while ($row = $result->fetch_assoc())
                    KanojoX::$parser->parse($rows, $row);
            } else
                throw new UrabeSQLException($this->error($sql));
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
        $statement = $link->prepare($sql);
        if ($statement && isset($variables) && is_array($variables))
            $this->bind($statement, $variables);
        return $statement ? $statement : $this->error($sql);
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
        $format = "";
        $parameters = array();
        foreach ($variables as &$value) {
            if (is_int($value))
                $tp = "i";
            else if (is_double($value))
                $tp = "d";
            else if (is_string($value))
                $tp = "s";
            else
                $tp = "b";
            $format .= $tp;
        }
        array_push($parameters, $format);
        foreach ($variables as &$value)
            array_push($parameters, $value);
        return call_user_func_array(array($statement, 'bind_param'), $this->refValues($parameters));
    }
    /**
     * Converts an array in to a referenced values
     *
     * @param array $arr The array to referenced
     * @return array The referenced values
     */
    function refValues($arr)
    {
        if (strnatcmp(phpversion(), '5.3') >= 0) //Reference is required for PHP 5.3+
        {
            $refs = array();
            foreach ($arr as $key => $value)
                $refs[$key] = &$arr[$key];
            return $refs;
        }
        return $arr;
    }
    /**
     * Gets the query for selecting the table definition
     *
     * @param string $table_name The table name
     * @return string The table definition selection query
     */
    public function get_table_definition_query($table_name)
    {
        $fields = MYSQL_FIELD_COL_ORDER . ", " . MYSQL_FIELD_COL_NAME . ", " . MYSQL_FIELD_DATA_TP . ", " .
            MYSQL_FIELD_CHAR_LENGTH . ", " . MYSQL_FIELD_NUM_PRECISION . ", " . MYSQL_FIELD_NUM_SCALE;
        if (isset($this->schema)) {
            $schema = $this->schema;
            $sql = "SELECT $fields FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME` = '$table_name' AND `TABLE_SCHEMA` = '$this->db_name'";
        } else
            $sql = "SELECT $fields FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME` = '$table_name'";
        return $sql;
    }
    /**
     * Gets the table definition parser for the MySQL connector
     *
     * @return array The table definition fields as an array of FieldDefinition
     */
    public function get_table_definition_parser()
    {
        $fields = array(
            MYSQL_FIELD_COL_ORDER => new FieldDefinition(0, MYSQL_FIELD_COL_ORDER, PARSE_AS_INT),
            MYSQL_FIELD_COL_NAME => new FieldDefinition(1, MYSQL_FIELD_COL_NAME, PARSE_AS_STRING),
            MYSQL_FIELD_DATA_TP => new FieldDefinition(2, MYSQL_FIELD_DATA_TP, PARSE_AS_STRING),
            MYSQL_FIELD_CHAR_LENGTH => new FieldDefinition(3, MYSQL_FIELD_CHAR_LENGTH, PARSE_AS_INT),
            MYSQL_FIELD_NUM_PRECISION => new FieldDefinition(4, MYSQL_FIELD_NUM_PRECISION, PARSE_AS_INT),
            MYSQL_FIELD_NUM_SCALE => new FieldDefinition(5, MYSQL_FIELD_NUM_SCALE, PARSE_AS_INT)
        );
        return $fields;
    }
    /**
     * Gets the table definition mapper for the MySQL connector
     *
     * @return array The table mapper as KeyValued<String,String> array
     */
    public function get_table_definition_mapper()
    {
        $map = array(
            MYSQL_FIELD_COL_ORDER => TAB_DEF_INDEX,
            MYSQL_FIELD_COL_NAME => TAB_DEF_NAME,
            MYSQL_FIELD_DATA_TP => TAB_DEF_TYPE,
            MYSQL_FIELD_CHAR_LENGTH => TAB_DEF_CHAR_LENGTH,
            MYSQL_FIELD_NUM_PRECISION => TAB_DEF_NUM_PRECISION,
            MYSQL_FIELD_NUM_SCALE => TAB_DEF_NUM_SCALE
        );
        return $map;
    }
}
?>