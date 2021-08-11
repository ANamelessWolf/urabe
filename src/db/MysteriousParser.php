<?php
namespace Urabe\DB;
use Urabe\DB\TableDefinition;
use Urabe\DB\FieldDefinition;
use Urabe\DB\DBUtils;
use Urabe\Runtime\MysteriousParsingException;
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
     * @var TableDefinition The table definition.
     */
    public $table_definition;
    /**
     * Defines the result parsing method, this function receives a row and
     * an array where the data should be putted.
     *
     * @var callable The parse method, passed as an anonymous function
     */
    public $parse_method;
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
        $this->table_definition = new TableDefinition();
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
     * Parse a selected value using the field definition class. The field
     * definition is selected from the table Definition
     *
     * @param string $column_name The column name
     * @param mixed $selValue The selected value
     * @return mixed The parsed value
     */
    public function parse_value($column_name, $selValue)
    {
        if ($this->table_definition->exists($column_name)) {
            $field = $this->table_definition->get_field_definition($column_name);
            $value = $field->get_value($selValue);
            return $value;
        } else {
            $error = DBUtils::create_parsing_error($column_name, $selValue);
            throw new MysteriousParsingException($error);
        }
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
        $column_names = $this->table_definition->get_column_names();
        foreach ($row as $column_name => $selected_value)
            if (in_array($column_name, $column_names))
                $newRow[$column_name] = $mys_parser->parse_value($column_name, $selected_value);
        $result[$newRow[TAB_DEF_NAME]] = $this->table_definition->parse_field_definition($newRow);
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
    protected function parse_with_field_definition($mys_parser, &$result, $row)
    {
        $newRow = array();
        $column_names = $this->table_definition->get_column_names();
        foreach ($row as $column_name => $selected_value)
            if (in_array($column_name, $column_names))
                $newRow[$column_name] = $mys_parser->parse_value($column_name, $selected_value);
        array_push($result, $newRow);
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
}