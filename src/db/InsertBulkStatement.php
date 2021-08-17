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
class InsertBulkStatement extends PreparedStatement
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
     * @var array The list of rows to insert
     */
    public $insert_rows;
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
        $this->insert_rows = $data;
        $index = 0;
        //Build prepare statement
        foreach ($data[0] as $column => $value) {
            array_push($this->columns, $column);
            array_push($this->place_holder_list, $this->connector->get_param_place_holder(++$index));
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
        $value_format = "(%s)";
        $bulk_values = array();
        $place_holders = implode(', ', $this->place_holder_list);
        for ($i=0; $i < sizeof($this->insert_rows); $i++) {
            foreach ($this->columns as &$column)
                array_push($this->values, $this->insert_rows[$i][$column]);
            $ins_row_format = sprintf($value_format, $place_holders);
            array_push($bulk_values, $ins_row_format);
        }
        $bulk = implode(', ', $bulk_values);
        $sql = sprintf($sql_format, $columns, $bulk);
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
        $columns = implode(', ', $this->columns);
        $value_format = "(%s)";
        $bulk_values = array();
        for ($i=0; $i < sizeof($this->insert_rows); $i++) {
            $row_value = array();
            foreach ($this->insert_rows[$i] as $column => $value){
                array_push($this->values, $this->insert_rows[$i][$column]);
                array_push($row_value, $value);
            }
            $value_data = implode(', ', $row_value);
            $ins_row_format = sprintf($value_format, $value_data);
            array_push($bulk_values, $ins_row_format);
        }
        $bulk = implode(', ', $bulk_values);
        $sql = sprintf($sql_format, $columns, $bulk);
        return $sql;
    }
}
