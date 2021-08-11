<?php
namespace Urabe\DB;
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
abstract class FieldDefinition
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
     * @param DBDriver $driver The selected database driver
     * @param mixed $value The selected value
     * @return mixed The value as the same type of the table definition.
     */
    public abstract function format_value($driver, $value);
    /**
     * Formats a value to be use as a place holder parameter
     * @param mixed $value The selected value
     * @return mixed The value as the same type of the table definition.
     */
    protected function default_format_value($value)
    {
        if (is_null($value))
            return null;
        else if (in_array($this->data_type, array(PARSE_AS_STRING, PARSE_AS_INT, PARSE_AS_LONG, PARSE_AS_NUMBER)))
            return $value;
        else
            return strval($value);
    }
}
