<?php
namespace Urabe\DB;
use Urabe\DB\DBKanojoX;
/**
 * PreparedStatement
 * 
 * Creates a prepare statement
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class PreparedStatement
{
    /**
     * @var array The parameter values
     */
    public $values;
    /**
     * @var array The column list
     */
    public $columns;

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
        $this->connector = $connector;
        //Build prepare statement
        foreach ($data as $column => $value){
            array_push($this->columns, $column);
            array_push($this->values, $value);
        }
    }
    /**
     * Builds the sql
     *
     * @param string $sql_format The SQL format
     * @return string The formatted sql
     */
    public function build_sql($sql_format){
        $index = 0;
        return sprintf($sql_format, $this->connector->get_param_place_holder(++$index));
    }
    /**
     * SQL formatted
     *
     * @param string $sql_format The SQL format
     * @return string The formatted sql
     */
    public function check_sql($sql_format){
        return sprintf($sql_format, $this->values);
    }
}
?>