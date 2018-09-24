<?php
include_once "FieldDefinition.php";
/**
 * Field Definition Class
 * 
 * This class encapsulates a table field definition of type character variable. 
 * Each table field is associated to a column name and the column data type.
 * This class will treat the field as string type.
 * @api Makoto Urabe DB Manager Oracle
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class VarCharFieldDefinition extends FieldDefinition
{
    /**
     * @var string STRING_FORMAT
     * The format  used to save the field in a JSON result.
     */
    const STRING_FORMAT = '"%s" : "%s"';
    /**
     * @var int The character maximum length
     */
    public $char_length;

    /**
     * Initialize a new instance of a Var Char Field Definition class
     *
     * @param string $index The field name
     * @param string $field The field name
     * @param string $data_type The field data type
     * @param string $char_length The character maximum length
     */
    public function __construct($index, $column, $data_type, $char_length)
    {
        parent::__construct($index, $column, $data_type);
        $this->char_length = $char_length;
    }
    /**
     * Gets the value from a string in the row definition data type
     *
     * @param string $value The selected value as string
     * @return mixed The value as the same type of the table definition.
     */
    public function get_value($value)
    {
        return $value;
    }
}
?>