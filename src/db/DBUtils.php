<?php

namespace Urabe\DB;
use Exception;
use Urabe\Model\Table;
use Urabe\Model\Field;
use Urabe\Config\ConnectionError;
use Urabe\DB\FieldDefinition;
use Urabe\DB\StringFieldDefinition;
use Urabe\DB\NumericFieldDefinition;
use Urabe\DB\DateFieldDefinition;
use Urabe\DB\BooleanFieldDefinition;

/**
 * Database Utilities
 * 
 * This class encapsulates database function utilities
 * 
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class DBUtils
{
    /**
     * Creates a Field Definition object from a data type
     *
     * @param FieldDefinition $data The data type
     * @return FieldDefinition The field definition object
     */
    public static function create($data)
    {
        $tp = $data->data_type;
        if ($tp == PARSE_AS_STRING)
            $field_definition = new StringFieldDefinition($data->column_index, $data->column_name, PARSE_AS_STRING, $data->char_max_length);
        else if ($tp == PARSE_AS_INT)
            $field_definition = new NumericFieldDefinition($data->column_index, $data->column_name, PARSE_AS_INT, $data->numeric_precision, $data->numeric_scale);
        else if ($tp == PARSE_AS_NUMBER)
            $field_definition = new NumericFieldDefinition($data->column_index, $data->column_name, PARSE_AS_NUMBER, $data->numeric_precision, $data->numeric_scale);
        else if ($tp == PARSE_AS_DATE)
            $field_definition = new DateFieldDefinition($data->column_index, $data->column_name, PARSE_AS_DATE, $data->date_format);
        else if ($tp == PARSE_AS_LONG)
            $field_definition = new NumericFieldDefinition($data->column_index, $data->column_name, PARSE_AS_LONG, $data->numeric_precision, $data->numeric_scale);
        else if ($tp == PARSE_AS_BOOLEAN)
            $field_definition = new BooleanFieldDefinition($data->column_index, $data->column_name, PARSE_AS_BOOLEAN);
        $field_definition->db_type = $data->db_type;
        return $field_definition;
    }

    /**
     * Creates a Table Definition used in the Mysterious parser from a
     * selection query on the MySQL database
     *
     * @param KanojoX $conn The database connector
     * @param string $tableName The table name
     * @param string $dbName The database name
     * @return TableDefinition The Table definition
     */
    public static function createTableDefinitionFromMySQLTable($conn, $tableName, $dbName = null)
    {
        $tableDefinition = new MySQLTableDefinition($tableName, $dbName);
        $parser = new MysteriousParserForTableDefinition($tableDefinition);
        $connector = new MYSQLKanojoX($conn, $parser);
        $connector->connect();
        $fields = $connector->fetch_assoc($tableDefinition->select_query());
        $tableDefinition->table->fields = $fields;
        $tableDefinition->driver;
        $table = $tableDefinition->table;
        return TableDefinition::createFromTable($table);
    }

    /**
     * Creates a TableDefinition used in the Mysterious parser
     * from a json file. The JSON file must had the Table-Field structure
     *
     * @param string $jsonPth The path to the JSON File
     * @return TableDefinition The Table definition
     */
    public static function createTableDefinitionFromJSON($jsonPth)
    {
        if (file_exists($jsonPth)) {
            $json_string = file_get_contents($jsonPth);
            $data = json_decode($json_string, true);
            $table = new Table();
            $table->table_name = $data["table_name"];
            $table->fields = array();
            $table->driver =$data["driver"];
            foreach ($data["fields"] as $properties) {
                $field = new Field();
                foreach ($properties as $key => $value)
                    $field->{$key} = isset($value) ? $value : 0;
                array_push($table->fields, $field);
            }
            return TableDefinition::createFromTable($table);
        } else
            throw new Exception("El archivo json no existe");
    }

    /**
     * Gets the error found in a ORACLE resource object could be a
     * SQL statement error or a connection error.
     *
     * @param string $column_name The column name
     * @param mixed $selValue The selected value
     * @return ConnectionError The connection or transaction error 
     */
    public static function create_parsing_error($column_name, $selValue)
    {
        $error = new ConnectionError();
        $error->code = ERR_PARSING_VALUE_CODE;
        $error->message = sprintf(ERR_PARSING_VALUE, $column_name, $selValue);
        return $error;
    }
}
