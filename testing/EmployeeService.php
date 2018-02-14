<?php
include_once("../urabe/KanojoX.php");
include_once("../urabe/Urabe.php");
include_once("../urabe/QueryResult.php");
include_once("../urabe/HasamiWrapper.php");
include_once("../urabe/ICustomizableService.php");
class EmployeeService extends HasamiWrapper implements ICustomizableService
{

    public function __construct()
    {

        $kanojo = new KanojoX();
        $kanojo->host = "10.0.0.3";
        $kanojo->user_name = "riviera";
        $kanojo->password = "r4cks";
        $table_def = open_json_file("table_test_definition.json");
        $table_def = FieldDefinition::parse_result($table_def);
        parent::__construct("BASES", $kanojo, "ID_REVIT", $table_def);
    }
    /**
     * Defines the task that is called when a GET verbose is
     * called
     *
     * @param HasamiRESTfulService $service The Restfull service
     * @return QueryResult|string The server response
     */
    public function GETServiceTask($service)
    {
       
        return $service->default_GET_action();
    }

}
?>