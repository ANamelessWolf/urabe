<?php 
namespace Urabe\Utils;
use Urabe\Utils\Enum;
/**
 * Database connection drivers supported by URABE API
 * The collection of enums that manages URABE API
 * @api Makoto Urabe DB Manager DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
abstract class PGSQL_Result extends Enum
{
    /**
     * @var string PGSQL_EMPTY_QUERY
     * Result code for empty query
     */
    const PGSQL_EMPTY_QUERY = 0;
    /**
     * @var string PGSQL_COMMAND_OK
     * Result code for command OK
     */
    const PGSQL_COMMAND_OK = 1;
    /**
     * @var string PGSQL_TUPLES_OK
     * Result code for tuples 
     */
    const PGSQL_TUPLES_OK = 2;
    /**
     * @var string PGSQL_COPY_TO
     * Result code for copy to
     */
    const PGSQL_COPY_TO = 3;
    /**
     * @var string PGSQL_COPY_FROM
     * Result code for copy from
     */
    const PGSQL_COPY_FROM = 4;
    /**
     * @var string PGSQL_BAD_RESPONSE
     * Result code for bad response
     */
    const PGSQL_BAD_RESPONSE = 5;
    /**
     * @var string PGSQL_NONFATAL_ERROR
     * Result code for non fatal error 
     */
    const PGSQL_NONFATAL_ERROR = 7;
    /**
     * @var string PGSQL_FATAL_ERROR
     * Result code for fatal error
     */
    const PGSQL_FATAL_ERROR = 8;
}
?>