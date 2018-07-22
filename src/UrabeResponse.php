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
     * @return stdClass The response
     */
    public function get_exception_response($msg, $stack_trace = null)
    {
        return (object)(array(NODE_MSG => $msg, NODE_RESULT => array(), NODE_SIZE => 0, NODE_ERROR => $this->error));
    }
}
?>