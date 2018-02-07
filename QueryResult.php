<?php
include_once "Warai.php";
include_once "MysteriousParser.php";
/**
 * Query Result Class
 * 
 * This class encapsulates a selection query result.
 * This class treats the database fields types in three types; strings, dates and numbers.
 * @api Makoto Urabe Oracle
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class QueryResult
{
    /**
     * @var mixed[] The query result data
     */
    public $result;
    /**
     * @var bool The query result status
     */
    public $query_result;
    /**
     * @var string The SQL query
     */
    public $query;
    /**
     * @var string|null The query error if exists
     */
    public $error;
    /**
     * Initialize a new instance for a query result.
     */
    function __construct()
    {
        $this->result = array();
        $this->query_result = false;
    }
    /**
     * Encode this instance result in to a JSON string
     *
     * @return string The JSON String
     */
    public function encode()
    {
        return json_encode($this);
    }
    /**
     * Prepare an Oracle sentence to be excecuted
     *
     * @param resource $conn The Oracle connection object
     * @return resource|bool The Oracle sentence or False if an error is found. 
     */
    public function oci_parse($conn)
    {
        try {
            if (!is_null($this->query) && strlen($this->query) > 0)
                return oci_parse($conn, $this->query);
            else
                throw new Exception(ERR_EMPTY_QUERY);
        } catch (Exception $e) {
            $this->error = sprintf(ERR_BAD_QUERY, $this->query, $e->getMessage());
            return false;
        }
    }
    /**
     * Prepare an Oracle sentence to be excecuted
     *
     * @param resource $sentence The Oracle sentence
     * @param MysteriousParser $row_parser Defines the row parsing task. 
     * @return bool True if the sentence is executed with no problems 
     */
    public function fetch($sentence, $row_parser = null)
    {
        try {
            oci_execute($sentence);
            if (is_null($row_parser))
                oci_fetch_all($sentence, $this->result);
            else {
                while (oci_fetch($sentence))
                    array_push($this->result, $row_parser->parse($sentence));
            }
            return true;
        } catch (Exception $e) {
            $this->error = sprintf(ERR_BAD_QUERY, $this->query, $e->getMessage());
            return false;
        }
    }
}
?>