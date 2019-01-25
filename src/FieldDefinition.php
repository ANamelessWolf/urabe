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
     * @var string The column parsing type
     */
    public $data_type;
    /**
     * @var string The column db_type
     */
    public $db_type;
    /**
     * Initialize a new instance of a Field Definition class
     *
     * @param string $index The column index
     * @param string $column The column name
     * @param string $data_type The column parsing type
     */
    public function __construct($index, $column, $data_type)
    {
        $this->column_index = $index;
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
        else if ($this->data_type == PARSE_AS_INT || $this->data_type == PARSE_AS_LONG)
            return intval($value);
        else if ($this->data_type == PARSE_AS_NUMBER)
            return doubleval($value);
        else if ($this->data_type == PARSE_AS_DATE)
            return $value;
        else if ($this->data_type == PARSE_AS_BOOLEAN)
            return boolval($value);
        else
            return $value;
    }
    /**
     * Formats a value to be use as a place holder parameter
     *
     * @param DBDriver $driver The selected value as string
     * @param mixed $value The selected value as string
     * @return mixed The value as the same type of the table definition.
     */
    public function format_value($driver, $value)
    {
        if (is_null($value))
            return null;
        else if (in_array($this->data_type, array(PARSE_AS_STRING, PARSE_AS_INT, PARSE_AS_LONG, PARSE_AS_NUMBER)))
            return $value;
        else
            return strval($value);
    }
    /**
     * Creates a Field Definition object from a data type
     *
     * @param string $data The data type
     * @return FieldDefinition The field definition object
     */
    public static function create($data)
    {
        $tp = $data->data_type;
        if ($tp == PARSE_AS_STRING)
            $field_definition = new StringFieldDefinition($data->column_index, $data->column_name, PARSE_AS_STRING, $data->char_max_length);
        else if ($tp == PARSE_AS_INT)
            $field_definition = new NumericFieldDefinition($data->column_index, $data->column_name, PARSE_AS_INT, $data->numeric_precision, $data->numeric_scale);
        else if ($tp == PARSE_AS_NUMBER)
            $field_definition = new NumericFieldDefinition($data->column_index, $data->column_name, PARSE_AS_NUMBER, $data->numeric_precision, $data->numeric_scale);
        else if ($tp == PARSE_AS_DATE)
            $field_definition = new DateFieldDefinition($data->column_index, $data->column_name, PARSE_AS_DATE, $data->date_format);
        else if ($tp == PARSE_AS_LONG)
            $field_definition = new NumericFieldDefinition($data->column_index, $data->column_name, PARSE_AS_LONG, $data->numeric_precision, $data->numeric_scale);
        else if ($tp == PARSE_AS_BOOLEAN)
            $field_definition = new BooleanFieldDefinition($data->column_index, $data->column_name, PARSE_AS_BOOLEAN);
        else
            $field_definition = new FieldDefinition($data->column_index, $data->column_name, $data->db_type);
        $field_definition->db_type = $data->db_type;
        return $field_definition;
    }
}
?>