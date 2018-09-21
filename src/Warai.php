<?php

/**
 * Defines constants and messages relative to the Urabe API.
 * @version 1.0.0
 * @api Makoto Urabe Oracle
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
/***************************************
 *********** Class Names ***************
 ***************************************/
/**
 * @var string CLASS_ERR
 * The class names used for application errors
 */
const CLASS_ERR = 'ConnectionError';
/**
 * @var string CLASS_SQL_EXC
 * The class names used for sql exceptions
 */
const CLASS_SQL_EXC = 'UrabeSQLException';
/***************************************
 **************** Errors ***************
 ***************************************/
/**
 * @var string ERR_NOT_IMPLEMENTED
 * The error message sent when method is not implemented.
 */
const ERR_NOT_IMPLEMENTED = 'The method "%s", is not implemented in the class "%s".';
/**
 * @var string ERR_BAD_INDEX
 * The error message sent when the index is out of bounds.
 */
const ERR_BAD_INDEX = 'Bad index, no such index %s in array.';
/**
 * @var string ERR_BAD_RESPONSE
 * The error message sent when the service returns a bad response
 */
const ERR_BAD_RESPONSE = 'The web service returns a bad response';
/**
 * @var string ERR_BAD_QUERY
 * The error message sent when a bad query is executed.
 */
const ERR_BAD_QUERY = 'Bad query: %s';
/**
 * @var string ERR_EMPTY_QUERY
 * The error message sent when a query is null or empty
 */
const ERR_EMPTY_QUERY = 'The query is empty or null';
/**
 * @var string ERR_BAD_TABLE
 * The error message sent when the given table does not match's its service.
 */
const ERR_BAD_TABLE = 'The table %s does not match the selected service.';
/**
 * @var string ERR_READING_JSON
 * The error message sent when a JSON file can not be parsed.
 */
const ERR_READING_JSON_FILE = 'Error reading the JSON file from "%s"';
/**
 * @var string ERR_BAD_URL
 * The error message sent when the url parameters are not sent in pairs
 */
const ERR_URL_PARAM_FORMAT = 'The url has an invalid format, URL parameters must be sent in pairs.';
/**
 * @var string ERR_BODY_IS_NULL
 * The error message sent when the the body is null.
 */
const ERR_BODY_IS_NULL = 'An error ocurred parsing the message body. The body message is null or invalid';
/**
 * @var string ERR_BODY_NOT_ARRAY
 * The error message sent when the the body is needed to be an array.
 */
const ERR_BODY_NOT_ARRAY = 'The body must be an array.';
/**
 * @var string ERR_BAD_CONNECTION
 * The error message sent when the connection is not valid.
 */
const ERR_BAD_CONNECTION = 'Invalid database connection';
/**
 * @var string ERR_CONNECTION
 * The error message sent when the connection to the database is closed.
 */
const ERR_CONNECTION_CLOSED = 'The connection to the database is closed.';
/**
 * @var string ERR_NOT_CONNECTED
 * The error message sent when the KanojoX connector is not connected.
 */
const ERR_NOT_CONNECTED = 'No connection to the database, did you use connect()?';
/**
 * @var string ERR_INCOMPLETE_DATA
 * The error message sent when the the node is missing data.
 */
const ERR_INCOMPLETE_DATA = 'The node %s does not contain enough data. Needed values [%s].';
/**
 * @var string ERR_INCOMPLETE_BODY
 * The error message sent when the the body doesn't has a property
 */
const ERR_INCOMPLETE_BODY = 'The property %s was not found in the body.';
/**
 * @var string ERR_INCOMPLETE_BODY_CONDITION
 * The error message sent when the the condition fields are missing on the body.
 */
const ERR_INCOMPLETE_BODY_CONDITION = 'The body does not contain enough data to fit the condition. Needed fields [%s].';
/**
 * @var string ERR_MISS_INSERT_FIELDS
 * The error message sent when there are no insert fields.
 */
const ERR_MISS_INSERT_FIELDS = 'No insertion fields have been defined, "table_insert_fields" is NULL.';
/**
 * @var string ERR_MISS_UPDATE_FIELDS
 * The error message sent when there are no update fields.
 */
const ERR_MISS_UPDATE_FIELDS = 'No update fields have been defined, "table_update_fields" is NULL.';
/**
 * @var string ERR_MISS_PARAM
 * The error message sent when a parameter is missing.
 */
const ERR_MISS_PARAM = "The parameter '%s' was not found on the response.";
/**
 * @var string ERR_MISS_TABLE
 * The error message sent when the table does not exists.
 */
const ERR_MISS_TABLE = "The table '%s' doesn't exists on the database.";
/**
 * @var string ERR_METHOD_NOT_DEFINED
 * The error message sent when a callback is used with no definition.
 */
const ERR_METHOD_NOT_DEFINED = 'An error occurred trying to call %s.';
/**
 * @var string ERR_NULL_BODY
 * The error message sent when the body is null.
 */
const ERR_NULL_BODY = 'An error occurred reading the message body, the body does not contain a valid JSON format.';
/**
 * @var string ERR_INVALID_SERVICE
 * The error message sent when no service name is specified.
 */
const ERR_INVALID_SERVICE = 'No service specified';
/**
 * @var string ERR_INVALID_SERVICE_TASK
 * The error message sent when trying to get a service response with no task
 */
const ERR_INVALID_SERVICE_TASK = 'No service task specified for the current service';
/**
 * @var string ERR_INVALID_SERVICE
 * The error message sent when no service name is specified.
 */
const ERR_SERVICE_RESTRICTED = 'This service can not be access via the verbose %s';
/**
 * @var string ERR_SERVICE_RESPONSE
 * The error message sent when an exception occurred during a web request
 */
const ERR_SERVICE_RESPONSE = 'Error executing the service. ';
/**
 * @var string ERR_TASK_UNDEFINED
 * The error message sent when the task is missing or not Defined.
 */
const ERR_TASK_UNDEFINED = "The given task was not found on the service";
/***************************************
 ************** CAPTIONS ***************
 ***************************************/
/**
 * @var string CAP_DELETE
 * String caption for Delete.
 */
const CAP_DELETE = 'Delete';
/**
 * @var string CAP_UPDATE
 * String caption for Update.
 */
const CAP_UPDATE = 'Update';
/**
 * @var string CAP_INSERT
 * String caption for Insert.
 */
const CAP_INSERT = 'Insert';
/**
 * @var string CAP_EXTRACT
 * String caption for Extract.
 */
const CAP_EXTRACT = 'Extract';
/**
 * @var string CAP_URABE_ACTION
 * GET variable name used to defined a web service costume call back action
 */
const CAP_URABE_ACTION = 'urabeAction';
/***************************************
 ************** JSON NODES *************
 ***************************************/
/**
 * @var string NODE_RESULT
 * The node name that saves the transaction result
 */
const NODE_RESULT = 'result';
/**
 * @var string NODE_SIZE
 * The node name that saves the result size
 */
const NODE_SIZE = 'size';
/**
 * @var string NODE_MSG
 * The node name to save the response message
 */
const NODE_MSG = 'message';
/**
 * @var string NODE_QUERY
 * The node name that saves the transaction query
 */
const NODE_QUERY = 'query';
/**
 * @var string NODE_SQL
 * The node name that saves the SQL statement
 */
const NODE_SQL = 'sql';
/**
 * @var string NODE_ERROR
 * The node name that saves the transaction error
 */
const NODE_ERROR = 'error';
/**
 * @var string NODE_SUCCEED
 * The node name that stores the query succeed status
 */
const NODE_SUCCEED = 'succeed';
/**
 * @var string NODE_AFF_ROWS
 * The node name that stores the number of affected rows
 */
const NODE_AFF_ROWS = 'affected_rows';
/**
 * @var string NODE_QUERY_RESULT
 * The node name that saves the transaction query result
 */
const NODE_QUERY_RESULT = 'query_result';
/**
 * @var string NODE_ERROR_CONTEXT
 * The node name that saves the error context
 */
const NODE_ERROR_CONTEXT = 'err_context';
/**
 * @var string NODE_FIELDS
 * The node name that saves the table field definition
 */
const NODE_FIELDS = 'fields';
/**
 * @var string NODE_KEY
 * The node name that saves an element key
 */
const NODE_KEY = 'key';
/**
 * @var string NODE_CODE
 * The node name that saves the number code
 */
const NODE_CODE = 'code';
/**
 * @var string NODE_FILE
 * The node name that saves the file path
 */
const NODE_FILE = 'file';
/**
 * @var string NODE_LINE
 * The node name that saves the file line
 */
const NODE_LINE = 'line';
/**
 * @var string NODE_STACK
 * The node name that saves the exception stack trace
 */
const NODE_STACK = 'stack_trace';
/**
 * @var string NODE_PARAMS
 * The node name that saves variables parameters
 */
const NODE_PARAMS = 'parameters';
/**
 * @var string NODE_COLS
 * The node name that stores an array of column names
 */
const NODE_COLS = 'columns';
/**
 * @var string NODE_VAL
 * The node name that stores an array of column names paired with its values
 */
const NODE_VAL = 'values';


/****************************************
 ************ URL PARAMS KEYS ************
 *****************************************/
/**
 * @var string KEY_SERVICE
 * The parameter key that defines a service name
 */
const KEY_SERVICE = 'service';
/**
 * @var string KEY_TASK
 * The parameter key that defines a service task
 */
const KEY_TASK = 'task';
/**
 * @var string KEY_PRETTY_PRINT
 * The parameter key that defines a service task
 */
const KEY_PRETTY_PRINT = 'PP';
/**
 * @var string PRETTY_PRINT_DARK
 * The parameter key that specifies a dark theme with pretty print
 */
const PRETTY_PRINT_DARK = 'Dark';
/**
 * @var string PRETTY_PRINT_LIGHT
 * The parameter key that specifies a light theme with pretty print
 */
const PRETTY_PRINT_LIGHT = 'Light';
/***************************
 ****** Parsing Types ******
 ***************************/
/**
 * @var string ORACLE_FIELD_COL_ORDER
 * The field name that stores the column name
 */
const PARSE_AS_STRING = 'String';
/**
 * Parse the value as an integer
 */
const PARSE_AS_INT = 'Integer';
/**
 * Parse the value as long
 */
const PARSE_AS_LONG = 'Long';
/**
 * Parse the value as number
 */
const PARSE_AS_NUMBER = 'Number';
/**
 * Parse the value as date
 */
const PARSE_AS_DATE = 'Date';
/************************************
 ****** Table definition nodes ******
 ************************************/
/**
 * @var string TAB_DEF_INDEX
 * The field name that stores the column index
 */
const TAB_DEF_INDEX = 'column_index';
/**
 * @var string TAB_DEF_NAME
 * The field name that stores the column name
 */
const TAB_DEF_NAME = 'column_name';
/**
 * @var string TAB_DEF_TYPE
 * The field name that stores the column data type
 */
const TAB_DEF_TYPE = 'data_type';
/**
 * @var string TAB_DEF_CHAR_LENGTH
 * The field name that stores the column max number of character length
 */
const TAB_DEF_CHAR_LENGTH = 'char_max_length';
/**
 * @var string TAB_DEF_NUM_PRECISION
 * The field name that stores the column number precision
 */
const TAB_DEF_NUM_PRECISION = 'numeric_precision';
/**
 * @var string TAB_DEF_NUM_SCALE
 * The field name that stores the column number scale
 */
const TAB_DEF_NUM_SCALE = 'numeric_scale';
/**
 * @var string TAB_NAME
 * The field name that stores the name of the table
 */
const TAB_NAME = 'table_name';
/**
 * @var string TAB_COL_FILTER
 * The column name used as filter in the selection data
 */
const TAB_COL_FILTER = 'column_filter';
/**
 * @var string TAB_INS_COLS
 * The name of the field that stores the name of the columns to insert
 */
const TAB_INS_COLS = 'ins_columns';
/******************************************
 ************ FIELD NAMES *****************
 *****************************************/
/**
 * @var string ORACLE_FIELD_COL_ORDER
 * The field name that stores the column name
 */
const ORACLE_FIELD_COL_ORDER = 'COLUMN_ID';
/**
 * @var string ORACLE_FIELD_COL_NAME
 * The field name that stores the column name
 */
const ORACLE_FIELD_COL_NAME = 'COLUMN_NAME';
/**
 * @var string ORACLE_FIELD_DATA_TP
 * The field name that stores data type
 */
const ORACLE_FIELD_DATA_TP = 'DATA_TYPE';
/**
 * @var string ORACLE_FIELD_CHAR_LENGTH
 * The field name that stores data length
 */
const ORACLE_FIELD_CHAR_LENGTH = 'CHAR_LENGTH';
/**
 * @var string ORACLE_FIELD_NUM_PRECISION
 * The field name that stores data length
 */
const ORACLE_FIELD_NUM_PRECISION = 'DATA_PRECISION';
/**
 * @var string ORACLE_FIELD_NUM_SCALE
 * The field name that stores data length
 */
const ORACLE_FIELD_NUM_SCALE = 'DATA_SCALE';
/**
 * @var string PG_FIELD_COL_ORDER
 * The field name that stores the column name
 */
const PG_FIELD_COL_ORDER = 'ordinal_position';
/**
 * @var string PG_FIELD_COL_NAME
 * The field name that stores the column name
 */
const PG_FIELD_COL_NAME = 'column_name';
/**
 * @var string PG_FIELD_DATA_TP
 * The field name that stores data type
 */
const PG_FIELD_DATA_TP = 'data_type';
/**
 * @var string PG_FIELD_CHAR_LENGTH
 * The field name that stores data length
 */
const PG_FIELD_CHAR_LENGTH = 'character_maximum_length';
/**
 * @var string PG_FIELD_NUM_PRECISION
 * The field name that stores data length
 */
const PG_FIELD_NUM_PRECISION = 'numeric_precision';
/**
 * @var string PG_FIELD_NUM_SCALE
 * The field name that stores data length
 */
const PG_FIELD_NUM_SCALE = 'numeric_scale';
/**
 * @var string MYSQL_FIELD_COL_ORDER
 * The field name that stores the column name
 */
const MYSQL_FIELD_COL_ORDER = 'ORDINAL_POSITION';
/**
 * @var string MYSQL_FIELD_COL_NAME
 * The field name that stores the column name
 */
const MYSQL_FIELD_COL_NAME = 'COLUMN_NAME';
/**
 * @var string MYSQL_FIELD_DATA_TP
 * The field name that stores data type
 */
const MYSQL_FIELD_DATA_TP = 'COLUMN_TYPE';
/**
 * @var string MYSQL_FIELD_CHAR_LENGTH
 * The field name that stores data length
 */
const MYSQL_FIELD_CHAR_LENGTH = 'CHARACTER_MAXIMUM_LENGTH';
/**
 * @var string MYSQL_FIELD_NUM_PRECISION
 * The field name that stores data length
 */
const MYSQL_FIELD_NUM_PRECISION = 'NUMERIC_PRECISION';
/**
 * @var string MYSQL_FIELD_NUM_SCALE
 * The field name that stores data length
 */
const MYSQL_FIELD_NUM_SCALE = 'NUMERIC_SCALE';
/******************************************
 ************ FUNCTION NAMES **************
 *****************************************/
/**
 * @var string F_POST
 * The name of the POST action function
 */
const F_POST = 'POST_action';
/**
 * @var string F_GET
 * The name of the GET action function
 */
const F_GET = 'GET_action';
/************************************
 ************ Settings **************
 ************************************/

/**
 * @var string GET_PARAM_MODE
 * The type of parameters that are obtains from get variables
 */
const GET_PARAM_MODE = 'GET_VARS';
/**
 * @var string URL_PARAM_MODE
 * The type of parameters that are obtains from url parameters
 */
const URL_PARAM_MODE = 'URL_PARAMETERS';
/**
 * @var string MIX_PARAM_MODE
 * Use get variables and url parameters.
 */
const GET_AND_URL_PARAM = "MIXED";
?>