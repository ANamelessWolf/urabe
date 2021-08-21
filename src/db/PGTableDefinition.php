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
class PGTableDefinition extends TableDefinition
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
        parent::__construct($tableName, "PG");
        $PG_FIELD_COL_ORDER = new NumericFieldDefinition(0, PG_FIELD_COL_ORDER, PARSE_AS_INT, 5, 0);
        $PG_FIELD_COL_NAME = new StringFieldDefinition(1, PG_FIELD_COL_NAME, PARSE_AS_STRING, 255);
        $PG_FIELD_DATA_TP = new StringFieldDefinition(2, PG_FIELD_DATA_TP, PARSE_AS_STRING, 255);
        $PG_FIELD_CHAR_LENGTH = new NumericFieldDefinition(3, PG_FIELD_CHAR_LENGTH, PARSE_AS_INT, 5, 0);
        $PG_FIELD_NUM_PRECISION = new NumericFieldDefinition(4, PG_FIELD_NUM_PRECISION, PARSE_AS_INT, 5, 0);
        $PG_FIELD_NUM_SCALE = new NumericFieldDefinition(5, PG_FIELD_NUM_SCALE, PARSE_AS_INT, 5, 0);
        $this->add(PG_FIELD_COL_ORDER, $PG_FIELD_COL_ORDER);
        $this->add(PG_FIELD_COL_NAME, $PG_FIELD_COL_NAME);
        $this->add(PG_FIELD_DATA_TP, $PG_FIELD_DATA_TP);
        $this->add(PG_FIELD_CHAR_LENGTH, $PG_FIELD_CHAR_LENGTH);
        $this->add(PG_FIELD_NUM_PRECISION, $PG_FIELD_NUM_PRECISION);
        $this->add(PG_FIELD_NUM_SCALE, $PG_FIELD_NUM_SCALE);
    }

    /**
     * Gets the query for selecting the table definition
     *
     * @return string The table definition selection query
     */
    public function select_query()
    {
        $fields = PG_FIELD_COL_ORDER . ", " . PG_FIELD_COL_NAME . ", " . PG_FIELD_DATA_TP . ", " .
            PG_FIELD_CHAR_LENGTH . ", " . PG_FIELD_NUM_PRECISION . ", " . PG_FIELD_NUM_SCALE;
        if (isset($this->schema)) {
            $schema = $this->schema;
            $sql = "SELECT $fields FROM information_schema.columns WHERE table_name = '$this->table_name' AND table_schema = '$schema'";
        } else
            $sql = "SELECT $fields FROM information_schema.columns WHERE table_name = '$this->table_name'";
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
        $field->column_index = $row[PG_FIELD_COL_ORDER];
        $field->column_name = $row[PG_FIELD_COL_NAME];
        $field->data_type = $this->get_type($row[PG_FIELD_DATA_TP]);
        $field->char_max_length =  isset($row[PG_FIELD_CHAR_LENGTH]) ? $row[PG_FIELD_CHAR_LENGTH] : 0;
        $field->numeric_precision = isset($row[PG_FIELD_NUM_PRECISION]) ? $row[PG_FIELD_NUM_PRECISION] : 0;
        $field->numeric_scale = isset($row[PG_FIELD_NUM_SCALE]) ? $row[PG_FIELD_NUM_SCALE] : 0;
        return $field;
    }
}
