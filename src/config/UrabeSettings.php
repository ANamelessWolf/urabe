<?php

namespace Urabe\Config;

use Urabe\Config\FieldTypeCategory;
use Urabe\Config\ServiceStatus;
use Urabe\Utils\JsonPrettyStyle;

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
class UrabeSettings
{
    /**
     * @var string Defines the type of parameters to use by the web services.
     * Available modes URL_PARAM_MODE or GET_PARAM_MODE or MIX_PARAM_MODE
     */
    public static $parameter_mode;
    /**
     * @var bool If sets to true Urabe handles errors as defined in the KanojoX Class
     */
    public static $handle_errors;
    /**
     * @var string Resources language message
     */
    public static $language;
    /**
     * @var bool If sets to true Urabe handles exceptions as defined in the KanojoX Class
     */
    public static $handle_exceptions;
    /**
     * @var bool If sets to true and Urabe handles exceptions the error details such as file, line, error code and context are showed in the response
     */
    public static $show_error_details;
    /**
     * @var bool If sets to true and Urabe handles exceptions the error context is shown in the response
     */
    public static $show_error_context;
    /**
     * @var bool If sets to true and Urabe handles exceptions the stack trace will be added to the response
     */
    public static $enable_stack_trace;
    /**
     * @var bool If sets to true add SQL statement in Urabe response. This should be enable just for testing purpose,
     * not recommendable for staging or production.
     */
    public static $add_query_to_response;
    /**
     * @var bool If sets to true hides the error code. This should be enable just for testing purpose,
     * not recommendable for staging or production.
     */
    public static $hide_exception_error;
    /**
     * @var ServiceStatus The default status for GET Service
     */
    public static $default_GET_status;
    /**
     * @var ServiceStatus The default status for POST Service
     */
    public static $default_POST_status;
    /**
     * @var ServiceStatus The default status for PUT Service
     */
    public static $default_PUT_status;
    /**
     * @var ServiceStatus The default status for DELETE Service
     */
    public static $default_DELETE_status;
    /**
     * @var JsonPrettyStyle The JSON PP Dark Style
     */
    public static $dark_pp_style;
    /**
     * @var JsonPrettyStyle The JSON PP Light Style
     */
    public static $light_pp_style;
    /**
     * @var JsonPrettyStyle The default JSON PP Style
     */
    public static $default_pp_style;
    /**
     * @var boolean True if the background is dark, otherwise it will be white
     */
    public static $default_pp_bg;
    /**
     * @var string The date format used to present dates, to modify 
     * the date format visit the url: https://secure.php.net/manual/en/function.date.php
     */
    public static $date_format;
    /**
     * @var string The path to the folder where the table definitions are stored
     */
    public static $table_definitions_path;
    /**
     * @var FieldTypeCategory The name of supported database types
     */
    public static $fieldTypeCategory;
    /**
     * @var array The list of errors
     */
    public static $errors;
    /**
     * @var int The http error code
     */
    public static $http_error_code;
}

//1: Inicialización de las configuraciónes

UrabeSettings::$parameter_mode = URL_PARAM_MODE;
UrabeSettings::$handle_errors = true;
UrabeSettings::$handle_exceptions = true;
UrabeSettings::$show_error_details = false;
UrabeSettings::$show_error_context = false;
UrabeSettings::$enable_stack_trace = false;
UrabeSettings::$add_query_to_response = true;
UrabeSettings::$hide_exception_error = false;
UrabeSettings::$default_GET_status = ServiceStatus::AVAILABLE;
UrabeSettings::$default_POST_status = ServiceStatus::BLOCKED;
UrabeSettings::$default_PUT_status = ServiceStatus::BLOCKED;
UrabeSettings::$default_DELETE_status = ServiceStatus::BLOCKED;
UrabeSettings::$dark_pp_style = JsonPrettyStyle::DarkStyle();
UrabeSettings::$light_pp_style = JsonPrettyStyle::LightStyle();
UrabeSettings::$default_pp_style = JsonPrettyStyle::DarkStyle();
UrabeSettings::$default_pp_bg = true;
UrabeSettings::$date_format = "m-d-y";
UrabeSettings::$table_definitions_path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'table_definitions' . DIRECTORY_SEPARATOR;
UrabeSettings::$fieldTypeCategory = new FieldTypeCategory();
UrabeSettings::$errors = array();
UrabeSettings::$http_error_code = null;

