<?php
include_once "StringFieldDefinition.php";
include_once "NumericFieldDefinition.php";
include_once "DateFieldDefinition.php";
include_once "BooleanFieldDefinition.php";
/**
 * Mysterious parser class
 * 
 * This class parses a row from a table definition
 * @version 1.0.0
 * @api Makoto Urabe DB Manager Oracle
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class MysteriousParser
{
    public $id;
    /**
     * @var array The table fields definition as an array of FieldDefinition.
     */
    public $table_definition;
    /**
     * @var array Defines how the columns are mapped to the message response, if null
     * the columns maintains the database column names. The values are passed as a key value pair, where the
     * first value is the database column name and the second the message field name.
     * This values are case sensitive
     */
    public $column_map;

    /**
     * Defines the result parsing method, this function receives a row and
     * an array where the data should be putted.
     *
     * @var callback The parse method, passed as an anonymous function
     */
    public $parse_method;
    /**
     * Specifies the class used to called the parsing methods
     *
     * @var object The main class used to call the parsing method
     */
    private $caller;
    /**
     * __construct
     *
     * Initialize a new instance of the Mysterious parser.
     * @param FieldDefinition[] $table_definition The table fields definition.
     * When table definition is presented the fetched data is parsed using the parse_with_field_definition function 
     */
    public function __construct($table_definition = null, $caller = null, $parse_method = "")
    {
        $this->caller = isset($caller) ? $caller : $this;
        $this->table_definition = $table_definition;
        if (isset($table_definition) && !isset($caller))
            $this->parse_method = "parse_with_field_definition";
        else if (isset($caller)) {
            $this->parse_method = $parse_method;
        } else {
            $this->parse_method = "simple_parse";
        }
        $this->id = hash("md5", $this->parse_method . spl_object_hash($this));
    }
    /**
     * Gets the sender description
     *
     * @param mixed $context Extra data used by this sender
     * @return array The sender data as a key value paired array with the keys {caller, method, id, context}
     */
    private function get_sender($context = null)
    {
        return array("caller" => get_class($this->caller), "method" => $this->parse_method, "id" => $this->id, "context" => is_null($context) ? "" : $context);
    }
    /**
     * Check if a field name is defined on the table definition
     *
     * @param string $field_name The field name
     * @return boolean True if the field name is defined otherwise false
     */
    public function is_defined($field_name)
    {
        return array_key_exists($field_name, $this->table_definition);
    }
    /**
     * Parse the fetch assoc result by the parse_method callback definition
     *
     * @param array $result The result row to parse
     * @param array $row The selected row picked from the fetch assoc process.
     * @return void
     */
    public function parse(&$result, $row)
    {
        if (is_string($this->parse_method))
            $this->caller->{$this->parse_method}($this, $result, $row);
        else
            call_user_func_array($this->parse_method, array($this, &$result, $row));
    }
    /**
     * Gets the field definition used to parse a row
     *
     * @param string $newRow The row definition
     * @return FieldDefinition The field definition
     */
    public function get_parsing_data($newRow)
    {
        $tp = $newRow[TAB_DEF_TYPE];
        $dataTypes = KanojoX::$settings->field_type_category;
        $max_length = is_null($newRow[TAB_DEF_CHAR_LENGTH]) ? 0 : intval($newRow[TAB_DEF_CHAR_LENGTH]);
        $scale = is_null($newRow[TAB_DEF_NUM_SCALE]) ? 0 : intval($newRow[TAB_DEF_NUM_SCALE]);
        $precision = is_null($newRow[TAB_DEF_NUM_PRECISION]) ? 0 : intval($newRow[TAB_DEF_NUM_PRECISION]);
        if ($tp == PARSE_AS_STRING || $this->is_of_type($tp, $dataTypes->String))
            $field_definition = new StringFieldDefinition($newRow[TAB_DEF_INDEX], $newRow[TAB_DEF_NAME], PARSE_AS_STRING, $max_length);
        else if ($tp == PARSE_AS_INT || $this->is_of_type($tp, $dataTypes->Integer))
            $field_definition = new NumericFieldDefinition($newRow[TAB_DEF_INDEX], $newRow[TAB_DEF_NAME], PARSE_AS_INT, $precision, $scale);
        else if ($tp == PARSE_AS_NUMBER || $this->is_of_type($tp, $dataTypes->Number))
            $field_definition = new NumericFieldDefinition($newRow[TAB_DEF_INDEX], $newRow[TAB_DEF_NAME], PARSE_AS_NUMBER, $precision, $scale);
        else if ($tp == PARSE_AS_DATE || $this->is_of_type($tp, $dataTypes->Date))
            $field_definition = new DateFieldDefinition($newRow[TAB_DEF_INDEX], $newRow[TAB_DEF_NAME], PARSE_AS_DATE, KanojoX::$settings->date_format);
        else if ($tp == PARSE_AS_LONG || $this->is_of_type($tp, $dataTypes->Long))
            $field_definition = new NumericFieldDefinition($newRow[TAB_DEF_INDEX], $newRow[TAB_DEF_NAME], PARSE_AS_LONG, $precision, $scale);
        else if ($tp == PARSE_AS_BOOLEAN || $this->is_of_type($tp, $dataTypes->Boolean))
            $field_definition = new BooleanFieldDefinition($newRow[TAB_DEF_INDEX], $newRow[TAB_DEF_NAME], PARSE_AS_BOOLEAN);
        else
            $field_definition = new FieldDefinition($newRow[TAB_DEF_INDEX], $newRow[TAB_DEF_NAME], $tp);
        $field_definition->db_type = $newRow[TAB_DEF_TYPE];
        return $field_definition;
    }
    /**
     * Check if a given type belongs to a given type category
     *
     * @param string $dataType The data type to validate
     * @param string $dataTypes The collection of data types
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
     * Parse the data using the field definition, if a column map is set the result keys are mapped
     * to the given value
     *
     * @param MysteriousParser $mys_parser The mysterious parser that are extracting the data
     * @param array $result The collection of rows where the parsed rows are stored
     * @param array $row The selected row picked from the fetch assoc process
     * @return void
     */
    public function parse_table_field_definition($mys_parser, &$result, $row)
    {
        $newRow = array();
        $column_names = array_map(function ($item) {
            return $item->column_name;
        }, $mys_parser->table_definition);
        foreach ($row as $column_name => $column_value) {
            if (in_array($column_name, $column_names)) {
                $key = $mys_parser->get_column_name($column_name);
                $value = $mys_parser->table_definition[$column_name]->get_value($column_value);
                $newRow[$key] = $value;
            }
        }
        $result[$newRow[TAB_DEF_NAME]] = $this->get_parsing_data($newRow);
    }
    /**
     * Execute the default parse storing the value to the array with an associated key.
     * The associated key is the column name
     *
     * @param MysteriousParser $mys_parser The mysterious parser that are extracting the data
     * @param array $result The collection of rows where the parsed rows are stored
     * @param array $row The selected row picked from the fetch assoc process
     * @return void
     */
    public function simple_parse($mys_parser, &$result, $row)
    {
        array_push($result, $row);
    }
    /**
     * Parse the data using the field definition, if a column map is set the result keys are mapped
     * to the given value
     *
     * @param MysteriousParser $mys_parser The mysterious parser that are extracting the data
     * @param array $result The collection of rows where the parsed rows are stored
     * @param array $row The selected row picked from the fetch assoc process
     * @return void
     */
    private function parse_with_field_definition($mys_parser, &$result, $row)
    {
        $newRow = array();
        $column_names = array_map(function ($item) {
            return $item->column_name;
        }, $mys_parser->table_definition);
        foreach ($row as $column_name => $column_value) {
            if (in_array($column_name, $column_names)) {
                $key = $mys_parser->get_column_name($column_name);
                $value = $mys_parser->table_definition[$column_name]->get_value($column_value);
                $newRow[$key] = $value;
            }
        }
        array_push($result, $newRow);
    }
    /**
     * Gets the column name from the column_map array if is defined, otherwise
     * the column_name stays as the value selected
     *
     * @param string $column_name The column name
     * @return string The column name, same or mapped name
     */
    private function get_column_name($column_name)
    {
        if (isset($this->column_map) && array_key_exists($column_name, $this->column_map))
            return $this->column_map[$column_name];
        else
            return $column_name;
    }
}
?>