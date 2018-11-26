<?php
/**
 * Defines application constants
 *
 * @package URABE-API
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @version v.1.1 (01/10/2019)
 * @copyright copyright (c) 2018-2020, Nameless Studios
 */
require_once "WaraiMessages_en.php";
require_once "EnumErrorMessages_en.php";
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
 ************** CAPTIONS ***************
 ***************************************/
/** String caption for Delete */
const CAP_DELETE = 'delete';
/**
 * @var string CAP_UPDATE
 * String caption for Update.
 */
const CAP_UPDATE = 'update';
/**
 * @var string CAP_INSERT
 * String caption for Insert.
 */
const CAP_INSERT = 'insert';
/**
 * @var string CAP_EXTRACT
 * String caption for Extract.
 */
const CAP_EXTRACT = 'Extract';
/**
 * @var string CAP_URABE_ACTION
 * The method prefix name used to define the methods that can be called
 * via a web service
 */
const CAP_URABE_ACTION = 'u_action_';
/**
 * @var string VAR_URABE_ACTION
 * GET variable name used to defined a web service costume call back action
 */
const VAR_URABE_ACTION = 'uAction';
/**
 * @var string CAP_GET_VARS
 * String caption for Get variables
 */
const CAP_GET_VARS = 'GET variables';
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
/**
 * @var string NODE_CONDITION
 * The node name that stores an SQL statement condition
 */
const NODE_CONDITION = 'condition';

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
 * @var string PARSE_AS_STRING
 * The field name that stores the column name
 */
const PARSE_AS_STRING = 'String';
/**
 * @var string PARSE_AS_INT
 * Parse the value as an integer
 */
const PARSE_AS_INT = 'Integer';
/**
  * @var string PARSE_AS_LONG
  * Parse the value as long
 */
const PARSE_AS_LONG = 'Long';
/**
  * @var string PARSE_AS_NUMBER
  * Parse the value as number
 */
const PARSE_AS_NUMBER = 'Number';
/**
  * @var string PARSE_AS_DATE
  * Parse the value as date
 */
const PARSE_AS_DATE = 'Date';
/**
  * @var string PARSE_AS_BOOLEAN
  * Parse the value as boolean
 */
const PARSE_AS_BOOLEAN = 'Boolean';
/**
  * @var string PARSING_TYPES
  * The name of the parsing types row
 */
const PARSING_TYPES = 'ParsingTypes';
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