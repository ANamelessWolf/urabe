<?php

/**
 * An Connection database error
 * 
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class ConnectionError
{
    /**
     * @var int $code The last error code number.
     */
    public $code;
    /**
     * @var string $message The database connection error text. 
     */
    public $message;
    /**
     * @var string $sql The SQL statement text. If there was no statement, this is an empty string. 
     */
    public $sql;
    /**
     * @var string $file The file where the error was found
     */
    public $file;
    /**
     * @var int The line where the error was found
     */
    public $line;
    /**
     * @var array The error context
     */
    public $context;
    /**
     * @var array The generated stack trace
     */
    public $stack_trace;
}
?>