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
    }
    /**
     * Tests the service data current status
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