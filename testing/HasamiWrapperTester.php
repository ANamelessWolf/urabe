<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once "utils/HasamiWrapperTestUtils.php";

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
        //This changes default status for the given services
        $this->set_service_status("PUT", ServiceStatus::AVAILABLE);
        $this->set_service_status("DELETE", ServiceStatus::AVAILABLE);
        //This mode will simulate the user is logged executing the function 
        $this->set_service_status("POST", ServiceStatus::LOGGED);

        //This only applies if GET verbose detected
        if ($this->request_data->method == "GET" && $this->request_data->GET_variable_equals("selection_mode", "advance"))
            $this->set_service_task("GET", "advance_select");
        //This only applies if POST verbose is detected
        if ($this->request_data->method == "POST" && $this->request_data->GET_variable_equals("update_mode", "advance"))
            $this->set_service_task("POST", "advance_update");
        //This only applies if POST verbose is detected
        if ($this->request_data->method == "DELETE" && $this->request_data->GET_variable_equals("delete_mode", "advance"))
            $this->set_service_task("DELETE", "advance_delete");

    }

    /**
     * This functions simulates the validation access
     * via a selection of an user id via a user password and username.
     * The username and password will be send in the body.
     *
     * @return boolean True if the validation access succeed
     */
    protected function validate_access()
    {
        if ($this->request_data->validate_obligatory_body_properties("username", "password")) {
            $user_name = $this->request_data->body->username;
            $password = $this->request_data->body->password;
            $response = $this->select_user($this->urabe, array($user_name, $password));
            //Should select at least one row.
            //This simulates a validation access
            return $response->size > 0;
        }
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
        $column_names = parent::get_insert_columns();
        //Ignore last login column
        unset($column_names["last_login"]);
        return $column_names;
    }

    /**
     * This functions test the advance selection, this function overrides the default selection
     * and its defined using the Wrapper set_service_task passing as parameter the request method "GET"
     * and the function name. Also for this example, this function expects that the GET variables contains
     * "username" and "password"
     *
     * @param WebServiceContent $data The web service content
     * @param Urabe $urabe The database manager
     * @return UrabeResponse The selection response
     */
    public function advance_select($data, $urabe)
    {
        if ($data->validate_obligatory_GET_variables("username", "password")) {
            //Use universal format @paramIndex for place holders
            $parameters = $data->pick_GET_variable("username", "password");
            return $this->select_user($urabe, $parameters);
        }
    }
    /**
     * This functions test the advance update, this function overrides the default update actions
     * using the Wrapper set_service_task passing as parameter the request method "POST"
     * and the function name. Also for this example, this function expects that the some parameters are defined in the
     * condition body
     *
     * @param WebServiceContent $data The web service content
     * @param Urabe $urabe The database manager
     * @return UrabeResponse The selection response
     */
    public function advance_update($data, $urabe)
    {
        //Validate body
        $data->validate_obligatory_body_properties(NODE_VAL, "adv_condition");
        //Extract values
        $percent = $this->format_value($urabe->get_driver(), "percent", $data->body->adv_condition->percent);
        $is_active = $this->format_value($urabe->get_driver(), "is_active", $data->body->adv_condition->is_active);
        //Build condition
        $condition = "percent > " . $percent . " AND " . "is_active = '" . $is_active . "'";
        $values = $this->format_values($data->body->{NODE_VAL});
        //Update
        return $urabe->update($this->table_name, $values, $condition);
    }

    /**
     * This functions test the advance delete, this function overrides the default delete action 
     * using the Wrapper set_service_task, passing as parameter the request method "DELETE"
     * and the function name. Also for this example, this function expects that some parameters are defined in the
     * condition body
     *
     * @param WebServiceContent $data The web service content
     * @param Urabe $urabe The database manager
     * @return UrabeResponse The selection response
     */
    public function advance_delete($data, $urabe)
    {
        //Validate body
        $data->validate_obligatory_body_properties("adv_condition");
        //Extract values
        $percent = $this->format_value($urabe->get_driver(), "percent", $data->body->adv_condition->percent);
        $is_active = $this->format_value($urabe->get_driver(), "is_active", $data->body->adv_condition->is_active);
        //Build condition
        $condition = "percent > " . $percent . " AND " . "is_active = '" . $is_active . "'";
        //Update
        return $urabe->delete($this->table_name, $condition);
    }

    /**
     * Tests a function that only is allowed to execute in POST or PUT
     * By default callback functions received the web service content and the database connector
     * @param WebServiceContent $data The web service content
     * @param Urabe $urabe The database manager
     * @return UrabeResponse The urabe response
     */
    public function u_action_test_restrict_call_access($data, $urabe)
    {
        $data->restrict_by_content("POST", "PUT");
        $response = new UrabeResponse();
        return $response->get_response("You are allowed", array());
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
    /**
     * Selects and user from the database
     *
     * @param Urabe $urabe The database manager
     * @param array $parameters The parameters needed to select the user,
     * Should be user_name and password
     * @return object The message response
     */
    private function select_user($urabe, $parameters)
    {
        $table_name = $this->table_name;
        $condition = "u_name = @1 AND u_pass = @2";
        $sql = $urabe->format_sql_place_holders("SELECT * FROM $table_name WHERE $condition");
        $result = $urabe->select($sql, $parameters);
        return $result;

    }
}
$service = new HasamiWrapperTester();
$result = $service->get_response();
echo (is_string($result) ? $result : json_encode($result, JSON_PRETTY_PRINT));
?>