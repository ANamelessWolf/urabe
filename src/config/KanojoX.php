<?php
namespace Urabe\Config;
/**
 * Database connection model
 * 
 * Kanojo means girlfriend in japanese and this class saves the connection data
 * @version 1.0.0
 * @api Makoto Urabe DB Manager database connector
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
abstract class KanojoX
{ 
    /**
     * @var DBDriver The database driver
     */
    public $db_driver;
    /**
     * @var string $host Can be either a host name or an IP address.
     */
    public $host = "127.0.0.1";
    /**
     * @var string $port Connection port
     */
    public $port;
    /**
     * @var string $db_name The database name.
     */
    public $db_name;
    /**
     * @var string $user_name The database connection user name.
     */
    public $user_name;
    /**
     * @var string $password The connection password 
     */
    public $password = "";
}
?>