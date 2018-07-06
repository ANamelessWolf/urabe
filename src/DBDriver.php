<?php 
/**
 * Database connection drivers supported by URABE API
 * The collection of enums that manages URABE API
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
abstract class DBDriver extends Enum
{
    /**
     * @var string ORACLE
     * Manage ORACLE connection with OCI8 Functions
     */
    const ORACLE = 0;
    /**
     * @var string PG
     * Manage PG connection with libpq Library
     */
    const PG = 1;
    /**
     * The mysqli extension allows you to access the 
     * functionality provided by MySQL 4.1 and above.
     */
    const MYSQL = 2;
}
?>