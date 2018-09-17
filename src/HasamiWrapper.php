<?php
include_once "Urabe.php";
include_once "MysteriousParser.php";
include_once "FieldDefinition.php";
include_once "UrabeSettings.php";
include_once "GETService.php";
include_once "ParameterCollection.php";

/**
 * A Hasami Wrapper is a web service wrapper Class
 * This class encapsulate and manage web service verbose PUT, POST, DELETE and GET
 * @version 1.0.0
 * @api Makoto Urabe Oracle
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class HasamiWrapper implements IHasami
{
    /**
     * The web service request content
     *
     * @var WebServiceContent The web service content
     */
    private $request_data;
    /**
     * @var Urabe The database manager
     */
    private $urabe;
    /**
     * @var string The table name
     */
    private $table_name;
    /**
     * @var string The default column filter name
     */
    public $column_filter_name;

    /**
     * Gets the database manager
     *
     * @return Urabe The database manager
     */
    public function get_urabe()
    {
        return $this->urabe;
    }
    /**
     * Gets the web service request content
     *
     * @return WebServiceContent Returns the web service content
     */
    public function get_request_data()
    {
        return $this->request_data;
    }
    /**
     * Gets the table name 
     *
     * @return string Returns the table name
     */
    public function get_table_name()
    {
        return $this->table_name;
    }
    /**
     * Gets the column name used as default filter
     *
     * @return string Returns the column name
     */
    public function get_default_filter_column_name()
    {
        return $this->column_filter_name;
    }

    /**
     * @var GETService Defines the GET web service request
     */
    public $GET;
    /**
     * @var boolean If sets to true the service allows GET requests
     * By default the service allows GET requests
     */
    public $enable_GET;


    /**
     * @var MysteriousParser The query result parser
     */
    public $parser;

    /**
     * @var FieldDefintion[] The table fields definitions
     */
    public $table_fields;
    /**
     * @var ParameterCollection The web service parameter collection
     */
    public $parameters;
    /**
     * @var bool True if the response is returned as a
     * JSON string otherwise is returned as QueryResult object
     */
    public $response_is_encoded;
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
        $this->response_is_encoded = true;
        if (is_null($table_def))
            $this->table_fields = $this->connector->get_table_definition($this->table_name);
        else
            $this->table_fields = $table_def;
        $this->primary_key = $primary_key;
        $this->parser = new MysteriousParser($this->table_fields);
        $this->database = $this->connector->database_name;
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->access_is_allowed = true;
        $this->get_body();
        $this->get_parameters();
        //Por default se permiten todos los servicios
        $this->enable_GET = true;
        //Se inicializan los métodos a los que tiene disponible el servidor.
        $this->GET = new GETService($this);
        if (method_exists($this, "GETServiceTask"))
            $this->GET->service_task = "GETServiceTask";
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
    /**
     * Initialize the web service parameters.
     * To change the default parameters change the
     * @return void
     */
    public function get_parameters()
    {
        $this->parameters = new ParameterCollection();
        if (UrabeSettings::$parameter_mode == URL_PARAM)
            $this->parameters->get_url_parameters();
        else if (UrabeSettings::$parameter_mode == GET_PARAM)
            $this->parameters->get_variables();
        else if (UrabeSettings::$parameter_mode == GET_AND_URL_PARAM) {
            $this->parameters->get_url_parameters();
            $this->parameters->get_variables();
        }
    }
    /**
     * Gets the service response
     *
     * @param boolean $pretty_print If true the response is printed in the pretty JSON format
     * @return QueryResult|string The web service response
     */
    public function get_response($pretty_print = false)
    {
        try {

            if (!$this->access_is_allowed) {
                http_response_code(403);
                throw new Exception(sprintf(ERR_SERVICE_RESTRICTED, $this->method));
            }

            //Update callback if costume action is found
            if ($this->request_data->in_GET_variables(CAP_URABE_ACTION))
                $this->{$this->request_data->method}->service_task = $this->request_data->get_variables[CAP_URABE_ACTION];
            //Gets the web service response
            $result = $this->get_service_response($this->request_data->method);
            //Prints result with HTML format or just a plain JSON string
            return $pretty_print ? pretty_print_format($result) : json_encode($result);
        } catch (Exception $e) {
            throw new Exception(ERR_SERVICE_RESPONSE . $e->getMessage(), $e->getCode());
        }
    }


    /**
     * Gets the web service response 
     * @param string $request_method The request method verbose
     * @throws Exception An exception is raised if an error occurred executing the web request
     * @return UrabeResponse The web service response
     */
    private function get_service_response($request_method)
    {
        try {
            switch ($request_method) {
                case 'GET':
                    $result = $this->execute_response($this->GET, $this->enable_GET);
                    break;
    // case 'PUT':
    //     $result = $this->get_server_response($this->PUT, $this->enable_PUT);
    //     break;
    // case 'POST':
    //     $result = $this->get_server_response($this->POST, $this->enable_POST);
    //     break;
    // case 'DELETE':
    //     $result = $this->get_server_response($this->DELETE, $this->enable_DELETE);
    //     break;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Gets the web service response if the service is enabled
     *
     * @param HasamiRestfulService $service The Restful service
     * @param bool $is_available True if the service is available, false if is restricted
     * @return UrabeResponse The web service response
     */
    private function execute_response($service, $is_available)
    {
        if ($is_available) {
            http_response_code(200);
            $result = $service->get_response();
        } else {
            http_response_code(403);
            throw new Exception(sprintf(ERR_SERVICE_RESTRICTED, $this->method));
        }
        return $result;
    }

}
?>