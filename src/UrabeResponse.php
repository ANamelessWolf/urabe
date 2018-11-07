<?php

/**
 * Urabe Response Class
 * 
 * This class encapsulates a service response
 * @api Makoto Urabe DB Manager Oracle
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
     * @param object|null $stack_trace The stack trace result, optional
     * @return object The response message
     */
    public function get_exception_response($msg, $stack_trace = null)
    {
        return $this->format_exception_response($msg, $this->error, $stack_trace);
    }
    /**
     * Gets the response message for exception
     * @param Exception $exc The executed exception
     * @param string|null $stack_trace The stack trace result, optional
     * @return object The response message
     */
    public function get_simple_exception_response($exc, $stack_trace = null)
    {
        $error = array(NODE_CODE => $exc->getCode(), NODE_FILE => $exc->getFile(), NODE_LINE => $exc->getLine());
        return $this->format_exception_response($exc->getMessage(), $error, $stack_trace);
    }
    /**
     * Formats the Urabe exception response
     *
     * @param string $msg The exception message
     * @param string $error The exception error definition
     * @param string $stack_trace If allowed in application settings the error $stack_trace
     * @return object The response message
     */
    private function format_exception_response($msg, $error, $stack_trace = null)
    {
        if (KanojoX::$settings->hide_exception_error)
            $error = (object)(array(NODE_MSG => $msg, NODE_RESULT => array(), NODE_SIZE => 0, NODE_ERROR => null));
        else
            $error = (object)(array(NODE_MSG => $msg, NODE_RESULT => array(), NODE_SIZE => 0, NODE_ERROR => $error));
        if (!is_null($stack_trace))
            $error->{NODE_STACK} = $stack_trace;
        return $error;
    }
    /**
     * Gets the response message for a successful request
     *
     * @param string $msg The response message
     * @param array $result The response result
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