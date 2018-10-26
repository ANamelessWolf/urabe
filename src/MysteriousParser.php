<?php

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
     * __construct
     *
     * Initialize a new instance of the Mysterious parser.
     * @param FieldDefinition[] $table_definition The table fields definition.
     * When table definition is presented the fetched data is parsed using the parse_with_field_definition function 
     */
    public function __construct($table_definition = null)
    {
        if (isset($table_definition)) {
            $this->table_definition = $table_definition;
            $this->parse_method = function ($mys_parser, &$result, $row) {
                $this->parse_with_field_definition($mys_parser, $result, $row);
            };
        } else
            $this->parse_method = function ($mys_parser, &$result, $row) {
            array_push($result, $row);
        };
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
        $field_definition = new FieldDefinition($newRow[TAB_DEF_INDEX], $newRow[TAB_DEF_NAME], $newRow[TAB_DEF_TYPE]);
        return $field_definition;
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
        $result[$newRow[TAB_DEF_NAME]] =;
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