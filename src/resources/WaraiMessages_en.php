<?php
/**
 * Defines info and error messages relative to the Urabe API.
 * @version 1.0.0
 * @api Makoto Urabe DB Manager Oracle
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
/***************************************
 **************** Info *****************
 ***************************************/
/**
 * @var string INF_SELECT
 * The message response for a successful query.
 */
const INF_SELECT = 'Selection succeed';
/***************************************
 **************** Error ****************
 ***************************************/
/**
 * @var string ERR_SAVING_JSON
 * The error message sent when an error ocurred saving a JSON object
 */
const ERR_SAVING_JSON = 'An error ocurred saving the JSON object';
/**
 * @var string ERR_NOT_IMPLEMENTED
 * The error message sent when method is not implemented.
 */
const ERR_NOT_IMPLEMENTED = 'The method "%s", is not implemented in the class "%s".';
/**
 * @var string ERR_BAD_RESPONSE
 * The error message sent when the service returns a bad response
 */
const ERR_BAD_RESPONSE = 'The web service returns a bad response';
/**
 * @var string ERR_BAD_QUERY
 * The error message sent when a bad query is executed.
 */
const ERR_BAD_QUERY = 'Bad query: %s.';
/**
 * @var string ERR_READING_JSON_FILE
 * The error message sent when a JSON file can not be parsed.
 */
const ERR_READING_JSON_FILE = "Error reading the JSON file from '%s'.";
/**
 * @var string ERR_BAD_URL
 * The error message sent when the url can be parsed
 */
const ERR_BAD_URL = 'The url has an invalid format.';
/**
 * @var string ERR_BODY_IS_NULL
 * The error message sent when the the body is null.
 */
const ERR_BODY_IS_NULL = 'An error ocurred parsing the message body. The body message is null or invalid.';
/**
 * @var string ERR_BAD_CONNECTION
 * The error message sent when the connection is not valid.
 */
const ERR_BAD_CONNECTION = 'Invalid database connection.';

/**
 * @var string ERR_NOT_CONNECTED
 * The error message sent when the KanojoX connector is not connected.
 */
const ERR_NOT_CONNECTED = 'No connection to the database, did you use connect()?';
/**
 * @var string ERR_INCOMPLETE_DATA
 * The error message sent when the the node is missing data.
 */
const ERR_INCOMPLETE_DATA = 'The %s does not contain enough data. Needed values [%s].';
/**
 * @var string ERR_INCOMPLETE_BODY
 * The error message sent when the the body doesn't has a property
 */
const ERR_INCOMPLETE_BODY = 'The properties [%s] were not found in the body.';
/**
 * @var string ERR_MISSING_CONDITION
 * The error message sent when the the condition is not defined
 */
const ERR_MISSING_CONDITION = 'A condition is needed to %s';
/**
 * @var string ERR_INVALID_SERVICE
 * The error message sent when no service name is specified.
 */
const ERR_INVALID_SERVICE = 'No service specified.';
/**
 * @var string ERR_INVALID_SERVICE_TASK
 * The error message sent when trying to get a service response with no task
 */
const ERR_INVALID_SERVICE_TASK = 'No service task specified for the current service.';
/**
 * @var string ERR_INVALID_ACTION
 * The error message sent when trying to call a not implemented action
 */
const ERR_INVALID_ACTION = 'No action is implemented in this web service with the name %s. ';
/**
 * @var string ERR_INVALID_SERVICE
 * The error message sent when no service name is specified.
 */
const ERR_SERVICE_RESTRICTED = 'This service can not be access via the verbose %s.';
/**
 * @var string ERR_VERBOSE_NOT_SUPPORTED
 * The error message sent when the request method is not supported
 */
const ERR_VERBOSE_NOT_SUPPORTED = 'This service does not support the verbose %s.';
/**
 * @var string ERR_SERVICE_RESPONSE
 * The error message sent when an exception occurred during a web request
 */
const ERR_SERVICE_RESPONSE = 'Error executing the service.';
?>