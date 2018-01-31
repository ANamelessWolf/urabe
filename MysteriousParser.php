<?php
include_once "FieldDefintion.php";
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
     * @var FieldDefintion[] The table fields definition.
     */
    public $table_definition;
    /**
     * __construct
     *
     * Initialize a new instance of the Mysterious parser.
     * @param FieldDefintion[] $table_definition The table fields definition.
     */
    function __construct($table_definition)
    {
        $this->table_definition = $table_definition;
    }
    /**
     * Gets a row obtained from a selection query and the row is parsed to match the table
     * definition types.
     * 
     * @param mixed[] $row The selected row.
     * @return mixed[] The row with table definition values.
     */
    function parse($row)
    {
        $result = array();
        foreach ($row as $field_name => $field_value) {
            $is_in_table = array_key_exists($field_name, $this->table_definition);
            //Filter only the selected values that are contained on the table definition
            if ($is_in_table) {
                //Gets the field definition
                $field_defintion = new FieldDefintion($field_name, $this->table_definition[$field_name]->data_type);
                $value = $field_defintion->GetValue($field_value);
                if ($field_defintion->is_date()) {
                    $result[$field_name . "_angular"] = date_format_angular($value);
                    $result[$field_name] = $value;
                }
                else
                    $result[$field_name] = $value;
            }
        }
        return $result;
    }
}
?>