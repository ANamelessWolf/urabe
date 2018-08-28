
# Using KanojoX as a database connector

`KanojoX` acts as **Urabe** core, it wraps most common functions of a database and unifies them allowing to work transparently between database without changing our code.

The `KanojoX` class is an abstract class must be inherit by each supported driver, currently the application has compatibility with **ORACLE**, **PG** and **MYSQL**. The connector is composed by a group of connection variables, a module with database basic functionality, a result parser, a database model mapper and an error handler.

## Connection variables

To create a connection each `KanojoX` driver implements a different connection method, that uses all or some of the following variables.

```php
public $host;       //@var string — Can be either a host name or an IP address.
public $port;       //@var int — The connection port
public $db_name;    //@var string — The database name
public $user_name;  //@var string — The database user name
public $password;   //@var string — The user name connection password
```

This variables can be initialize calling the `init` method. This method received a decoded JSON object

```php
/*
* @param object $body_json — The request body as decoded JSON object
*/
void public function init($body_json);
```

## Database basic functionality

- **connect** — Opens a database connection and returns the connection object or the connection resource

```php
object public function connect();
```

- **close** — Close the current connection and calls `free_result()` function

```php
void public function close();
```

- **free_result()** — Frees stored result memory for the given statements. Some functions like `fetch_assoc()` and `execute()` create statements.

```php
void public free_result();
```

- **execute(string $sql, mixed[] $variables = null)** — Sends a request to execute a prepared statement with given parameters, waits for the result and returns the service response as a **UrabeResponse**.

```php
/*
* @param string $sql The SQL Statement to execute
* @param array $variables The variables to be bind in the prepare statement place holders
*/
UrabeResponse public execute($sql, $variables = null);
```

- **fetch_assoc(string $sql, mixed[] $variables=null)** — Execute a selection query with or without placeholders, waits for the result and returns an associative array containing the result-set row. Each array entry corresponds to a column of the row and is parsed as defined by the `KanojoX->parser` variable, by default the result is returned associative `column_name>>column_value`.

```php
/*
* @param string $sql The SQL Statement to execute
* @param array $variables The variables to be bind in the prepare statement place holders
*/
array public fetch_assoc($sql, $variables = null);
```

- **error(object|resource $sender, ConnectionError $error = null)** — Gets the last error message caused by the current connection or a prepare statement. The message is formatted using the executes SQL and other fields configurable in application settings.

```php
/*
* @param string $sql The error source, can be the connection object or the prepare statement
* @param array $error If the error is caused by another error, send the error.
*/
ConnectionError public error($sender, $error = null);
```

## Result parser

`KanojoX` defined a variable of type `MysteriousParser` that allows to returns the fetched values as defined in the `MysteriousParser` callback.

```php
public $parser; //@var MysteriousParser — Set the way the fetch result is parsed
```

The parser is initialized in the class constructor and can be change before a database `fetch_assoc()` call.

```php
public function __construct() {
...
$this->parser = new MysteriousParser();
...
}
```

The parser callback `MysteriousParser->parse_method` is defined with the following body:

```php
/*
* @param MysteriousParser $parser The parser who is executing the parse_method callback
* @param array $result The array where parsed rows are stored
* @param array $row The next row from a query as an associative array
*/
function ($parser, &$result, $row);
```

By default the parser stored the $row associative in the result.

```php
MysteriousParser->parse_method = function ($parser, &$result, $row) {
    array_push($result, $row);
};
```

## Database model mapper

The connector also has the functionality to selects the table definition, each driver define the following methods.

- **get_table_definition_query($table_name)** — Gets the query for selecting the table definition

```php
/*
* @param string $table_name The table name
*/
string public function get_table_definition_query($table_name);
```

- **get_table_definition_parser()** — Gets the table definition parser for the database connector

```php
MysteriousParser public function get_table_definition_parser();
```

- **get_table_definition_mapper()** — Gets the table definition mapper for the database connector and returns an associative array as `KeyValued<string,string>[]`

```php
array public function get_table_definition_mapper()
```

### Data mapped

The data is mapped using the Mysterious parser and generated table definition mapper that looks like this JSON object example of the first mapped row.

```json
{
    "column_index": 1,
    "column_name": "ID",
    "data_type": "VARCHAR2",
    "char_max_length": 15,
    "numeric_precision": null,
    "numeric_scale": null
}
```

## Error and Exception Handling

By default `KanojoX` manage unhandled exceptions and error as web service response. To disable errors and use PHP default configuration, change the following entries in the application settings file [UrabeSettings.php](https://github.com/ANamelessWolf/urabe/blob/nameless-dev/src/UrabeSettings.php).

```php
/**
* @var bool If sets to true Urabe handles errors, setting a new handler via set_error_handler function
*/
"handle_errors" => true,
/**
* @var bool If sets to true Urabe handles exceptions, setting a new handler via set_exception_handler function
*/
"handle_exceptions" => true,
```

### Error Handler

`KanojoX` function `error_handler` is used to handling errors during runtime, catch when an error happens, or when triggering an error under certain conditions (using [trigger_error()](https://php.net/manual/en/function.trigger-error.php)). By default the errors are saved as `ConnectionError` objects and stored in the `KanojoX::$errors` static property.

To access the last error:

```php
ConnectionError public function get_last_error();
```

Or access the error by static way

```php
$err_index = sizeof($errors) - 1;   //Selecting last error
$error = KanojoX::$errors[$err_index];
```

Here an example of how to trigger and retrieve the last error.

```php
trigger_error('Trigger Error', E_USER_WARNING);
$error = $kanojo->get_last_error();
```

### Exception Handler

`KanojoX` treats unhandled exception as a `400 HTTP RESPONSE`, when an exception is executed in any part of the API, the method `KanojoX->exception_handler` is called. Allowing `KanojoX` to handle `exceptions` make easier to debug and manage errors in the view mode.

Exception error message can be configured changing the following settings variables:

```php
/**
* @var bool If sets to true The error details such as file name, line, error code and context are showed in the response
*/
"show_error_details" => true,
/**
* @var bool If sets to true the error context is shown in the response
*/
"show_error_context" => true,
/**
* @var bool If sets to the stack trace will be added to the response
*/
"enable_stack_trace" => false,
/**
* @var bool If sets to true add SQL statement in Urabe response.
*/
"add_query_to_response" => true,
/**
* @var bool If sets to true hides the error from response
*/
"hide_exception_error" => false
```

This is an example exception response when all error details are available.

```json
{
  "message": "Bad query: ORA-00942: table or view does not exist",
  "result": [],
  "size": 0,
  "error": {
    "query": "SELECT * FROM table_name WHERE id = 1",
    "code": 942,
    "file": "C:\\xampp\\htdocs\\urabe\\src\\ORACLEKanojoX.php",
    "line": 177,
    "err_context": [
      {
        "error": {
          "message": "oci_execute(): ORA-00942: table or view does not exist",
          "code": 2,
          "file": "C:\\xampp\\htdocs\\urabe\\src\\ORACLEKanojoX.php",
          "line": 171,
          "err_context": {
            "sql": "SELECT * FROM table_name WHERE id = 1",
            "variables": null,
            "rows": [],
            "class": "oci8 statement"
          }
        }
      }
    ]
  },
  "stack_trace": "#0 C:\\xampp\\htdocs\\urabe\\testing\\KanojoXTestUtils.php(29): ORACLEKanojoX->fetch_assoc('SELECT ADDR_ID,...')\n#1 C:\\xampp\\htdocs\\urabe\\testing\\KanojoXTester.php(28): test_fetch_assoc_no_params(Object(ORACLEKanojoX), Object(stdClass))\n#2 {main}"
}
```

The same response formatted to a staging environment.

```json
{
  "message": "Bad query: ORA-00942: table or view does not exist",
  "result": [],
  "size": 0,
  "error": null
}
```