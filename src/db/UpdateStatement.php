<?php

namespace Urabe\DB;

use Urabe\DB\DBKanojoX;

/**
 * UpdateStatement
 * 
 * Creates an update prepare statement
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class UpdateStatement extends PreparedStatement
{
    /**
     * @var DBKanojo The database connector
     */
    public $connector;
    /**
     * @var array The value to updates
     */
    public $update_values;
    /**
     * Initialize a new prepared statement
     * @param DBKanojo $connector The database connector
     * @param array The data used to create the prepared statement
     */
    public function __construct($connector, $data)
    {
        $this->values = array();
        $this->columns = array();
        $this->connector = $connector;
        $this->update_values = $data;
        //Build prepare statement
        foreach ($data as $column => $value) {
            array_push($this->columns, $column);
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
        $set_format = "%s = %s";
        $index=0;
        $update_values = array();
         //Build prepare statement
        foreach ($this->update_values as $column => $value) {
           $upd_row = sprintf($set_format, $column, $this->connector->get_param_place_holder(++$index));
           array_push($update_values, $upd_row);
        }
        $update_values = implode(', ', $update_values);
        $sql = sprintf($sql_format, $update_values);
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
        $set_format = "%s = %s";
        $update_values = array();
         //Build prepare statement
        foreach ($this->update_values as $column => $value) {
           $upd_row = sprintf($set_format, $column, $value);
           array_push($update_values, $upd_row);
        }
        $update_values = implode(', ', $update_values);
        $sql = sprintf($sql_format, $update_values);
        return $sql;
    }
}
