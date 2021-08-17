<?php

namespace Urabe\DB;

use Urabe\DB\DBKanojoX;

/**
 * InsertStatement
 * 
 * Creates an insert prepare statement
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class InsertStatement extends PreparedStatement
{
    /**
     * @var array The list of insert values as place holders
     */
    public $place_holder_list;

    /**
     * @var DBKanojo The database connector
     */
    public $connector;

    /**
     * Initialize a new prepared statement
     * @param DBKanojo $connector The database connector
     * @param array The data used to create the prepared statement
     */
    public function __construct($connector, $data)
    {
        $this->values = array();
        $this->columns = array();
        $this->place_holder_list = array();
        $this->connector = $connector;
        $index = 0;
        //Build prepare statement
        foreach ($data as $column => $value) {
            array_push($this->columns, $column);
            array_push($this->place_holder_list, $this->connector->get_param_place_holder(++$index));
            array_push($this->values, $value);
        }
    }
    /**
     * Builds the insertion sql
     *
     * @param string $sql_format The SQL format
     * @return string The formatted sql
     */
    public function build_sql($sql_format)
    {
        $columns = implode(', ', $this->columns);
        $place_holders = implode(', ', $this->place_holder_list);
        $sql = sprintf($sql_format, $columns, $place_holders);
        return $sql;
    }
    /**
     * SQL formatted
     *
     * @param string $sql_format The SQL format
     * @return string The formatted sql
     */
    public function check_sql($sql_format)
    {
        $columns = implode(', ', $this->columns);
        $values = implode(', ', $this->values);
        $sql = sprintf($sql_format, $columns, $values);
        return $sql;
    }
}
