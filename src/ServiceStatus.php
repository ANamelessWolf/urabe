<?php 
require_once "Enum.php";
/**
 * Defines an availability status when executing a service
 * @api Makoto Urabe DB Manager DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
abstract class ServiceStatus extends Enum
{
    /**
     * @var string AVAILABLE
     * The service can be accessed without restrictions
     */
    const AVAILABLE = 0;
    /**
     * @var string BLOCKED
     * The service can be accessed
     */
    const BLOCKED = 1;
    /**
     * @var string LOGGED
     * The service can be accessed only for logged users
     */
    const LOGGED = 2;
}
?>