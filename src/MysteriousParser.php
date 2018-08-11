<?php
include_once "FieldDefinition.php";
/**
 * Mysterious parser class
 * 
 * This class parses a row from a table definition
 * @version 1.0.0
 * @api Makoto Urabe Oracle
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class MysteriousParser
{
    /**
     * @var FieldDefinition[] The table fields definition.
     */
    public $table_definition;
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
     */
    public function __construct($table_definition = null)
    {
        if (isset($table_definition))
            $this->table_definition = $table_definition;
        $this->parse_method = function ($parser, &$result, $row) {
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
     * Gets the row parsed
     * 
     * @param resource $sentence The Oracle sentence
     * @return mixed[] The parsed row
     */
    public function parse(&$result, $row)
    {
        call_user_func_array($this->parse_method, array($this, &$result, $row));
        // $result = new stdClass();
        // foreach ($this->table_definition as &$field) {
        //     try {
        //         $value = oci_result($sentence, $field->field_name);
        //         if ($field->is_date()) {
        //             $result->{$field->field_name . "_angular"} = date_format_angular($value);
        //             $result->{$field->field_name} = $value;
        //         } else
        //             $result->{$field->field_name} = $field->get_value($value);
        //     } catch (Exception $e) {
        //         $result->{$field->field_name} = $field->get_value(null);
        //         $result->{NODE_ERROR} = $e->getMessage();
        //     }
        // }
        // return $result;
    }
    /**
     * Creates a Mysterious parser from a JSON string
     *
     * @param string $json_string The JSON string
     * @return MysteriousParser  Table definition parser
     */
    public static function create_from_JSON($json_string)
    {
        $fields = array();
        $data = json_decode($json_string);
        foreach ($data->{NODE_FIELDS} as &$field)
            array_push($fields, new FieldDefinition($field->field_name, $field->data_type));
        return new MysteriousParser($fields);
    }
}
?>