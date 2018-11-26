<?php

/**
 * This interface allows to manage access to a Restful Service
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
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
     * Gets the selection filter, used by the GET service
     * in its default mode
     *
     * @return string Returns the column filter
     */
    public function get_selection_filter();
    /**
     * Sets the selection filter, used by the GET service
     * in its default mode
     * @param string $condition The filter condition
     * @return string Returns the column name
     */
    public function set_selection_filter($condition);
    /**
     * Gets the service manager by the verbose type
     * @param string $verbose The service verbose type
     * @return HasamiRESTfulService The service manager
     */
    public function get_service($verbose);
    /**
     * Gets the service status assigned to the given service
     * @param string $verbose The service verbose type
     * @return ServiceStatus The service current status
     */
    public function get_service_status($verbose);
    /**
     * Sets the service status to the given service name
     * @param string $verbose The service verbose type
     * @param ServiceStatus $status The service status
     * @return void
     */
    public function set_service_status($verbose, $status);
}
?>