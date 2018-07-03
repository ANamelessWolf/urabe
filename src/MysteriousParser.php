<?php
include_once "FieldDefinition.php";
include_once "HasamiUtils.php";
/**
 * Mysterious parser class
 * 
 * This class parses a row from a table defintion
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
     * __construct
     *
     * Initialize a new instance of the Mysterious parser.
     * @param FieldDefinition[] $table_definition The table fields definition.
     */
    public function __construct($table_definition)
    {
        $this->table_definition = $table_definition;
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
     * Gets a row obtained from a selection query and the row is parsed to match the table
     * definition types.
     * 
     * @param resource $sentence The Oracle sentence
     * @return mixed[] The parsed row
     */
    public function parse($sentence)
    {
        $result = new stdClass();
        foreach ($this->table_definition as &$field) {
            try {
                $value = oci_result($sentence, $field->field_name);
                if ($field->is_date()) {
                    $result->{$field->field_name . "_angular"} = date_format_angular($value);
                    $result->{$field->field_name} = $value;
                } else
                    $result->{$field->field_name} = $field->get_value($value);
            } catch (Exception $e) {
                $result->{$field->field_name} = $field->get_value(null);
                $result->{NODE_ERROR} = $e->getMessage();
            }
        }
        return $result;
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