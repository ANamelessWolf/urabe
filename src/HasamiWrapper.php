<?php
include_once "GETService.php";
include_once "PUTService.php";
include_once "DELETEService.php";
include_once "POSTService.php";
include_once "IHasami.php";

/**
 * A Hasami Wrapper is a web service wrapper Class
 * This class encapsulate and manage web service verbose PUT, POST, DELETE and GET
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class HasamiWrapper implements IHasami
{
    /************************
     *** Protected fields ***
     ************************/

    /**
     * The web service request content
     *
     * @var WebServiceContent The web service content
     */
    protected $request_data;
    /**
     * @var Urabe The database manager
     */
    protected $urabe;
    /**
     * @var array The table fields definitions
     * Can be loaded from a query or from a JSON string
     */
    protected $table_definition;
    /**
     * @var string The table name
     */
    protected $table_name;
    /**
     * @var string The Selection filter collection used by GET service
     */
    protected $selection_filter;
    /**
     * @var string Sets or gets the table primary key column name
     * This field is used when constructing a condition for UPDATE or DELETE
     */
    protected $primary_key;
    /**
     * @var array The Restful services managed by hasami wrapper in an array of HasamiRestfulService
     * Each value is index by the verbose name
     */
    protected $services;
    /**
     * @var array The Restful services available status, the service only execute when the
     * status is active or by succeeding in authorization mode
     * Each value is index by the verbose name
     */
    protected $services_status;

    /************************************
     *** Public access to properties  ***
     ***   via getters and setters    ***
     ************************************/

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
     * Gets the table definition as an array of FieldDefinition
     * @return array The table fields as an array of FieldDefinition
     */
    public function get_table_definition()
    {
        $this->table_definition = $this->urabe->get_table_definition($this->table_name);
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
    public function get_selection_filter()
    {
        return $this->selection_filter;
    }
    /**
     * Sets the selection filter, used by the GET service
     * in its default mode
     * @param string $condition The filter condition
     * @return string Returns the column name
     */
    public function set_selection_filter($condition)
    {
        $this->selection_filter = $condition;
    }
    /**
     * Gets the column name used as primary key
     *
     * @return string Returns the column name
     */
    public function get_primary_key_column_name()
    {
        return $this->primary_key;
    }
    /**
     * Gets the table INSERT column names
     * By default the insertion columns are all the columns from the table definition
     * except by the primary key column
     *
     * @return array Returns the column names in an array of strings
     */
    public function get_insert_columns()
    {
        $columns = array();
        //var_dump($this->table_definition);
        for ($i = 0; $i < sizeof($this->table_definition); $i++) {
            if ($this->table_definition[$i]["column_name"] != $this->primary_key)
                array_push($columns, $this->table_definition[$i]["column_name"]);
        }
        return $columns;
    }
    /**
     * Gets the service manager by the verbose type
     * @param string $verbose The service verbose type
     * @return HasamiRestfulService The service manager
     */
    public function get_service($verbose)
    {
        return $this->services[$verbose];
    }
    /**
     * Gets the service status assigned to the given service
     * @param string $verbose The service verbose type
     * @return ServiceStatus The service current status
     */
    public function get_service_status($verbose)
    {
        return $this->services_status[$verbose];
    }
    /**
     * Sets the service status to the given service name
     * @param string $verbose The service verbose type
     * @param ServiceStatus $status The service status
     * @return void
     */
    public function set_service_status($verbose, $status)
    {
        $this->services_status[$verbose] = $status;
    }

    /*******************
     *** Constructor ***
     *******************/

    /**
     * __construct
     *
     * Initialize a new instance of a HasamiWrapper Class
     * @param string $full_table_name The full table name, used to wrap SELECT, UPDATE, INSERT AND DELETE actions
     * @param KanojoX $connector The database connector 
     * @param string|NULL $primary_key The name of the primary key.
     * @param FieldDefinition[] $table_definition The table definition, if null
     * the table definition are obtained via a selection query.
     */
    public function __construct($full_table_name, $connector, $primary_key = null, $table_def = null)
    {
        $this->table_name = $full_table_name;
        $this->urabe = new Urabe($connector);
        $this->primary_key = $primary_key;
        //Selecting table definition and table definition parser
        if (is_null($table_def))
            $this->table_definition = $this->urabe->get_table_definition($this->table_name);
        else
            $this->table_definition = $table_def;
        //Start with the table definition parser
        $this->urabe->set_parser(new MysteriousParser($this->table_definition));
        //Get the request content
        $this->request_data = new WebServiceContent();
        //Initialize services
        $this->services = $this->init_services();
        $this->services_status = $this->init_service_status();
    }
    /**
     * Initialize the services for HasamiWrapper
     *
     * @return array The Restful services supported by this wrapper
     */
    protected function init_services()
    {
        if (property_exists($this->request_data->body, $primary_key))
            $condition = "$primary_key = " . $this->request_data->body->{$primary_key};
        else
            $condition = null;
        if (property_exists($this->request_data->body, 'filter'))
            $this->selection_filter = $this->request_data->body->filter;
        return array(
            "GET" => new GETService($this),
            "PUT" => new PUTService($this),
            "POST" => new POSTService($this, $condition),
            "DELETE" => new DELETEService($this, $condition)
        );
    }
    /**
     * Initialize the service status for the HasamiWrapper
     * The default configuration can be set in the Urabe settings
     *
     * @return array The Restful services supported by this wrapper
     */
    protected function init_service_status()
    {
        return array(
            "GET" => KanojoX::$settings->default_GET_status,
            "PUT" => KanojoX::$settings->default_PUT_status,
            "POST" => KanojoX::$settings->default_POST_status,
            "DELETE" => KanojoX::$settings->default_DELETE_status,
        );
    }
    /**
     * Gets the service status
     */
    public function get_status()
    {
        $keys = array_keys($this->services_status);
        $status = array();
        foreach ($keys as &$key)
            $status[$key] = ServiceStatus::getName($this->get_service_status($key));

        return (object)array(
            "Status" => $status,
            "Content" => $this->request_data,
            "Connection" => $this->urabe->get_connection_data(),
            "Table" => array(
                "name" => $this->table_name,
                "primary_key" => $this->primary_key,
                "columns" => $this->table_definition,
                "selection_filter" => $this->selection_filter
            ),
            "Actions" => $this->get_available_actions()
        );
    }
    /**
     * This function list all available web service special actions
     * all actions are identified by starting with the prefix u_action
     * @return array The list of available actions inside an array
     */
    function get_available_actions()
    {
        $class_name = get_class($this);
        $class = new ReflectionClass($class_name);
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        $actions = array();
        $uSize = strlen(CAP_URABE_ACTION);
        foreach ($methods as &$method) {
            if ($method->class == $class_name && substr($method->name, 0, $uSize) == CAP_URABE_ACTION)
                array_push($actions, substr($method->name, $uSize));
        }
        return $actions;
    }
    /**
     * Gets the service response
     * First check if an action exists on the service, The action service is passed in the GET Variable action
     * If the action exists but is not defined an exception is thrown, if no action is passed the task is directly taken
     * from the Request method wrapper.
     *
     * @return UrabeResponse|string The web service response, if the PP variable is found in GET Variables, the result is a formatted HTML
  
    public function get_response()
    {
        try {
            if (in_array(CAP_URABE_ACTION, $this->request_data->get_variables)) {
                $actions = $this->get_actions();
                $action = $this->request_data->get_variables[CAP_URABE_ACTION];
                $isSupported = array_key_exists($request_method, $this->services);
                //Execute if the action exist otherwise throw an Exception
                if (in_array($action, $actions)) {
                    $service = $this->get_service($request_method);
                    $service->service_task = CAP_URABE_ACTION . $action;
                } else {
                    http_response_code(500);
                    throw new Exception(sprintf(ERR_INVALID_ACTION, $action));
                }
            }
            $result = $service->get_service_response();
            //If pretty print is enable prints result with HTML format
            return in_array(KEY_PRETTY_PRINT, $this->request_data->get_variables) ? pretty_print_format($result) : $result;
        } catch (Exception $e) {
            throw new Exception(ERR_SERVICE_RESPONSE . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Gets the web service response 
     * @param string $request_method The request method verbose
     * @throws Exception An exception is thrown if an error occurred executing the web request
     * @return UrabeResponse The web service response

    private function get_service_response($request_method)
    {
        try {
            if (array_key_exists($request_method, $this->services)) {
                $status = $this->get_service_status($request_method);
                if ($status == ServiceStatus::AVAILABLE || ($status == ServiceStatus::LOGGED && $this->check_login_session())) {
                    http_response_code(200);
                    $service = $this->get_service($request_method);
                    $result = $service->get_response();
                } else if ($status == ServiceStatus::LOGGED) {
                    http_response_code(403);
                    throw new Exception(sprintf(ERR_SERVICE_RESTRICTED, $this->method));
                } else {
                    http_response_code(500);
                    throw new Exception(sprintf(ERR_VERBOSE_NOT_SUPPORTED, $this->method));
                }
            } else {
                http_response_code(500);
                throw new Exception(sprintf(ERR_VERBOSE_NOT_SUPPORTED, $this->method));
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
     */


    /**
     * This function list with all available actions for the current web service
     * Only functions starting with the word "action" are listed
     *                                  
     * @return array The list of available actions as an array of strings
     */
    private function get_actions()
    {
        $functions = get_defined_functions();
        $functions = $functions["user"];
        $action_funcs = array();
        $actionSize = strlen(CAP_URABE_ACTION);
        for ($i = 0; $i < sizeof($functions); $i++) {
            if (strlen($functions[$i]) > $actionSize && substr($functions[$i], 0, $actionSize) == CAP_URABE_ACTION)
                array_push($test_func, str_replace(array(SERVICE_FUNC_PREFIX), array(), $functions[$i]));
        }
        return $action_funcs;
    }
}
?>