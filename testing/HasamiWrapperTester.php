<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once "./utils/HasamiWrapperTestUtils.php";

/**
 * HasamiWrapperTester Class
 * 
 * This class is used to test the functionality of a web service built with HasamiWrapper 
 * @version 1.0.0
 * @api Makoto Urabe DB Manager database connector
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class HasamiWrapperTester extends HasamiWrapper
{
    const TABLE_NAME = "users";
    /**
     * Initialize a new instance of the test service
     */
    public function __construct()
    {
        $connector = get_KanojoX_from_file("../tmp/conn_file.json");
        parent::__construct($connector->schema . "." . self::TABLE_NAME, $connector, "id");

        if ($this->request_data->GET_variable_equals("selection_mode", "advance"))
            $this->set_service_task("GET", "advance_select");
    }

    /**
     * This functions test the advance selection, this function overrides the default selection
     * and its defined using the Wrapper set_service_task passing as parameter the request method "GET"
     * and this function name. Also for this example this function expects that the GET variables contains
     * "username" and "password"
     *
     * @param WebServiceContent $data The web service content
     * @param Urabe $urabe The database manager
     * @return UrabeResponse The selection response
     */
    public function advance_select($data, $urabe)
    {
        if ($data->validate_obligatory_GET_variables("username", "password")) {
            $table_name = $this->table_name;
            //Use universal format @paramIndex for place holders
            $condition = "u_name = @1 AND u_pass = @2";
            $sql = $urabe->format_sql_place_holders("SELECT * FROM $table_name WHERE $condition");
            $result = $urabe->select($sql, $data->pick_GET_variable("username", "password"));
            return $result;
        }
    }

    /**
     * Tests the service data current status this function should be called
     *
     * @return void
     */
    public function u_action_status()
    {
        return $this->get_status();
    }
}
$service = new HasamiWrapperTester();
$result = $service->get_response();
echo (is_string($result) ? $result : json_encode($result, JSON_PRETTY_PRINT));
?>