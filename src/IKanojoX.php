<?php

/**
 * Connetion functionality
 * Implements a database connection functionality wraps most used
 * PHP database connector functions
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
interface IKanojoX
{
    /**
     * Closes a connection
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function close();
    /**
     * Open a Database connection
     *
     * @return stdClass The database connection object
     */
    public function connect();
    /**
     * Get the last error message string of a connection
     *
     * @param string|null $sql The last excecuted statement. Can be null
     * @param ConnectionError $error If the error exists pass the eror
     * @return ConnectionError The connection error 
     */
    public function error($sql, $error);
    /**
     * Sends a request to execute a prepared statement with given parameters, 
     * and waits for the result
     *
     * @param string $sql The SQL Statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement.
     * @return stdClass A query result resource on success or FALSE on failure.
     */
    public function execute($sql, $variables = null);
    /**
     * Returns an associative array containing the next result-set row of a 
     * query. Each array entry corresponds to a column of the row. 
     * This function is typically called in a loop until it returns FALSE, 
     * indicating no more rows exist.
     *
     * @param string $sql The SQL Statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement.
     * @return array Returns an associative array. If there are no more rows in the statement then the connection error is returned.
     * */
    public function fetch_assoc($sql, $variables);
    /**
     * Frees the memory associated with a result
     *
     * @return void
     */
    public function free_result();
    /**
     * Gets the query for selecting the table definition
     *
     * @param string $table_name The table name
     * @return string The table definition selection query
     */
    public function get_table_definition_query($table_name);
}
?>