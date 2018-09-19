<?php

/**
 * This interface allows to manage access to a Restful Service
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
interface IHasami
{
    /**
     * Gets the database manager
     *
     * @return Urabe The database manager
     */
    public function get_urabe();
    /**
     * Gets the web service request content
     *
     * @return WebServiceContent Returns the web service content
     */
    public function get_request_data();
    /**
     * Gets the table name 
     *
     * @return string Returns the table name
     */
    public function get_table_name();
    /**
     * Gets the table INSERT column names
     *
     * @return array Returns the column names in an array of strings
     */
    public function get_insert_columns();
    /**
     * Gets the column name used as primary key
     *
     * @return string Returns the column name
     */
    public function get_primary_key_column_name();
    /**
     * Gets the column name used as default filter
     *
     * @return string Returns the column name
     */
    public function get_default_filter_column_name();
}
?>