<?php 
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
class OracleKanojoX extends KanojoX
{
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
     * Returns the last error found
     *
     * @return array The last error found
     */
    protected function get_error()
    {
        return oci_error();
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