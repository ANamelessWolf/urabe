<?php
/**
 * Defines error messages executed by the Enum class
 * @version 1.0.0
 * @api Makoto Urabe DB Manager Oracle
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
/**
 * @var string ERR_ENUM_INVALID_VALUE
 * The error message sent when a given value is not found in the ENUM
 */
const ERR_ENUM_INVALID_VALUE = "The value '%s', is not a valid value for the given ENUM '%s'.";
/**
 * @var string ERR_ENUM_INVALID_NAME
 * The error message sent when a given name is not defined in the ENUM
 */
const ERR_ENUM_INVALID_NAME = "The name '%s', is not defined for the given ENUM '%s'.";
?>