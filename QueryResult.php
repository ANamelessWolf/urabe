<?php
include_once "Warai.php";
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
}
?>