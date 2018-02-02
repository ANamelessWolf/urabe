<?php
include_once "Warai.php";
/**
 * Field Definition Class
 * 
 * This class encapsulates a table field. Each table field is asociated with a the table field name and the table field data type.
 * This class treats the database fields types in three types; strings, dates and numbers.
 * @api Makoto Urabe Oracle
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class FieldDefintion
{
    /**
     * @var string STRING_FORMAT
     * The format that saves the JSON data with a string format.
     */
    const STRING_FORMAT = '"%s" : "%s"';
    /**
     * @var string INTEGER_FORMAT
     * The format that saves the JSON data with a number format.
     */
    const INTEGER_FORMAT = '"%s" : %s';
    /**
     * @var string STRING_FORMAT
     * The format that saves the JSON data with a date format.
     * Saves the date in JSON format and in the database format.
     */
    const DATE_FORMAT = '"%s" : "%s", "%s_db" : "%s"';
    /**
     * @var string The field name
     */
    public $field_name;
    /**
     * @var string The field data type
     */
    public $data_type;
    /**
     *
     * Initialize a new instance of a Field Defintion class
     *
     * @param string $field The field name
     * @param string $data_type The field data type
     */
    public function __construct($field, $data_type)
    {
        $this->field_name = $field;
        $this->data_type = $data_type;
    }
    /**
     * Gets the value from a string in the row definition data type
     *
     * @param string $value The selected value as string
     * @return mixed The value as the same type of the table definition.
     */
    public function GetValue($value)
    {
        $integer_types = array("LONG","ROWID","UROWID");
        $float_types = array("FLOAT","NUMBER");
        if (in_array($this->data_type, $integer_types))
            return is_null($value) ? 0 : intval($value);
        else if (in_array($this->data_type, $float_types))
            return is_null($value) ? 0.0 : floatval($value);
        //Not defined fields are treated as strings
        else
            return is_null($value) ? "" : $value;
    }
    /**
     * Verify if the current data type is a date type
     * @param string $data_type The field data type
     */
    function is_date()
    {
       $condition = $this->data_type=="DATE" || 
                    strpos($this->data_type, "TIMESTAMP") ||
                    strpos($this->data_type, "INTERVAL DAY") ||
                    strpos($this->data_type, "INTERVAL YEAR");
        return $condition;
    }
}
?>