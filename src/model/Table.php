<?php
namespace Urabe\Model;
/**
 * A model for a Field for a table database
 * 
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class Table
{
    /**
     * @var string The table name
     */
    public $table_name;
    /**
     * @var string The conecction type
     */
    public $driver;
    /**
     * @var array The table columns
     */
    public $fields;
}