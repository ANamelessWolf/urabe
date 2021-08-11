<?php 
namespace Urabe\Config;
use Urabe\Utils\Enum;
/**
 * Database connection drivers supported by URABE API
 * The collection of enums that manages URABE API
 * @api Makoto Urabe DB Manager DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
abstract class DBDriver extends Enum
{
    /**
     * @var string NS
     * Not supported driver
     */
    const NS = -1;
    /**
     * @var string ORACLE
     * ORACLE driver connection with OCI Functions
     */
    const ORACLE = 0;
    /**
     * @var string PG
     * PG connection with libPQ Library
     */
    const PG = 1;
    /**
     * @var string MYSQL
     * The mysqli extension allows you to access the 
     * functionality provided by MySQL 4.1 and above.
     */
    const MYSQL = 2;
}
?>