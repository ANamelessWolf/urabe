<?php

namespace Urabe\Model;

/**
 * A model for a Field for a table database
 * 
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class Field
{
    /**
     * @var int The column index
     */
    public $column_index;
    /**
     * @var bool If true the field is required to be inserted
     */
    public $required;
    /**
     * @var string The column name
     */
    public $column_name;
    /**
     * @var string The column data type
     */
    public $data_type;
    /**
     * @var string In case of being of data type string this field stores the character max length
     */
    public $char_max_length;
    /**
     * @var string In case of being of data type numeric this field stores the numeric precision
     */
    public $numeric_precision;
    /**
     * @var string In case of being of data type numeric this field stores the numeric scale
     */
    public $numeric_scale;
}
