<?php 
namespace Urabe\Utils;
/**
 * Defines the type of number to be saved
 * @api Makoto Urabe DB Manager DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
abstract class NumericType extends Enum
{
    /**
     * @var string NAN
     * Not a number
     */
    const NAN = -1;
    /**
     * @var string INTEGER
     * Defines a numeric integer
     */
    const INTEGER = 0;
    /**
     * @var string LONG
     * Defines a numeric long
     */
    const LONG = 1;
    /**
     * @var string DOUBLE
     * Defines a numeric double
     */
    const DOUBLE = 2;
}
?>