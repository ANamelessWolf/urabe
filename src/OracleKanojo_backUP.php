<?php 
include_once "Kanojo.php";
include_once "Warai.php";
/**
 * A Database data struct 
 * 
 * Kanojo means girlfriend in japanase and this class saves the connection data structure used to connect to
 * an Oracle database.
 * @version 1.0.0
 * @api Makoto Urabe Oracle
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class OracleKanojoX extends KanojoX implements IKanojoX
{
    /**
     * @var string $stids 
     * The generated statements to 
     */
    private $stids;
    /**
     * Creates an Oracle connection object or returns false if the connection fails.
     * @param string $conn_str The connection string if available
     * @param boolean $throw_warnings if true warnings are thrown as exceptions errors
     * @return bool|resource The connection object or false if the connection could not be created
     */
    public function __construct($conn_str = null, $throw_warnings = false)
    {
        parent::__construct($conn_str, $throw_warnings);
    }
    /**
     * Prepares an ORACLE statement for execution
     *
     * @param stdClass $connection The database active connection
     * @param string $sql The SQL statement
     * @return stdClass Returns a statement handle on success, or FALSE on error.
     */
    public function parse($connection, $sql)
    {
        try {
            if (!is_null($sql) && strlen($sql) > 0)
                return oci_parse($connection, $this->query);
            else
                throw new Exception(ERR_EMPTY_QUERY);
        } catch (Exception $e) {
            $this->error = sprintf(ERR_BAD_QUERY, $sql, $e->getMessage());
            return false;
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
    public function fetch_asoc($sql, $variables = null)
    {
        try {
            $ok = oci_execute($statement);
            if ($ok) {
                if (is_null($row_parser))
                    oci_fetch_all($sentence, $this->result);
                else {
                    while (oci_fetch($sentence))
                        array_push($this->result, $row_parser->parse($sentence));
                }
            } else
                $this->error = sprintf(ERR_BAD_QUERY, $this->query, $e->getMessage());
            return true;
        } catch (Exception $e) {
            $this->error = sprintf(ERR_BAD_QUERY, $this->query, $e->getMessage());
            return false;
        }
    }
    /**
     * Returns the last error found
     *
     * @return array The last error found
     */
    protected function get_error($resource)
    {
        $err = oci_error($resource);
        var_dump($err);
        return $err;
    }

    /**
     * Initialize a new instance for the database connector
     * @param string|null $conn_str The parameters needed to initialize the connection object.
     * If connection string is null by default it creates a SID connection
     * @return resource The connection object type='oci8 connection' or false
     */
    protected function init_connection($conn_str = null)
    {
        if (is_null($conn_str))
            $conn_string = create_SID_connection($this->host, $this->port, $this->db_name);
        else
            $conn_string = $conn_str;
        return oci_connect($this->user_name, $this->password, $conn_string, self::DEFAULT_CHAR_SET);
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