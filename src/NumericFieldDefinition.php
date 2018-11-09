<?php
include_once "FieldDefinition.php";
include_once "NumberType.php";
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
class NumericFieldDefinition extends FieldDefinition
{
    /**
     * @var int The numeric precision
     */
    public $numeric_precision;
    /**
     * @var int The numeric scale
     */
    public $numeric_scale;
    /**
     * @var NumberType The numeric type
     */
    public $numeric_type;
    /**
     * Initialize a new instance of a Field Definition class
     *
     * @param string $index The column index
     * @param string $column The column name
     * @param string $data_type The data type name
     * @param int $precision The numeric precision
     * @param int $scale The numeric scale
     * value zero allows unlimited characters
     */
    public function __construct($index, $column, $data_type, $precision, $scale)
    {
        $this->numeric_scale = $scale;
        $this->numeric_precision = $precision;
        if ($data_type == PARSE_AS_INT)
            $this->numeric_type = NumberType::INTEGER;
        else if ($data_type == PARSE_AS_NUMBER)
            $this->numeric_type = NumberType::DOUBLE;
        else if ($data_type == PARSE_AS_LONG)
            $this->numeric_type = NumberType::LONG;
        else
            $this->numeric_type = NumberType::NAN;
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
        else if ($this->numeric_type == NumberType::INTEGER) {
            return intval($value);
        } else if ($this->numeric_type == NumberType::DOUBLE) {
            return doubleval($value);
        } else if ($this->numeric_type == NumberType::LONG) {
            return strval($value);
        } else
            return null;
    }
}
?>