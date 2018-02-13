<?php
include_once "Urabe.php";
include_once "MysteriousParser.php";
include_once "FieldDefintion.php";
include_once "GETService.php";
include_once "ParameterCollection.php";
/**
 * A Hasami Wrapper is a web service wrapper Class
 * This class encapsulate and manage webservice verbose PUT, POST, DELETE and GET
 * @version 1.0.0
 * @api Makoto Urabe Oracle
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class HasamiWrapper
{
    /**
     * @var string Gets the request HTTP verbose name; POST, GET, PUT, and DELETE. 
     */
    public $method;
    /**
     * @var stdClass The body message
     */
    public $body;
    /**
     * @var Urabe The Oracle Connector
     */
    public $connector;
    /**
     * @var string The table name
     */
    public $table_name;
    /**
     * @var FieldDefintion[] The table fields definitions
     */
    public $table_fields;
    /**
     * @var string Gets or sets the name of the table primary key field name.
     */
    public $primary_key;
    /**
     * @var string $database_name 
     * The database name used when performing queries.
     */
    public $database_name;
    /**
     * @var GETService The webservice GET action
     */
    public $GET;
    /**
     * __construct
     *
     * Initialize a new instance of a HasamiWrapper Class
     * @param string $table_name The table name.
     * @param KanojoX $connection_id The connection id
     * @param string|NULL $primary_key The name of the primary key.
     * @param FieldDefinition[] $table_def The table definition, if null
     * the table definition are selected from the database.
     */
    public function __construct($table_name, $connection_id, $primary_key = null, $table_def = null)
    {
        $this->table_name = $table_name;
        $this->connector = new Urabe($connection_id);
        if (is_null($table_def))
            $this->table_fields = $this->connector->get_table_definition($this->table_name);
        else
            $this->table_fields = $table_def;
        $this->primary_key = $primary_key;
        $this->parser = new MysteriousParser($this->table_fields);
        $this->database = $this->connector->database_name;
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->get_body();
        //Por default se permiten todos los servicios
        $this->enable_GET = true;
        //Se inicializan los métodos a los que tiene disponible el servidor.
        $this->GET = new GETService($this);
    }
    /**
     * Initialize the body object extracting the data from the file contents 
     * php://input
     * @return void
     */
    public function get_body()
    {
        $body_methods = array("PUT", "POST", "DELETE");
        if (in_array($this->method, $body_methods)) {
            $this->body = file_get_contents('php://input');
            $this->body = json_decode($this->body);
        } else
            $this->body = null;
    }
}
?>