<?php
namespace Urabe\DB;
use Urabe\Config\UrabeSettings;
use Urabe\DB\FieldDefinition;
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
     * Initialize a new instance of the Table Definition
     */
    public function __construct()
    {
        $this->fields = array();
        $this->column_names = array();
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
     * Check Field data type compatibility
     *
     * @param string $dataType The data type to validate
     * @param array $dataTypes The collection of data types
     * @return Boolean True if the data types is of any of the given types
     */
    public function is_of_type($dataType, $dataTypes)
    {
        $tp = strtolower($dataType);
        foreach ($dataTypes as &$data_type)
            if (strpos($tp, $data_type) !== false)
                return true;
        return false;
    }
    /**
     * Gets the field definition used to parse a row
     *
     * @param string $newRow The row definition
     * @return FieldDefinition The field definition
     */
    public function parse_field_definition($newRow)
    {
        $tp = $newRow[TAB_DEF_TYPE];
        $dataTypes = UrabeSettings::$fieldTypeCategory;
        $max_length = is_null($newRow[TAB_DEF_CHAR_LENGTH]) ? 0 : intval($newRow[TAB_DEF_CHAR_LENGTH]);
        $scale = is_null($newRow[TAB_DEF_NUM_SCALE]) ? 0 : intval($newRow[TAB_DEF_NUM_SCALE]);
        $precision = is_null($newRow[TAB_DEF_NUM_PRECISION]) ? 0 : intval($newRow[TAB_DEF_NUM_PRECISION]);
        if ($tp == PARSE_AS_STRING || $this->is_of_type($tp, $dataTypes->StringTypes))
            $field_definition = new StringFieldDefinition($newRow[TAB_DEF_INDEX], $newRow[TAB_DEF_NAME], PARSE_AS_STRING, $max_length);
        else if ($tp == PARSE_AS_INT || $this->is_of_type($tp, $dataTypes->IntegerTypes))
            $field_definition = new NumericFieldDefinition($newRow[TAB_DEF_INDEX], $newRow[TAB_DEF_NAME], PARSE_AS_INT, $precision, $scale);
        else if ($tp == PARSE_AS_NUMBER || $this->is_of_type($tp, $dataTypes->NumberTypes))
            $field_definition = new NumericFieldDefinition($newRow[TAB_DEF_INDEX], $newRow[TAB_DEF_NAME], PARSE_AS_NUMBER, $precision, $scale);
        else if ($tp == PARSE_AS_DATE || $this->is_of_type($tp, $dataTypes->DateTypes))
            $field_definition = new DateFieldDefinition($newRow[TAB_DEF_INDEX], $newRow[TAB_DEF_NAME], PARSE_AS_DATE, UrabeSettings::$date_format);
        else if ($tp == PARSE_AS_LONG || $this->is_of_type($tp, $dataTypes->LongTypes))
            $field_definition = new NumericFieldDefinition($newRow[TAB_DEF_INDEX], $newRow[TAB_DEF_NAME], PARSE_AS_LONG, $precision, $scale);
        else if ($tp == PARSE_AS_BOOLEAN || $this->is_of_type($tp, $dataTypes->BooleanTypes))
            $field_definition = new BooleanFieldDefinition($newRow[TAB_DEF_INDEX], $newRow[TAB_DEF_NAME], PARSE_AS_BOOLEAN);
        else
            $field_definition = new FieldDefinition($newRow[TAB_DEF_INDEX], $newRow[TAB_DEF_NAME], $tp);
        $field_definition->db_type = $newRow[TAB_DEF_TYPE];
        return $field_definition;
    }
}