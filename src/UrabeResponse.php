<?php

/**
 * Urabe Response Class
 * 
 * This class encapsulates a service response
 * @api Makoto Urabe Oracle
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class UrabeResponse
{
    /**
     * @var mixed[] The query result data
     */
    public $result;
    /**
     * @var string|null The query error if exists
     */
    public $error;
    /**
     * @var string The SQL query
     */
    public $query;
    /**
     * @var bool The query result status
     */
    public $query_result;
    /**
     * @var int The result size
     */
    public function get_size_result()
    {
        return sizeof($this->result);
    }
    /**
     * Gets the response message for exception
     * @param string $msg The response message
     * @param stdClass|null $stack_trace The stack trace result, optional
     * @return stdClass The response message
     */
    public function get_exception_response($msg, $stack_trace = null)
    {
        return (object)(array(NODE_MSG => $msg, NODE_RESULT => array(), NODE_SIZE => 0, NODE_ERROR => $this->error));
    }
    /**
     * Gets the response message for a successful request
     *
     * @param string $msg The response message
     * @param string $result The response result
     * @param string $sql The SQL statement
     * @return object The response message
     */
    public function get_response($msg, $result, $sql = null)
    {
        $this->query = $sql;
        $count = sizeof($result);
        $response = array(NODE_MSG => $msg, NODE_RESULT => $result, NODE_SIZE => sizeof($result), NODE_ERROR => null);
        if (isset($this->query) && KanojoX::$settings->add_query_to_response)
            $response[NODE_QUERY] = $this->query;
        return (object)($response);
    }
    /**
     * Gets the response message for a successful executed query response
     *
     * @param string $succeed True if the execute query succeed
     * @param int $affected_rows The number of affected rows after a successful query
     * @param string $sql The SQL statement
     * @return object The response message
     */
    public function get_execute_response($succeed, $affected_rows, $sql = null)
    {
        $this->query = $sql;
        $count = sizeof($result);
        $response = array(NODE_SUCCEED => $succeed, NODE_AFF_ROWS => $affected_rows, NODE_RESULT => array(), NODE_ERROR => null);
        if (isset($this->query) && KanojoX::$settings->add_query_to_response)
            $response[NODE_QUERY] = $this->query;
        return (object)($response);
    }
}
?>