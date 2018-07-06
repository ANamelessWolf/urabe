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
     * @var int $code 
     * The last error code number.
     */
    public $code;
    /**
     * @var string $message The database connection error text. 
     */
    public $message = "127.0.0.1";
    /**
     * @var string $sql The SQL statement text. If there was no statement, this is an empty string. 
     */
    public $sql;
}
?>