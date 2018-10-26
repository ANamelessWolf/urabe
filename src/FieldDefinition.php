<?php

/**
 * Field Definition Class
 * 
 * This class encapsulates a table column definition and format it values to JSON field value
 * Each table field is associated to a column and stores its index and data type.
 * 
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class FieldDefinition
{
    /**
     * @var int The column index
     */
    public $column_index;
    /**
     * @var string The column name
     */
    public $column_name;
    /**
     * @var string The column data type
     */
    public $data_type;
    /**
     * Initialize a new instance of a Field Definition class
     *
     * @param string $index The field name
     * @param string $field The field name
     * @param string $data_type The field data type
     */
    public function __construct($index, $column, $data_type)
    {
        $this->column_index = $index;
        $this->field_name = $column;
        $this->column_name = $column;
        $this->data_type = $data_type;
    }


    /**
     * Gets the value from a string in the row definition data type
     *
     * @param string $value The selected value as string
     * @return mixed The value as the same type of the table definition.
     */
    public function get_value($value)
    {
        if (is_null($value))
            return null;
        else if ($this->data_type == PARSE_AS_STRING)
            return strval($value);
        else if ($this->data_type == PARSE_AS_INT)
            return intval($value);
        else if ($this->data_type == PARSE_AS_NUMBER)
            return doubleval($value);
        else if ($this->data_type == PARSE_AS_DATE)
            return $value;
    }
}
?>