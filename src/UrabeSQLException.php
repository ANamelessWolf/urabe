<?php
class UrabeSQLException extends Exception
{
    /**
     * @var string $sql The SQL statement text. If there was no statement, this is an empty string. 
     */
    public $sql;
    /**
     * Initialize a new instance of an Urabe SQL Exception
     *
     * @param ConnectionError $error The connection error
     */
    public function __construct($error)
    {
        parent::__construct(sprintf(ERR_BAD_QUERY, $error->message), $error->code);
        $this->sql = $error->sql;
    }
}
?>