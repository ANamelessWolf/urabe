<?php
include "ServiceStatus.php";
include "DBDriver.php";
include "JsonPrettyPrint.php";
/**
 * Urabe application settings
 *
 * In this file the application work around can be customized
 * 
 * @version 1.0.0
 * @api Makoto Urabe DB Manager database connector
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
     * @var bool If sets to true and Urabe handles exceptions the error details such as file, line, error code and context are showed in the response
     */
    "show_error_details" => true,
    /**
     * @var bool If sets to true and Urabe handles exceptions the error context is shown in the response
     */
    "show_error_context" => false,
    /**
     * @var bool If sets to true and Urabe handles exceptions the stack trace will be added to the response
     */
    "enable_stack_trace" => false,
    /**
     * @var bool If sets to true add SQL statement in Urabe response. This should be enable just for testing purpose,
     * not recommendable for staging or production.
     */
    "add_query_to_response" => true,
    /**
     * @var bool If sets to true hides the error code. This should be enable just for testing purpose,
     * not recommendable for staging or production.
     */
    "hide_exception_error" => false,
    /**
     * @var bool If sets to true, url parameters are extracted in pairs. Where the first parameter is the key and the
     * second the value.
     */
    "url_params_in_pairs" => true,
    /**
     * @var ServiceStatus The default status for GET Service
     */
    "default_GET_status" => ServiceStatus::AVAILABLE,
    /**
     * @var ServiceStatus The default status for POST Service
     */
    "default_POST_status" => ServiceStatus::BLOCKED,
    /**
     * @var ServiceStatus The default status for PUT Service
     */
    "default_PUT_status" => ServiceStatus::BLOCKED,
    /**
     * @var ServiceStatus The default status for DELETE Service
     */
    "default_DELETE_status" => ServiceStatus::BLOCKED,
    /**
     * @var JsonPrettyStyle The default JSON PP Style
     */
    "default_pp_style" => JsonPrettyStyle::DarkStyle(),
    /**
     * @var boolean True if the background is dark, otherwise it will be white
     */
    "default_pp_bg" => true,
    "field_type_category" => (object)array(
        "ParsingTypes" => array(
            "String", "Integer", "Long", "Number", "Date", "Boolean"
        ),
        "String" => array(
            //PG Types
            "character", "text"
        ),
        "Integer" => array(
            //PG Types
            "integer", "smallint"
        ),
        "Long" => array(
            //PG Types
            "bigint"
        ),
        "Number" => array(
            //PG Types
            "double precision", "numeric", "real"
        ),
        "Date" => array(
            //PG Types
            "date",
            "timestamp"
        ),
        "Boolean" => array(
            "boolean"
        )
    )
);
?>