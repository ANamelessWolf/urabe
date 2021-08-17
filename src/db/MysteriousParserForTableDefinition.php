<?php
namespace Urabe\DB;
use Urabe\DB\MysteriousParser;
use Urabe\DB\TableDefinition;
/**
 * Mysterious parser class
 * 
 * This class parses a row from a table definition
 * @version 1.0.0
 * @api Makoto Urabe DB Manager Oracle
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class MysteriousParserForTableDefinition extends MysteriousParser
{
    /**
     * __construct
     *
     * Initialize a new instance of the Mysterious parser used to parse a Table definition
     * @param TableDefinition $table_definition The table fields definition.
     * When table definition is presented the fetched data is parsed using the parse_with_field_definition function 
     */
    public function __construct($table_definition)
    {
        $this->caller = $this;
        $this->table_definition = $table_definition;
        $this->parse_method = "parse_table_field_definition";
        $this->id = hash("md5", $this->parse_method . spl_object_hash($this));
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
        $selected_rows = array();
        $column_names = $this->table_definition->get_column_names();
        foreach ($row as $column_name => $selected_value)
            if (in_array($column_name, $column_names))
                $selected_rows[$column_name] = $mys_parser->parse_value($column_name, $selected_value);
        $row_result = $this->table_definition->parse_field_definition($selected_rows);
        array_push($result, $row_result);
    }
}