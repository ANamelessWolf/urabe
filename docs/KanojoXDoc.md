# Introduction

`KanojoX` acts as **Urabe** core, it wraps most common functions of a database and unifies them allowing to work transparently between database without changing our code.

The `KanojoX` class is an abstract class must be inherit by each supported driver, currently the application has compatibility with **ORACLE**, **PG** and **MYSQL**. The connector is composed by a group of connection variables, a module with database basic functionality, a result parser, a database model mapper and an error handler.

## Connection variables

To create a connection each `KanojoX` driver implements a different connection method, that uses all or some of the following variables.

| Access | Var | Data type | Description |
| -------- | ---- | ----------- |--------|
| public | **$host** | _string_ | Can be either a host name or an IP address |
| public | **$port** | _int _ | The connection port |
| public | **$db_name** | _string_ | The database name |
| public | **$user_name** | _string_ | The connection user name |
| public | **$password** | _string_ | The connection password |

This variables can be initialize calling the `KanojoX::init` method. This method received a decoded JSON object

```php
void public function init($body_json);
```

### init() function description

| Name | Data type | Description |
| - | - | - |
| **`$body_json`** | **object** | The request body as decoded JSON object |

**Warnings:** An Exception is thrown if `$body_json` parameter is null
**returns:** _void_

## Content

The content of the class is divided in the following links click any topic for details.

1. [Database basic functionality](https://github.com/ANamelessWolf/urabe/wiki/KanojoX-Class,-database-functionality)
2. [Result parser](https://github.com/ANamelessWolf/urabe/wiki/KanojoX-Class,-result-parser)
3. [Database model mapper](https://github.com/ANamelessWolf/urabe/wiki/KanojoX-Class,-model)
4. [Error and Exception Handling](https://github.com/ANamelessWolf/urabe/wiki/KanojoX-Class,-error-and-exeception-handling)

## Database basic functionality

`KanojoX` defines the connector basic functionality, the connector wraps the driver connection actions and database manipulation.

### Database basic functionality Content

- [connect](https://github.com/ANamelessWolf/urabe/wiki/KanojoX-Class,-database-functionality#connect)
- [close](https://github.com/ANamelessWolf/urabe/wiki/KanojoX-Class,-database-functionality#close)
- [free_result](https://github.com/ANamelessWolf/urabe/wiki/KanojoX-Class,-database-functionality#free_result)
- [execute](https://github.com/ANamelessWolf/urabe/wiki/KanojoX-Class,-database-functionality#execute)
- [fetch_assoc](https://github.com/ANamelessWolf/urabe/wiki/KanojoX-Class,-database-functionality#fetch_assoc)
- [error](https://github.com/ANamelessWolf/urabe/wiki/KanojoX-Class,-database-functionality#error)

### connect()

Initialize a database connection using the default connection settings. When calling this method, expects connection variables are already loaded, to ensure all properties are loaded is recommended initialize them with the `KanojoX::init` method . Once the variables are initialized this method opens a database connection and returns the connection object or the connection resource.

```php
object|resource|ConnectionError public function connect();
```

#### connect() function description

| Name | Data type | Description |
| -------- | - | - |
| **NONE** | - | - |

**Example:** Connect to ORACLE

```php
//1: A new instance of KanojoX of type ORACLEKanojoX
$kanojo  =  new  ORACLEKanojoX();
$kanojo->owner  =  $body->owner;
//2: Initialize the connection before calling the connect method
$kanojo->init($body->connection);
//3: Connect to the Database
$conn  =  $kanojo->connect();
```

**return:** The connection object or a `ConnectionError` if the connection data is not valid to open the connection.

**Note:** Once connected, the current connection can be accessed via the connection property `KanojoX->connection`.

### close()

This method closes the current connection and calls `free_result()` method, it's recommended used only when multiple connections exists and the connection wants to be closed before the object is destructed. By default the `__destruct` method executes this method, if the object was already closed, the `__destruct` does nothing.

```php
bool public function close();
```

#### close() function description

| Name | Data type | Description |
| -------- | - | - |
| **NONE** | - | - |

**Example:** Close the connection to ORACLE

```php
$is_closed =  $kanojo->close();
```

**return:** True if the connection is closed otherwise false.

**Warnings:** An Exception is thrown if this method is called when no connection to the database is available.

### free_result()

Frees stored result memory for the given statements. Methods like `fetch_assoc()` and `execute()` create stored result memory.

```php
void public free_result();
```

#### free_result() function description

| Name | Data type | Description |
| -------- | - | - |
| **NONE** | - | - |

**Example:** Free stored result memory

```php
$is_closed =  $kanojo->free_result();
```

**return:** _void_

### execute()

Execute a prepared statement with or without parameters, waits for the result and returns it as a web service response of type `UrabeResponse`.

```php
UrabeResponse public execute($sql, $variables = null);
```

#### execute() function description

| Name | Data type | Description |
| ------- | ------ | - |
| **`sql`** | **string** | The SQL Statement to execute |
| _`$variables`_ | **mixed[]** | The colon-prefixed bind variables placeholder values used in the statement in order. |

**Example:** Inserts a new row

```php
$kanojo  =  new  ORACLEKanojoX();
$result = $kanojo->execute("INSERT INTO TABLE_NAME(ID, COLUMN_NAME) VALUES(?,?)",
array(1, "data"));
echo json_encode($result);
```

**return:** The query result as a web service response

The echo output:

```json
{
  "succeed": true,
  "affected_rows": 1,
  "result": [],
  "error": null,
  "query": {
    "sql": "INSERT INTO TABLE_NAME(ID, COLUMN_NAME) VALUES(?,?)",
    "parameters": [
      1,
      "data"
    ]
  }
}
```

### fetch_assoc()

Execute a selection query with or without placeholders, waits for the result and returns an associative array containing the resulting set. Each row entry is parsed using the `KanojoX->parser` property, by default the parser return its values associative `column_name>>column_value`.

```php
array public fetch_assoc($sql, $variables = null);
```

#### fetch_assoc() function description

| Name | Data type | Description |
| ------- | ------ | - |
| **`sql`** | **string** | The SQL Statement to execute |
| _`$variables`_ | **mixed[]** | The colon-prefixed bind variables placeholder values used in the statement in order. |

**Example:** Selects some data

```php
$kanojo  =  new  ORACLEKanojoX();
$result = $kanojo->fetch_assoc("SELECT * FROM USER");
echo json_encode($result);
```

**return:** The selected data with the parser format

```json
[
  {
    "id": "1",
    "u_name": "user1",
    "u_pass": "pass123"
  },
  {
    "id": "2",
    "u_name": "user2",
    "u_pass": "pass124"
  }
]
```

### error()

Gets the last error message caused by the current connection or a prepare statement. The message is formatted using fields configurable in application settings. This function is used internally but has to be implemented in each connection driver.

To select the last error call the method `Kanojo->get_last_error()`.

#### error() function description

| Name | Data type | Description |
| ------- | ------ | - |
| **`sql`** | **string** | The SQL Statement to execute |
| _`$error`_ | **ConnectionError** | The connection error that cause this error |

```php
ConnectionError public error($sql, $error= null);
```

**Example:** Print some error

```php
trigger_error('Trigger Error', E_USER_WARNING);
return  $kanojo->get_last_error();
```

**return:** The connection error

The output result

```json
{
  "code": 512,
  "message": "Trigger Error",
  "sql": null,
  "file": "C:\\xampp\\htdocs\\urabe\\testing\\utils\\KanojoXTestUtils.php",
  "line": 106
}
```

## Result parser

`KanojoX` defined a property of type `MysteriousParser` that allows to returns the fetched values as defined in the `MysteriousParser` callback.

| Access | Var | Data type | Description |
| -------- | ---- | ----------- |--------|
| public | **$parser** | _MysteriousParser _ | Defines  the way the result data is parsed |

As soon the class is constructed a default parser is defined

```php
public function __construct() {
  $this->parser = new MysteriousParser();
}
```

The parser can be changed at any moment before a `fetch_assoc` function call, if you see the body of any implemented `fetch_assoc` you will notice the following line.

For example let see the code for the class `PGKanojoX->fetch_assoc`

```php
$rows  =  array(); //Here the selected rows are stored
...
while ($row  =  pg_fetch_assoc($ok))
  $this->parser->parse($rows, $row);
...
return $rows;
```

As you can see the parser receive the fetched `$row` parse it values with the method `MysteriousParser->parse` and store the parsed result in the row collection `$rows`.

### MysteriousParser Class

This class defines a parser used in the associative fetch of some SQL selection query result. This class contemplates two ways to parsed data with a table definition or with some special customization.

Lets call this parser methods

- The Table definition method
- The Special customization method

Optional while parsing the column names can be renamed using  a column map array that defines the column name mapping. The mapped values are passed as a key value pair, where the key is the database column name and the value is the desired name.

#### MysteriousParser properties

| Access | Var | Data type | Description |
| -------- | ---- | ----------- |--------|
| public | **$table_definition** | _FieldDefinition[]_ | The table columns definitions as an array of `FieldDefinition`. |
| public | **$column_map** | _string[] _ | Defines how the columns are mapped to the message response, if null the columns maintains the database column names. This values are case sensitive |
| public | **$parse_method** | _callback_ | The parse method, defined as an anonymous function |

#### Initializing the parser

The constructor create two default parse methods depending on the constructor input parameters.

##### __construct()

| Name | Data type | Description |
| ------- | ------ | - |
| _`$table_definition`_ | **FieldDefinition[]** | The table fields definition.  |

```php
public  function  __construct($table_definition  =  null)
```

#### Table definition method

When table definition is presented in the constructor the fetched data is parsed using the `parse_with_field_definition` function.

```php
//Table definition method
public  function  __construct($table_definition  =  null){
...
  $this->parse_method  =  function ($parser, &$result,  $row) {
    $this->parse_with_field_definition($result, $row);
  };
...
}
```

The table definition method parses only the columns defined in the table definition array and map the column names if `column_map` is available.

##### parse_with_field_definition() function description

| Name | Data type | Description |
| ------- | ------ | - |
| _`$result`_ | **array[]** | The collection of rows where the parsed rows are stored  |
| _`$row`_ | **mixed[]** | The selected row picked from the fetch assoc process.

```php
void public function parse_with_field_definition(&$result, $row);
```

**Example:**

We have the following table definition

```json
[
  {
    "column_index": 1,
    "column_name": "id",
    "data_type": "integer",
    "char_max_length": null,
    "numeric_precision": 32,
    "numeric_scale": 0
  },
  {
    "column_index": 2,
    "column_name": "u_name",
    "data_type": "character varying",
    "char_max_length": 45,
    "numeric_precision": null,
    "numeric_scale": null
  }
]
```

While fetching we select the following row

```php
$row = array("id"=>115, "u_name"=>"Mike", "pass"=>"u_pass123", "register_year"=>2018);
```

With no mapping the parsed row will return

```php
$row = array("id"=>115, "u_name"=>"Mike");
```

Defining the following column mapping the result will be

```php
//Define the mapping updating the Mysterious parser column_map property
$parser->column_map = array("id"=>"user_id", "u_name"=>"user_name");
...
$row = array("user_id"=>115, "user_name"=>"Mike");
```

#### The Special customization method

When table definition is not presented in the constructor the fetched data is parsed using the callback stored in the property `$parse_method`.

The callback `MysteriousParser->parse_method` has the following body:

| Name | Data type | Description |
| ------- | ------ | - |
| _`$parser`_ | **MysteriousParser** | Reference the parser instance, that is executing the parsing callback  |
| _`&$result`_ | **array[]** | The row collection, where the parsed rows are stored  |
| _`$row`_ | **mixed[]** | The selected row picked from the fetch assoc process.

```php
function ($parser, &$result, $row);
```

By default the constructor defined the following parser

```php
MysteriousParser->parse_method = function ($parser, &$result, $row) {
    array_push($result, $row);
};
```

**Note:** Column mapping only is available in costume parser, and is accessed by `$parser->column_map`. A simple example for using a costume parser with column mapping.

```php
MysteriousParser->column_map = array("id"=>"user_id", "u_name"=>"user_name");
MysteriousParser->parse_method = function ($parser, &$result, $row) {
  $mapping = $parser->column_map;
  $mapped_row = array();
  foreach ($row  as  $column_name  =>  $column_value){
    $key  =  $mapping[$column_name];
    $mapped_row[$key] =$column_value;
  }
    array_push($result, $mapped_row);
};
```

## Database model mapper

The connector implements the functionality to select automatically the table definition, implementing the driver definition. The following methods are used internally, defined in each `KanojoX` connection driver.

### get_table_definition_query()

Gets the selection query for selecting the table definition

| Access | Var | Data type | Description |
| -------- | ---- | ----------- |--------|
| public | **$table_name** | _string_ | The table name. |

```php
string public function get_table_definition_query($table_name);
```

**Example:** For example the return selection query for the table `users` in ORACLE is

```sql
SELECT 'COLUMN_ID', 'COLUMN_NAME', 'DATA_TYPE',
'CHAR_LENGTH', 'DATA_PRECISION', 'DATA_SCALE'
FROM ALL_TAB_COLS
WHERE TABLE_NAME = 'users' AND OWNER = 'public'
```

**return:** The selection query

### get_table_definition_parser()

Gets the table definition parser for the database connector, the column names are mapped and the mapped column names are defined in `Warai.php`.

| Access | Var | Data type | Description |
| -------- | ---- | ----------- |--------|
| public | **NONE** | ----------- | -----------  |

```php
FieldDefinition[] public function get_table_definition_parser();
```

**return:** The array of field definition

A similar encoded output

```json
[
  {
    "column_index": 1,
    "column_name": "id",
    "data_type": "integer",
    "char_max_length": null,
    "numeric_precision": 32,
    "numeric_scale": 0
  },
  {
    "column_index": 2,
    "column_name": "u_name",
    "data_type": "character varying",
    "char_max_length": 45,
    "numeric_precision": null,
    "numeric_scale": null
  }
]
```

### get_table_definition_mapper()

Gets the table definition mapper for the database connector and returns an associative array.

| Access | Var | Data type | Description |
| -------- | ---- | ----------- |--------|
| public | **NONE** | ----------- | -----------  |

```php
string[] public function get_table_definition_mapper()
```

**Example:** Default column mapping for `ORACLEKanojoX` class.

```php
$map  =  array(
  ORACLE_FIELD_COL_ORDER  =>  TAB_DEF_INDEX,
  ORACLE_FIELD_COL_NAME  =>  TAB_DEF_NAME,
  ORACLE_FIELD_DATA_TP  =>  TAB_DEF_TYPE,
  ORACLE_FIELD_CHAR_LENGTH  =>  TAB_DEF_CHAR_LENGTH,
  ORACLE_FIELD_NUM_PRECISION  =>  TAB_DEF_NUM_PRECISION,
  ORACLE_FIELD_NUM_SCALE  =>  TAB_DEF_NUM_SCALE
);
return  $map;
```

## Error and Exception Handling

`Urabe` manage errors and exception using `KanojoX`, unhandled exceptions and non fatal error as returned as web service response. Errors and Exception handling are configure in the configuration file, [UrabeSettings.php](https://github.com/ANamelessWolf/urabe/blob/master/src/UrabeSettings.php).

### Configuration settings

The following setting configure the web service response format. To modify the current configuration setting it can be access from the static property `KanojoX::$settings`.

| Settings |  Type | Default |  Description |
| -------- | ------------ | -------- | ------------ |
| **handle_errors** | _bool_  | `true` | If sets to true `Urabe` handles errors as defined in the `KanojoX` Class |
| **handle_exceptions** | _bool_  | `true` | If sets to true `Urabe` handles exceptions as defined in the `KanojoX` Class |
| **show_error_details** | _bool_  | `true` | If sets to true and `Urabe` handles exceptions, the error details such as file, line, error code and context are shown in the response |
| **show_error_context** | _bool_  | `false` | If sets to true and `Urabe` handles exceptions, the error context is shown in the response |
| **enable_stack_trace** | _bool_  | `false` | If sets to true and `Urabe` handles exceptions, the stack trace will be added to the response |
| **add_query_to_response** | _bool_  | `true` | If sets to true `Urabe` adds the last executed SQL statement in to the response |
| **hide_exception_error** | _bool_  | `false` | If sets to true and `Urabe` adds the last executed error to the service response |

### Error Handler

`KanojoX` function `error_handler` is used to handling errors during run time, it catches a none fatal error or when triggering an error under certain conditions ([trigger_error()](https://php.net/manual/en/function.trigger-error.php)). By default the errors are saved as `ConnectionError` objects and stored in the `KanojoX::$errors` static property.

To access the last error:

```php
ConnectionError public function get_last_error();
```

Accessing the error from any class

```php
$err_index = sizeof(KanojoX::$errors) - 1;   //Selecting last error
$error = KanojoX::$errors[$err_index];
```

**Example:** Triggering and retrieving the last executed error.

```php
trigger_error('Trigger Error', E_USER_WARNING);
$error = $kanojo->get_last_error();
```

### Exception Handler

`KanojoX` treats unhandled exception as a `400 HTTP RESPONSE`, when an exception is executed in any part of the API, the method `KanojoX->exception_handler` is called. Allowing `KanojoX` to handle `exceptions` make easier to debug and manage errors in the view.

**Example:**  This is a message response from an SQL exception with full details.

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

**Example:**  This is a message response from an SQL exception formatted for a staging environment.

```json
{
  "message": "Bad query: ORA-00942: table or view does not exist",
  "result": [],
  "size": 0,
  "error": null
}
```