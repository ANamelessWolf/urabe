<?php

namespace Urabe;

use Exception;
use ReflectionClass;
use ReflectionMethod;
use Urabe\Config\UrabeSettings;
use Urabe\DB\DBUtils;
use Urabe\Service\WebServiceContent;
use Urabe\Service\ServiceCollection;
use Urabe\Service\GETService;
use Urabe\Service\PUTService;
use Urabe\Service\POSTService;
use Urabe\Service\DELETEService;
use Urabe\Config\ServiceStatus;
use Urabe\Utils\HasamiUtils;
use Urabe\Urabe;

/**
 * Hasami Web Service Class
 * This class encapsulate and manage a web service that consults and edit a database
 * via the verbose PUT, POST, DELETE and GET
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class HasamiWebService
{
    /**
     * The web service request content
     *
     * @var WebServiceContent The web service content
     */
    public $data;
    /**
     * @var Urabe The database manager
     */
    public $urabe;
    /*
     *@var string The primary key column name
    */
    public $primary_key;
    /**
     * @var ServiceCollection The Restful services collection
     */
    protected $services;
    /**
     * @var KanojoX The database connection data
     */
    private $connection;
    /**
     * @var string The path to the JSON definition file
     */
    private $json_table;
    /**
     * Initialize a new instance for the Hasami web service
     * The name of the table is used to find the table definition.
     * The path the table definition is defined on the UrabeSettings variable
     * table_definitions_path
     *
     * @param KanojoX $connection The database connection
     * @param string $table_name The name of the table
     * @param string $primary_key The primary key column name
     */
    public function __construct($connection, $table_name, $primary_key = null)
    {
        $table_dir = UrabeSettings::$table_definitions_path;
        $this->connection = $connection;
        $this->json_table = $table_dir . DIRECTORY_SEPARATOR . "$table_name.json";
        if (!file_exists($this->json_table))
            throw new Exception(sprintf("No existe la definición de tabla en el archivo '%s'", $this->json_table));
        $table = DBUtils::createTableDefinitionFromJSON($this->json_table);
        $this->urabe = new Urabe($this->connection, $table);
        $this->data = new WebServiceContent();
        $this->primary_key = $primary_key;
    }
    /**
     * Initialize the services for HasamiWrapper, by default
     * the web services had available accessibility
     * @param int $get_satus The default status for the GET Service
     * @param int $put_status The default status for the PUT Service
     * @param int $post_status The default status for the POST Service
     * @param int $delete_status The default status for the DELETE Service
     * @return void
     */
    protected function init_services($get_satus = ServiceStatus::AVAILABLE, $put_status = ServiceStatus::AVAILABLE, $post_status = ServiceStatus::AVAILABLE, $delete_status = ServiceStatus::AVAILABLE)
    {
        $this->services = new  ServiceCollection();
        $verbose = $this->data->method;
        switch ($verbose) {
            case 'GET':
                $this->check_block_status($get_satus, "GET");
                $filter = $this->data->get_vars->get("filter");
                if (!is_null($this->primary_key) && !is_null($filter))
                    $this->services->set("GET", new GETService($this->data, $this->urabe, $this->primary_key . "=" . $filter), $get_satus);
                else
                    $this->services->set("GET", new GETService($this->data, $this->urabe, null), $get_satus);
                break;
            case 'PUT':
                $this->check_block_status($put_status, "PUT");
                $this->services->set("PUT", new PUTService($this->data, $this->urabe, $this->primary_key), $put_status);
                break;
            case 'POST':
                $this->check_block_status($post_status, "POST");
                $this->services->set("POST", new POSTService($this->data, $this->urabe, $this->primary_key), $post_status);
                break;
            case 'DELETE':
                $this->check_block_status($delete_status, "DELETE");
                $this->services->set("DELETE", new DELETEService($this->data, $this->urabe, $this->primary_key), $delete_status);
                break;
        }
    }
    /**
     * Obtains the get service
     *
     * @return GETService The Get Service
     */
    public function get_service_GET()
    {
        $service = $this->services->get("GET");
        return $service;
    }
        /**
     * Obtains the PUT service
     *
     * @return PUTService The Get Service
     */
    public function get_service_PUT()
    {
        $service = $this->services->get("PUT");
        return $service;
    }
        /**
     * Obtains the POST service
     *
     * @return POSTService The POST Service
     */
    public function get_service_POST()
    {
        $service = $this->services->get("POST");
        return $service;
    }
        /**
     * Obtains the DELETE service
     *
     * @return DELETEService The Get Service
     */
    public function get_service_DELETE()
    {
        $service = $this->services->get("DELETE");
        return $service;
    }
    /**
     * Check the service blocked status
     *
     * @param int $status The service status
     * @param string $verbose The verbose
     * @return void
     */
    public function check_block_status($status, $verbose)
    {
        if ($status == ServiceStatus::BLOCKED) {
            UrabeSettings::$http_error_code = 403;
            throw new Exception(sprintf(ERR_SERVICE_RESTRICTED, $verbose));
        }
    }

    /**
     * Set the task for the current service
     *
     * @param string $verbose The verbose name
     * @param object $caller The class that has the function
     * @param string $service_task The function name
     * @return void
     */
    protected function set_task($verbose, $caller, $service_task)
    {
        if ($this->services->exists($verbose)) {
            $service = $this->services->get($verbose);
            $service->caller = $caller;
            $service->service_task = $service_task;
        }
    }

    /**
     * Gets the service status
     */
    public function get_status()
    {
        $table = $this->urabe->parser->table_definition;
        return (object)array(
            "Status" => $this->services->check_accessibility($this->data->method),
            "Content" => $this->request_data,
            "Table" => array(
                "name" => $table->table_name,
                "primary_key" => $this->primary_key,
                "columns" => $table->get_column_names(),
                "selection_filter" => $this->selection_filter
            ),
        );
    }
    /**
     * This function list all available web service special actions
     * all actions are identified by starting with the prefix u_action
     * @return array The list of available actions inside an array
     */
    private function get_available_actions()
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
     * This functions validates the access of a service called via verbose
     * Can be used to validate a login or a group access validation, this function should be overwritten in 
     * the children class.
     *
     * By default returns true
     * @return boolean True if the validation access succeed
     */
    protected function validate_login()
    {
        return true;
    }
    /**
     * Gets the web service response 
     * @param HasamiRESTfulService $service The current web service
     * @param string $verbose The request method verbose
     * @throws Exception An exception is thrown if an error occurred executing the web request
     * @return UrabeResponse The web service response
     */
    private function get_service_response($service, $verbose)
    {
        try {
            $status = $this->services->check_accessibility($verbose);
            switch ($status) {
                case ServiceStatus::AVAILABLE:
                    http_response_code(200);
                    return $service->get_response();
                    break;
                case ServiceStatus::LOGGED:
                    if ($this->validate_login()) {
                        http_response_code(200);
                        return $service->get_response();
                    } else {
                        UrabeSettings::$http_error_code = 403;
                        throw new Exception(sprintf(ERR_SERVICE_RESTRICTED, $verbose));
                    }
                    break;
                default:
                    UrabeSettings::$http_error_code = 500;
                    throw new Exception(sprintf(ERR_VERBOSE_NOT_SUPPORTED, $verbose));
                    break;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Checks the urabe action
     *
     * @param HasamiRESTfulService $service The Restful service
     * @return void
     */
    public function check_urabe_action($service)
    {
        $urabe_action = $this->data->get_vars->get(VAR_URABE_ACTION);
        if (isset($urabe_action)) {
            $actions = $this->get_available_actions();
            $service->caller = $this;
            if (in_array($urabe_action, $actions))
                $service->service_task = CAP_URABE_ACTION . $urabe_action;
            else {
                http_response_code(500);
                throw new Exception(sprintf(ERR_INVALID_ACTION, $urabe_action));
            }
        }
    }

    /**
     * Gets the service response
     * First check if an action exists on the service, The action service is passed in the GET Variable action
     * If the action exists but is not defined an exception is thrown, if no action is passed the task is directly taken
     * from the Request method wrapper.
     *
     * @return UrabeResponse The web service response, if the PP variable is found in GET Variables, the result is a formatted HTML
     **/
    public function get_response()
    {
        try {
            $verbose = $this->data->method;
            $service = $this->services->get($verbose);
            //1: Check the Urabe action
            $this->check_urabe_action($service);
            //2: Gets the service response
            $result = $this->get_service_response($service, $verbose);
            return $result;
        } catch (Exception $e) {
            throw new Exception(ERR_SERVICE_RESPONSE . $e->getMessage(), $e->getCode());
        }
    }
    /**
     * Prints the response
     *
     * @param UrabeResponse $result The web service response
     * @return void
     */
    public function print_response($result)
    {
        //If pretty print is enable prints result with HTML format
        $format_result = $this->data->url_params;
        if ($format_result) {
            $style = $this->data->get_vars->get(KEY_PRETTY_PRINT);
            $style = isset($style) ? strtolower($style) : "";
            switch ($style) {
                case "light":
                    $style = UrabeSettings::$light_pp_style;
                    break;
                case "dark":
                    $style = UrabeSettings::$dark_pp_style;
                    break;
                default:
                    $style = UrabeSettings::$default_pp_style;
                    break;
            }
            $response = HasamiUtils::pretty_print_format($this->data, $style);
        } else
            $response = json_encode($result, JSON_PRETTY_PRINT);
        echo $response;
    }
}
