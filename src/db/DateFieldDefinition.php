<?php
namespace Urabe\DB;
use Urabe\DB\FieldDefinition;
use Urabe\Config\DBDriver;
/**
 * String Date Definition Class
 * 
 * This class encapsulates a table column definition and format it values to JSON field value
 * Each table field is associated to a column and stores its index and data type.
 * 
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class DateFieldDefinition extends FieldDefinition
{
    /**
     * @var string The format used to parse the given date
     */
    public $date_format;
    /**
     * Initialize a new instance of a Field Definition class
     *
     * @param string $index The column index
     * @param string $column The column name
     * @param string $data_type The data type name
     * @param string $date_format The date format
     */
    public function __construct($index, $column, $data_type, $date_format)
    {
        $this->date_format = $date_format;
        parent::__construct($index, $column, $data_type);
    }
    /**
     * Gets the value from a string in the row definition data type
     *
     * @param string $value The selected value as string
     * @return string The value formatted as a date
     */
    public function get_value($value)
    {
        if (is_null($value))
            return null;
        else
            return date($this->date_format, strtotime($value));
    }
    /**
     * Formats a value to be use as a place holder parameter
     *
     * @param DBDriver $driver The selected value
     * @param mixed $value The selected value as string
     * @return mixed The value as the same type of the table definition.
     */
    public function format_value($driver, $value)
    {
        return $this->default_format_value($value);
    }    
}
?>