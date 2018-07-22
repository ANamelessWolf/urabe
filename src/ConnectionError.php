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
    public $err_context;
    /**
     * Formats an exception error
     *
     * @return array The exception error is a mixed array
     */
    public function get_exception_error()
    {
        $err_context = array();
        foreach (KanojoX::$errors as &$error)
            array_push(
            $err_context,
            array(NODE_ERROR => array(
                NODE_CODE => $error->code,
                NODE_FILE => $error->file,
                NODE_LINE => $error->line,
                NODE_ERROR_CONTEXT => $error->err_context
            ))
        );
        return array(NODE_CODE => $this->code, NODE_FILE => $this->file, NODE_LINE => $this->line, NODE_ERROR_CONTEXT => $err_context);
    }
}
?>