<?php

namespace Urabe;

use Urabe\HasamiWebService;
use Urabe\Config\ServiceStatus;

class HasamiViewWebService extends HasamiWebService
{
    /**
     * Initialize a new instance for the Hasami view web service
     * The name of the table is used to find the table definition.
     * The path the table definition is defined on the UrabeSettings variable
     * table_definitions_path
     *
     * @param KanojoX $connection The database connection
     * @param string $table_name The name of the table
     * @param string $primary_key The primary key column name
     */
    public function __construct($connection, $table_name, $primary_key = null)
    {
        parent::__construct($connection, $table_name, $primary_key);
        //Set web service accesibility
        $this->init_services(ServiceStatus::LOGGED, ServiceStatus::BLOCKED, ServiceStatus::BLOCKED, ServiceStatus::BLOCKED);        
    }
}