<?php

namespace Urabe;

use Exception;
use Urabe\DB\MysteriousParser;
use Urabe\DB\MYSQLKanojoX;
use Urabe\DB\ORACLEKanojoX;
use Urabe\DB\PGKanojoX;
use Urabe\DB\Selector;
use Urabe\DB\Executor;
use Urabe\Config\DBDriver;

/**
 * A Database connection manager
 * 
 * Urabe is the main protagonist in the Nazo no Kanojo X, this class manage and wraps all transactions to the database.
 * Given the Kanojo profile Urabe should be able to connect with ORACLE, PG and MySQL
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class Urabe
{
    /**
     * @var DBKanojoX The database manager
     */
    private $connector;
    /**
     * @var bool Check if there is an active connection to the database.
     */
    private $is_connected;
    /**
     * @var MysteriousParser The database parser
     */
    public $parser;
        /**
     * @var Selector The database selection tool
     */
    public $selector;
        /**
     * @var Executor The database query execution
     */
    public $executor;
    /**
     * __construct
     *
     * Initialize a new instance of the Urabe Database manager.
     * The connection is opened in the constructor should be closed using close method.
     * @param KanojoX $connection The database connector
     * @param TableDefinition $table_definition The Table definition
     */
    public function __construct($connection, $table_definition)
    {
        //1: Creación del parser
        $this->parser = new MysteriousParser($table_definition);
        //2: Inicialización del connector
        $driver_type = $connection->db_driver;
        if (DBDriver::MYSQL == $driver_type)
            $this->connector = new MYSQLKanojoX($connection, $this->parser);
        elseif (DBDriver::ORACLE == $driver_type)
            $this->connector = new ORACLEKanojoX($connection, $this->parser);
        elseif (DBDriver::PG == $driver_type)
            $this->connector = new PGKanojoX($connection, $this->parser);
        else
            throw new Exception(ERR_DB_NOT_SUPPORTED);
        //3: Conectar a la base de datos
        if (isset($this->connector)) {
            $this->connector->connect();
            if ($this->connector) {
                $this->is_connected = true;
                $this->selector = new Selector($this->connector, $this->is_connected, $this->parser);
                $this->executor = new Executor($this->connector, $this->is_connected, $this->parser);
            } else {
                $this->is_connected = false;
                $this->error = $this->connector;
            }
        } else
            throw new Exception(ERR_BAD_CONNECTION);
    }
    /**
     * Formats the bindable parameters place holders in to
     * the current driver place holder format
     *
     * @param string $sql The sql statement
     * @return string Returns the formatted sql statement
     */
    public function format_sql_place_holders($sql)
    {
        $matches = array();
        preg_match_all("/@\d+/", $sql, $matches);
        $search = array();
        $replace = array();
        for ($i = 0; $i < sizeof($matches[0]); $i++)
            if (!in_array($matches[0][$i], $search)) {
                $index = intval(str_replace('@', '', $matches[0][$i]));
                array_push($search, $matches[0][$i]);
                array_push($replace, $this->connector->get_param_place_holder($index));
            }
        return str_replace($search, $replace, $sql);
    }
    /**
     * Updates the mysterious parser
     * 
     * @param TableDefinition $table_definition The Table definition
     * @return void
     */
    public function update_parser($table_definition)
    {
        $this->parser = new MysteriousParser($table_definition);
        $this->selector->parser = $this->parser;
        $this->executor->parser = $this->parser;
        $this->connector->parser = $this->parser;
    }
    /**
     * Gets the database connector driver
     *
     * @return DBDriver The database driver
     */
    public function get_driver()
    {
        return $this->connector->db_driver;
    }
}
