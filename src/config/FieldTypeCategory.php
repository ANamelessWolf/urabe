<?php
namespace Urabe\Config;
/**
 * Field type data category configuration
 *
 * In this file the application work around can be customized
 * 
 * @version 1.0.0
 * @api Makoto Urabe DB Manager database connector
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class FieldTypeCategory
{
    /**
     * @var array Gets the supported types
     */
    public $SupportedTypes;
    /**
     * @var array Gets the data types for String type names
     */
    public $StringTypes;
        /**
     * @var array Gets the data types for Integer type names
     */
    public $IntegerTypes;
        /**
     * @var array Gets the data types for Long type names
     */
    public $LongTypes;
        /**
     * @var array Gets the data types for Number type names
     */
    public $NumberTypes;
        /**
     * @var array Gets the data types for Date type names
     */
    public $DateTypes;
        /**
     * @var array Gets the data types for Boolean type names
     */
    public $BooleanTypes;

    /**
     * __construct
     *
     * Initialize a new instance of a Field Type Category.
     */
    public function __construct()
    {
       $this->SupportedTypes = array(
            "String", "Integer", "Long", "Number", "Date", "Boolean"
       );
       $this->StringTypes = array(
            //PG Types
            "character", "text",
            //MySQL Types
            "varchar"
       );
        $this->IntegerTypes = array(
            //PG Types
            "integer", "smallint",
            //MySQL Types
            "int"
        );
        $this->LongTypes = array(
            //PG Types
            "bigint"
            //MySQL types
        );
        $this->NumberTypes = array(
            //PG Types
            "double precision", "numeric", "real",
            //MySQL types
            "double"
        );
        $this->DateTypes = array(
            //PG Types
            "date",
            "timestamp"
        );
        $this->BooleanTypes = array(
            "boolean"
        );
    }
}
