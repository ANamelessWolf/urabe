<?php
namespace Urabe\DB;
use Urabe\DB\FieldDefinition;
use Urabe\Config\DBDriver;
/**
 * String Field Definition Class
 * 
 * This class encapsulates a table column definition and format it values to JSON field value
 * Each table field is associated to a column and stores its index and data type.
 * 
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class StringFieldDefinition extends FieldDefinition
{
    /**
     * @var int The maximum number of allowed characters
     */
    public $char_max_length;
    /**
     * Initialize a new instance of a Field Definition class
     *
     * @param string $index The column index
     * @param string $column The column name
     * @param string $data_type The data type name
     * @param int $char_max_length The maximum number of allowed characters, 
     * value zero allows unlimited characters
     */
    public function __construct($index, $column, $data_type, $char_max_length)
    {
        $this->char_max_length = $char_max_length;
        parent::__construct($index, $column, $data_type);
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
        else
            return strval($value);
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
