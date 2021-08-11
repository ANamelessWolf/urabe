<?php
namespace Urabe\DB;
use Urabe\Config\ConnectionError;
use Urabe\DB\FieldDefinition;
use Urabe\DB\StringFieldDefinition;
use Urabe\DB\NumericFieldDefinition;
use Urabe\DB\DateFieldDefinition;
use Urabe\DB\BooleanFieldDefinition;
/**
 * Database Utilities
 * 
 * This class encapsulates database function utilities
 * 
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class DBUtils
{
    /**
     * Creates a Field Definition object from a data type
     *
     * @param FieldDefinition $data The data type
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

    /**
     * Gets the error found in a ORACLE resource object could be a
     * SQL statement error or a connection error.
     *
     * @param string $column_name The column name
     * @param mixed $selValue The selected value
     * @return ConnectionError The connection or transaction error 
     */
    public static function create_parsing_error($column_name, $selValue)
    {
        $error = new ConnectionError();
        $error->code = ERR_PARSING_VALUE_CODE;
        $error->message = sprintf(ERR_PARSING_VALUE, $column_name, $selValue);
        return $error;
    }

}