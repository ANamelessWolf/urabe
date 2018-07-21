<?php
include "Warai.php";
/**
 * Urabe application settings
 *
 * In this file the application work around can be customized
 * 
 * @version 1.0.0
 * @api Makoto Urabe database connector
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
return (object)array(
/**
 * @var string Defines the type of parameters to use by the web services.
 * Available modes URL_PARAM_MODE or GET_PARAM_MODE or MIX_PARAM_MODE
 */
    "parameter_mode" => URL_PARAM_MODE,
    /**
     * @var bool If sets to true Urabe handles errors as defined in the KanojoX Class
     */
    "handle_errors" => true,
    /**
     * @var bool If sets to true Urabe handles exceptions as defined in the KanojoX Class
     */
    "handle_exceptions" => true,
        /**
     * @var bool If sets to true and Urabe handles exceptions the stack trace will be added to the response
     */
    "enable_stack_trace" => false,
);
?>

?>