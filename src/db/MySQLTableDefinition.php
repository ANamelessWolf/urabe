<?php

namespace Urabe\DB;

use Urabe\DB\TableDefinition;
use Urabe\DB\NumericFieldDefinition;
use Urabe\DB\StringFieldDefinition;
use Urabe\Model\Table;
use Urabe\Model\Field;

/**
 * Table Definition Class
 * 
 * This class encapsulates a table column definition collection and format it values to JSON field value
 * Each table field is associated to a column and stores its index and data type.
 * 
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class MySQLTableDefinition extends TableDefinition
{
    /**
     * @var string El nombre de la base de datos
     */
    public $db_name;
    /**
     * Initialize a new instance of the MysQL Table Definition
     
     * @param string $tableName The name of the table
     */
    public function __construct($tableName, $db = null)
    {
        $this->db_name = $db;
        parent::__construct($tableName, "MySQL");
        $MYSQL_FIELD_COL_ORDER = new NumericFieldDefinition(0, MYSQL_FIELD_COL_ORDER, PARSE_AS_INT, 5, 0);
        $MYSQL_FIELD_COL_NAME = new StringFieldDefinition(1, MYSQL_FIELD_COL_NAME, PARSE_AS_STRING, 255);
        $MYSQL_FIELD_DATA_TP = new StringFieldDefinition(2, MYSQL_FIELD_DATA_TP, PARSE_AS_STRING, 255);
        $MYSQL_FIELD_CHAR_LENGTH = new NumericFieldDefinition(3, MYSQL_FIELD_CHAR_LENGTH, PARSE_AS_INT, 5, 0);
        $MYSQL_FIELD_NUM_PRECISION = new NumericFieldDefinition(4, MYSQL_FIELD_NUM_PRECISION, PARSE_AS_INT, 5, 0);
        $MYSQL_FIELD_NUM_SCALE = new NumericFieldDefinition(5, MYSQL_FIELD_NUM_SCALE, PARSE_AS_INT, 5, 0);
        $this->add(MYSQL_FIELD_COL_ORDER, $MYSQL_FIELD_COL_ORDER);
        $this->add(MYSQL_FIELD_COL_NAME, $MYSQL_FIELD_COL_NAME);
        $this->add(MYSQL_FIELD_DATA_TP, $MYSQL_FIELD_DATA_TP);
        $this->add(MYSQL_FIELD_CHAR_LENGTH, $MYSQL_FIELD_CHAR_LENGTH);
        $this->add(MYSQL_FIELD_NUM_PRECISION, $MYSQL_FIELD_NUM_PRECISION);
        $this->add(MYSQL_FIELD_NUM_SCALE, $MYSQL_FIELD_NUM_SCALE);
    }

    /**
     * Gets the query for selecting the table definition
     *
     * @return string The table definition selection query
     */
    public function select_query()
    {
        $fields = MYSQL_FIELD_COL_ORDER . ", " . MYSQL_FIELD_COL_NAME . ", " . MYSQL_FIELD_DATA_TP . ", " .
            MYSQL_FIELD_CHAR_LENGTH . ", " . MYSQL_FIELD_NUM_PRECISION . ", " . MYSQL_FIELD_NUM_SCALE;
        if (isset($this->db_name))
            $sql = "SELECT $fields FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME` = '$this->table_name' AND `TABLE_SCHEMA` = '$this->db_name'";
        else
            $sql = "SELECT $fields FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME` = '$this->table_name'";
        return $sql;
    }
    /**
     * Gets the field definition used to parse a row
     *
     * @param string The selected row
     * @return Field The Field definition
     */
    public function parse_field_definition($row)
    {
        $field = new Field();
        $field->column_index = $row[MYSQL_FIELD_COL_ORDER];
        $field->column_name = $row[MYSQL_FIELD_COL_NAME];
        $field->data_type = $this->get_type($row[MYSQL_FIELD_DATA_TP]);
        $field->char_max_length =  isset($row[MYSQL_FIELD_CHAR_LENGTH]) ? $row[MYSQL_FIELD_CHAR_LENGTH] : 0;
        $field->numeric_precision = isset($row[MYSQL_FIELD_NUM_PRECISION]) ? $row[MYSQL_FIELD_NUM_PRECISION] : 0;
        $field->numeric_scale = isset($row[MYSQL_FIELD_NUM_SCALE]) ? $row[MYSQL_FIELD_NUM_SCALE] : 0;
        return $field;
    }
}
