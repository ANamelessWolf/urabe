# Introduction

This section descries how to use the database manager to execute SQL statements. The class `Urabe` is initialized using a `KanojoX` connector. `Urabe` database manager is oriented to a web service application and the functions returns a web service response. The class allows to select, insert, update, delete or execute whatever SQL statement that can be run using the `Urabe->query()` function.

To explain the use of this class, a table USER will be used as an example.

![User table](https://raw.githubusercontent.com/ANamelessWolf/urabe/master/testing/img/user_table.PNG)

## Initializing the database connector

This class is constructed using a `KanojoX` class, previously initialized as described in the `KanojoX` section this API supports the functionality to ORACLE, MYSQL and PG. Once this class is constructed an access to the database connector is saved in the property `Urabe::connector`.

### constructor

```php
public function __construct($connector);
```

| Name | Data type | Description |
| - | - | - |
| **`$connector`** | **KanojoX** | The previously initialize `KanojoX` connector. |

**Warnings:** An Exception is thrown if `KanojoX` is not connected or not initialized.

**Example:** Initializing a new instance of Urabe Class

```php
//1: Creates a Kanojo Object used to connect to ORACLE
$kanojo = new ORACLEKanojoX();
$kanojo->init($body->connection);
//2: Create an instance of Urabe connector
$urabe = new Urabe($kanojo);
```

## Executing a query

To simple execute any query used the function `Urabe->query`, it receives an SQL statement wit or without parameters.

```php
function query($sql, $variables = null)
```

### query() function description

| Name | Data type | Description |
| - | - | - |
| **`$sql`** | **string** | The SQL statement |
| **`$variables`** | **array** | The SQL place holder values. |

**Warnings:** An Exception is thrown if this method is called when no connection to the database is available or if the SQL statement is invalid.

**Example:** Execute an insert query

```php
//$kanojo variable has been initialized correctly
$urabe = new Urabe($kanojo);
$result = $urabe->query(
"INSERT INTO testing.users (u_name, u_pass) VALUES ($1, $2)"
array("Mike","pass123"));
echo json_encode($result);
```

**return:** The web service response as an encoding _Sting_ of `UrabeResponse` object. You can override the response message via `UrabeResponse->message` before encoding.

The encoded response is:

```json
{
  "succeed": true,
  "affected_rows": 1,
  "result": [],
  "error": null,
  "query": {
    "sql": "INSERT INTO testing.users (u_name, u_pass) VALUES ($1, $2)",
    "parameters": [
      "Mike",
      "pass123"
    ]
  }
}
```

## Getting a table definition

`Urabe` has the functionality that allows to select the table definition used to create a `MysteriousParser`. The table definition is returned as an array of `FieldDefinition`. For this example will continue using the table named _USERS_ defined as :

```php
FieldDefinition[] function get_table_definition($table_name);
```

### get_table_definition() function description

| Name | Data type | Description |
| - | - | - |
| **`$table_name`** | **string** | The name of the table  |

**Warnings:** An Exception is thrown if this method is called when no connection to the database is available or if the SQL statement is invalid.

**Example:** Select the table definition for USERS table

```php
//For this test the connector is made for PG database
$kanojo  =  new  PGKanojoX();
$kanojo->init($body->connection);
$urabe->connector->schema = "testing";
//Initializing Urabe
$urabe = new Urabe($kanojo);
$result = $urabe->get_table_definition("users");
echo json_encode($result);
```

The output result:

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
  },
  {
    "column_index": 3,
    "column_name": "u_pass",
    "data_type": "character varying",
    "char_max_length": 45,
    "numeric_precision": null,
    "numeric_scale": null
  }
]
```

## Selecting data from the database

This section describe `Urabe` Class functionality for selecting data, for the following examples will be using a table named _USERS_ defined as follows:

### select()

The default selection for the `Urabe` database manager that returns the selection as a web service response. This method is called via `Urabe->select`, this method execute an SQL selection query and parse the data as defined in the given parser, if no parser is specified it uses the parser defined in the `KanojoX::parser`. The web service response is of type `UrabeResponse`.

```php
UrabeResponse function select($sql, $variables = null, $row_parser = null);
```

#### select() function description

| Name | Data type | Description |
| - | - | - |
| **`$sql`** | **string** | The SQL statement |
| _`$variables`_ | **mixed[]** | The colon-prefixed bind variables placeholder values used in the statement in order. |
| _`$row_parser`_ | **MysteriousParser** |The way the rows are fetched via a parser callback.|

**Warnings:** An Exception is thrown if this method is called when no connection to the database is available or if the SQL statement is invalid.

**Example:** Select all users from the table user

```php
//$kanojo variable has been initialized correctly
$urabe = new Urabe($kanojo);
$result = $urabe->select("SELECT * FROM testing.users");
echo json_encode($result);
```

**return:** The web service response as an encoding _Sting_ of `UrabeResponse` object. You can override the response message via `UrabeResponse->message` before encoding.
The encoded response.

```json
{
  "message": "Data selected from user table",
  "result": [
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
  ],
  "size": 2,
  "error": null,
  "query": "SELECT * FROM testing.users"
}
```

### select_all()

An alias of select function, execute `select()` with the following parameters.

| Name | value |
| - | - |
| **`$sql`** | `sprintf('SELECT  *  FROM %s', $table_name)` |
| _`$variables`_ | null |
| _`$row_parser`_ | `$row_parser`|

```php
UrabeResponse function select_all($table_name, $row_parser = null);
```

#### select_all() function description

| Name | Data type | Description |
| - | - | - |
| **`$table_name`** | **string** | The name of the table  |
| _`$row_parser`_ | **MysteriousParser** |The way the rows are fetched via a parser callback.|

**Warnings:** An Exception is thrown if this method is called when no connection to the database is available or if the SQL statement is invalid.

**Example:** Select all users from the table user

```php
//$kanojo variable has been initialized correctly
$urabe = new Urabe($kanojo);
$result = $urabe->select_all("testing.users");
echo json_encode($result);
```

**return:** The same result as the `select()` function.

### select_one()

As the name suggest returns one value a simple string taken the first value found on the first row and firs column, If no values are selected a default value is returned.

```php
string function select_one($sql, $variables = null, $default_val = null);
```

#### select_one()  function description

| Name | Data type | Description |
| - | - | - |
| **`$sql`** | **string** | The SQL statement |
| _`$variables`_ | **mixed[]** | The colon-prefixed bind variables placeholder values used in the statement in order. |
| _`$default_val`_ | **string** |The return value if nothing is selected.|

**Warnings:** An Exception is thrown if this method is called when no connection to the database is available or if the SQL statement is invalid.

**Example:** Select the `id` for user2

```php
//$kanojo variable has been initialized correctly
$urabe = new Urabe($kanojo);
$result = $urabe->select_one("SELECT id FROM testing.users WHERE u_name = ?", array('user2'));
echo $result;
```

**return:**  The selected value as _String_ value

The output result:

```bash
2
```

### select_items()

From a SQL selection query this methods returns the values taken from the first selected column. The values are returned in an array with no associative key.

```php
string function select_items($sql, $variables = null);
```

#### select_items()  function description

| Name | Data type | Description |
| - | - | - |
| **`$sql`** | **string** | The SQL statement |
| _`$variables`_ | **mixed[]** | The colon-prefixed bind variables placeholder values used in the statement in order. |

**Warnings:** An Exception is thrown if this method is called when no connection to the database is available or if the SQL statement is invalid.

**Example:** Select the all user names(`u_name` ) from for user2

```php
//$kanojo variable has been initialized correctly
$urabe = new Urabe($kanojo);
$result = $urabe->select_items("SELECT u_name FROM testing.users");
var_dump ($result);
```

**return:**  The selected value as an array.

The output result:

```php
array(2) {
  [0]=>
  string(5) "user1"
  [1]=>
  string(5) "user2"
}
```

## Inserting data into the database

This section describe `Urabe` Class functionality for inserting data, for the following examples will be using a table named _USERS_ defined as follows:
`Urabe` has two main methods to simplify the data insertion, one execute a simple insert an the other inserts multiple records in one query.

### Content

- [insert](https://github.com/ANamelessWolf/urabe/wiki/Urabe-Class,-inserting-data#inserting-one-record)
- [insert_bulk](https://github.com/ANamelessWolf/urabe/wiki/Urabe-Class,-inserting-data#inserting-multiple-records)
- [schemas](https://github.com/ANamelessWolf/urabe/wiki/Urabe-Class,-inserting-data#schemas)

### Inserting one record

To add a single record into a given table using `Urabe`, we don't need to write the SQL Statement, instead we call a function, that receives the name of the table and the values defined as an object.

The values schema is describe in the [schema section], for inserting a new user we'll need to pass the following object.

```json
{
  "U_NAME": "Mike",
  "U_PASS": "pass123"
}
```

**Note:** The object is decoded using the PHP method `json_decode`.

```php
UrabeResponse public function insert($table_name, $values);
```

#### insert() function description

| Name | Data type | Description |
| - | - | - |
| **`$table_name`** | **string** | The table name |
| **`$values`** | **object** | The values to insert as key value pair array. |

**Warnings:** An Exception is thrown if this method is called when no connection to the database is available or if the SQL statement is invalid.

**Example:** Insert a new user to the table.

```php
//$kanojo variable has been initialized correctly
$urabe = new Urabe($kanojo);
$user = $body->insert_values;
$result = $urabe->insert("USERS",$user);
echo json_encode($result);
```

**return:** The web service response as an encoding _Sting_ of `UrabeResponse` object. You can override the response message via `UrabeResponse->message` before encoding.

The encoded response is:

```json
{
  "succeed": true,
  "affected_rows": 1,
  "result": [],
  "error": null,
  "query": {
    "sql": "INSERT INTO testing.users (u_name, u_pass) VALUES ($1, $2)",
    "parameters": [
      "Mike",
      "pass123"
    ]
  }
}
```

### Inserting multiple records

To insert more than one record you can use `insert_bulk` method that receives the table name, the column names as an array of strings and the insert values as an array of objects.

The columns

```json
[
  "U_NAME",
  "U_PASS"
]
```

The insert values

```json
[
  {
    "U_NAME": "Mike",
    "U_PASS": "pass123"
  },
  {
    "U_NAME": "nameless",
    "U_PASS": "pass123"
  }
]
```

**Note:** The object is decoded using the PHP method `json_decode`.

```php
UrabeResponse function  insert_bulk($table_name,  $columns,  $values);
```

#### insert_bulk() function description

| Name | Data type | Description |
| - | - | - |
| **`$table_name`** | **string** | The table name |
| **`$columns`** | **string[]** | The table column names as an array of strings |
| **`$values`** | **object[]** | The values to insert as key value pair array. |

**Warnings:** An Exception is thrown if this method is called when no connection to the database is available or if the SQL statement is invalid.

**Example:** Insert a two users into the table.

```php
//$kanojo variable has been initialized correctly
$urabe = new Urabe($kanojo);
$columns = array("U_NAME", "U_PASS");
$users = $body->insert_values;
$result = $urabe->insert_bulk("USERS", $users);
echo json_encode($result);
```

**return:** The web service response as an encoding _Sting_ of `UrabeResponse` object. You can override the response message via `UrabeResponse->message` before encoding.

The encoded response is:

```json
{
  "succeed": true,
  "affected_rows": 2,
  "result": [],
  "error": null,
  "query": {
    "sql": "INSERT INTO testing.users (u_name, u_pass) VALUES ($1, $2), ($3, $4)",
    "parameters": [
      "Mike",
      "pass123",
      "nameless",
      "pass123"
    ]
  }
}
```

## Updating the database

This section describe `Urabe` Class functionality for updating data, for the following examples will be using a table named _USERS_ defined as follows:

`Urabe` simplify the update process with a method that receives the update data and a condition to match.

### Updating

To update some records into a given table using `Urabe`, we don't need to write the SQL Statement, instead we call the function `update`, that receives the name of the table and the values to update and a condition.

The values to update are defined in the `Table Record Schema` schema described in the [schema section](https://github.com/ANamelessWolf/urabe/wiki/Urabe-Class,-updating-the-database#schemas), for example if we want to update the user name we just need to pass the value associated with a key.

```json
{
  "U_NAME": "Mike-sama"
}
```

**Note:** The object is decoded using the PHP method `json_decode`.

#### update() function description

```php
UrabeResponse public function update($table_name,  $values,  $condition);
```

| Name | Data type | Description |
| - | - | - |
| **`$table_name`** | **string** | The table name |
| **`$values`** | **object** | The data to update as column paired valued. |
| **`$condition`** | **string** | The SQL statement update condition. |

**Warnings:** An Exception is thrown if this method is called when no connection to the database is available or if the SQL statement is invalid.

**Example:** Update the username

```php
$urabe = new Urabe($kanojo);
$update_data = $body->update_values;
$result = $urabe->update("USERS", $update_data,"ID = 1");
echo json_encode($result);
```

**return:** The web service response as an encoding _Sting_ of `UrabeResponse` object. You can override the response message via `UrabeResponse->message` before encoding.

The encoded response is:

```json
{
  "succeed": true,
  "affected_rows": 1,
  "result": [],
  "error": null,
  "query": {
    "sql": "UPDATE testing.users SET u_name = $1 WHERE id = 1",
    "parameters": [
      "MikeUpdated"
    ]
  }
}
```

#### update_by_field() function description

`Urabe` has an alias function that calls the update condition, instead of passing the condition it receives a column name and a column value. The condition expects that the column value is equal to the given value.

```php
UrabeResponse public function update_by_field($table_name,  $values,  $column_name,  $column_value)
```

| Name | Data type | Description |
| - | - | - |
| **`$table_name`** | **string** | The table name |
| **`$values`** | **object** | The data to update as column paired valued. |
| **`$column_name`** | **string** | The name of a column of the table. |
| **`$column_value`** | **mixed** | The value to compare equals to the table name. |

**Warnings:** An Exception is thrown if this method is called when no connection to the database is available or if the SQL statement is invalid.

**Example:** Update the username

```php
$urabe = new Urabe($kanojo);
$update_data = $body->update_values;
$result = $urabe->update_by_field("USERS", $update_data,"ID", 1);
echo json_encode($result);
```

**return:** The web service response as an encoding _Sting_ of `UrabeResponse` object. You can override the response message via `UrabeResponse->message` before encoding.

The encoded response is:

```json
{
  "succeed": true,
  "affected_rows": 1,
  "result": [],
  "error": null,
  "query": {
    "sql": "UPDATE testing.users SET u_name = $1 WHERE id = 1",
    "parameters": [
      "MikeUpdated"
    ]
  }
}
```

## Delete data from the database

This section describe `Urabe` Class functionality for deleting data, for the following examples will be using a table named _USERS_ defined as follows:

`Urabe` simplify the delete process with a method that receives the condition to match in a Delete SQL statement.

### Deleting

To delete some records into a given table using `Urabe`, we don't need to write the SQL Statement, instead we call the function `delete`, and defines a delete condition.

#### delete() function description

```php
UrabeResponse public function delete($table_name, $condition);
```

| Name | Data type | Description |
| - | - | - |
| **`$table_name`** | **string** | The table name |
| **`$condition`** | **string** | The SQL statement update condition. |

**Warnings:** An Exception is thrown if this method is called when no connection to the database is available or if the SQL statement is invalid.

**Example:** Delete an user record

```php
$urabe = new Urabe($kanojo);
$result = $urabe->delete("USERS", "ID = 1");
echo json_encode($result);
```

**return:** The web service response as an encoding _Sting_ of `UrabeResponse` object. You can override the response message via `UrabeResponse->message` before encoding.

The encoded response is:

```json
{
  "succeed": true,
  "affected_rows": 1,
  "result": [],
  "error": null,
  "query": "DELETE FROM testing.users WHERE id = 1"
}
```

#### Delete by field function description

`Urabe` has an alias function that calls the delete condition, instead of passing the condition it receives a column name and a column value. The condition expects that the column value is equal to the given value.

```php
UrabeResponse public function delete_by_field($table_name, $column_name,  $column_value)
```

| Name | Data type | Description |
| - | - | - |
| **`$table_name`** | **string** | The table name |
| **`$column_name`** | **string** | The name of a column of the table. |
| **`$column_value`** | **mixed** | The value to compare equals to the table name. |

**Warnings:** An Exception is thrown if this method is called when no connection to the database is available or if the SQL statement is invalid.

**Example:** Delete an user record

```php
$urabe = new Urabe($kanojo);
$update_data = $body->update_values;
$result = $urabe->delete_by_field("USERS", "ID", 1);
echo json_encode($result);
```

**return:** The web service response as an encoding _Sting_ of `UrabeResponse` object. You can override the response message via `UrabeResponse->message` before encoding.

The encoded response is:

```json
{
  "succeed": true,
  "affected_rows": 0,
  "result": [],
  "error": null,
  "query": {
    "sql": "DELETE FROM testing.users WHERE id = $1",
    "parameters": [
      1
    ]
  }
}
```

### Schemas

The following schemas are used when inserting data, using the `Urabe Class`

#### Table record schema

```json
{
  "$schema": "http://json-schema.org/draft-07/schema#",
  "$id": "#/table-record-schema.json",
  "type": "object",
  "title": "A table record",
  "description": "The definition of a table record, pairing column name with its value",
  "patternProperties": {
    "^[a-zA-Z_$0-9]+": {
      "type": [
        "integer",
        "number",
        "string",
        "boolean",
        "null"
      ]
    }
  },
  "examples": [
    {
      "U_NAME": "Mike",
      "U_PASS": "pass123"
    },
    {
      "id": 2,
      "user_name": "Mike",
      "registration_date": null
    }
  ]
}
```

#### Column names schema

```json
{
  "$id": "#/columns-schema.json",
  "type": "array",
  "title": "The column names",
  "description": "The name of the columns",
  "items": {
    "$id": "#/properties/columns/items",
    "type": "string",
    "title": "Column name",
    "examples": [
      "id",
      "user_name",
      "registration_date"
    ],
    "pattern": "^[a-zA-Z_$0-9]"
  },
  "minItems": 1,
  "uniqueItems": true
}
```

#### Table records schema

```json
{
  "$schema": "http://json-schema.org/draft-07/schema#",
  "$id": "#/table-records-schema.json",
  "type": "array",
  "title": "A collection of table records",
  "description": "Define more than one table record",
  "items": {
    "$id": "#/properties/items/table-record",
    "type": "object",
    "title": "A table record",
    "description": "The definition of a table record, pairing column name with its value",
    "$ref": "#/table-record-schema.json"
  },
  "minItems": 1
}
```