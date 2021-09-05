<?php

namespace Urabe\DB;

use Exception;
use Urabe\Config\UrabeSettings;
use Urabe\DB\FieldDefinition;
use Urabe\Model\Table;
use Urabe\Model\Field;

/**
 * Table Definition Class
 * 
 * This class encapsulates a table column definition collection and format it values to JSON field value
 * Each table field is associated to a column and stores its index and data type.
 * 
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class TableDefinition
{
    /**
     * @var array The list of fields
     */
    private $fields;
    /**
     * @var array The list of column names
     */
    private $column_names;
    /**
     * @var array The list of required fields
     */
    private $required_column_names;
    /**
     * @var string el nombre de la tabla
     */
    public $table_name;
    /**
     * @var Table El modelo de la tabla
     */
    public $table;
    /**
     * Initialize a new instance of the Table Definition
     * @param string $driver The driver name
     * @param string $tableName The name of the table
     */
    public function __construct($tableName, $driver)
    {
        $this->fields = array();
        $this->column_names = array();
        $this->required_column_names = array();
        $this->table_name = $tableName;
        $this->table = new Table();
        $this->table->fields = array();
        $this->table->table_name = $tableName;
        $this->table->driver = $driver;
    }
    /**
     * Creates a TableDefinition used in the Mysterious parser
     * from a Table model data
     *
     * @param Table $table The Table definition data
     * @return TableDefinition The Table definition
     */
    public static function createFromTable($table)
    {
        $tableDefinition = new TableDefinition($table->table_name, $table->driver);
        $tableDefinition->table = $table;
        foreach ($table->fields as &$field) {
            $type = $field->data_type;
            $fieldDefinition = null;
            if ($type == "Integer" || $type == "Long" || $type == "Number")
                $fieldDefinition = new NumericFieldDefinition($field->column_index, $field->column_name, $type, $field->numeric_precision, $field->numeric_scale);
            else if ($type == "Date")
                $fieldDefinition = new DateFieldDefinition($field->column_index, $field->column_name, $type, UrabeSettings::$date_format);
            else if ($type == "Boolean")
                $fieldDefinition = new BooleanFieldDefinition($field->column_index, $field->column_name, $type);
            else if ($type == "String")
                $fieldDefinition = new StringFieldDefinition($field->column_index, $field->column_name, $type, $field->char_max_length);
            else
                throw new Exception("No esta soportado el tipo: " . $type);
            $fieldDefinition->required = $field->required;
            $tableDefinition->add($field->column_name, $fieldDefinition);
        }
        return $tableDefinition;
    }

    /**
     * Adds a new field to the table definition by name
     *
     * @param string $key
     * @param FieldDefinition $field The field definition to add
     * @return void
     */
    public function add($key, $field)
    {
        $this->fields[$key] = $field;
        array_push($this->column_names, $key);
        if ($field->required)
            array_push($this->required_column_names, $key);
    }
    /**
     * Check if a field name is defined on the table definition
     *
     * @param string $field_name The field name
     * @return boolean True if the field name is defined otherwise false
     */
    public function exists($key)
    {
        return array_key_exists($key, $this->fields);
    }
    /***
     * Gets the list of column names
     * @return array Get the list of column names
     */
    public function get_column_names()
    {
        return $this->column_names;
    }
    /***
     * Gets the list of required column names
     * @return array Get the list of required column names
     */
    public function get_required_column_names()
    {
        return $this->required_column_names;
    }
    /**
     * Gets the field definition by its column name
     *
     * @param string $column_name The column name
     * @return FieldDefinition The field definition
     */
    public function get_field_definition($column_name)
    {
        return $this->fields[$column_name];
    }
    /**
     * Gets the field definition by its column name
     *
     * @param string $column_name The column name
     * @param FieldDefinition $field The field definition
     */
    public function set_field_definition($column_name, $field)
    {
        $this->fields[$column_name] = $field;
    }
    /**
     * Gets the PhP type for a database data type
     *
     * @param string $data_type The database data type
     * @return String The PHP data type name
     */
    public function get_type($data_type)
    {
        $typeCategory = UrabeSettings::$fieldTypeCategory;
        $regStrings = sprintf('/%s/', implode('|', $typeCategory->StringTypes));
        $regInteger = sprintf('/%s/', implode('|', $typeCategory->IntegerTypes));
        $regLong = sprintf('/%s/', implode('|', $typeCategory->LongTypes));
        $regDouble = sprintf('/%s/', implode('|', $typeCategory->NumberTypes));
        $regDate = sprintf('/%s/', implode('|', $typeCategory->DateTypes));
        $regBoolean = sprintf('/%s/', implode('|', $typeCategory->BooleanTypes));
        preg_match($regStrings, strtolower($data_type), $matchStrings, PREG_OFFSET_CAPTURE);
        preg_match($regInteger, strtolower($data_type), $matchInteger, PREG_OFFSET_CAPTURE);
        preg_match($regLong, strtolower($data_type), $matchLong, PREG_OFFSET_CAPTURE);
        preg_match($regDouble, strtolower($data_type), $matchDouble, PREG_OFFSET_CAPTURE);
        preg_match($regDate, strtolower($data_type), $matchDate, PREG_OFFSET_CAPTURE);
        preg_match($regBoolean, strtolower($data_type), $matchBoolean, PREG_OFFSET_CAPTURE);

        if (sizeof($matchStrings) > 0)
            return "String";
        else if (sizeof($matchInteger) > 0 || sizeof($matchLong) > 0)
            return "Integer";
        else if (sizeof($matchDouble) > 0)
            return "Number";
        else if (sizeof($matchDate) > 0)
            return "Date";
        else if (sizeof($matchBoolean) > 0)
            return "Boolean";
        else
            throw new Exception("No esta soportado el tipo: " . $data_type);
    }
    /**
     * Gets the field definition used to parse a row
     *
     * @param string The selected row
     * @return Field The Field definition
     */
    public function parse_field_definition($row)
    {
        $field = new Field();
        $field->column_index = $row[TAB_DEF_INDEX];
        $field->column_name = $row[TAB_DEF_NAME];
        $field->data_type = $this->get_type($row[TAB_DEF_TYPE]);
        $field->char_max_length =  isset($row[TAB_DEF_CHAR_LENGTH]) ? $row[TAB_DEF_CHAR_LENGTH] : 0;
        $field->numeric_precision = isset($row[TAB_DEF_NUM_PRECISION]) ? $row[TAB_DEF_NUM_PRECISION] : 0;
        $field->numeric_scale = isset($row[TAB_DEF_NUM_SCALE]) ? $row[TAB_DEF_NUM_SCALE] : 0;
        $field->required = true;
        return $field;
    }
    /**
     * Save the table definition on the Urabe settings
     *
     * @return void
     */
    public function save()
    {
        $tableName = $this->table_name;
        $file_path = UrabeSettings::$table_definitions_path . DIRECTORY_SEPARATOR . "$tableName.json";
        if (!file_exists(UrabeSettings::$table_definitions_path))
            mkdir(UrabeSettings::$table_definitions_path, 0777, true);
        if (file_put_contents($file_path, json_encode($this->table, JSON_PRETTY_PRINT)) == false)
            throw new Exception(ERR_SAVING_JSON);
    }
}
