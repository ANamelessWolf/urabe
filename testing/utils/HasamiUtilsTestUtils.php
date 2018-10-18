<?php
include_once "../src/ORACLEKanojoX.php";
include_once "../src/PGKanojoX.php";
include_once "../src/MYSQLKanojoX.php";
include_once "TestUtils.php";
/**
 * Picks a Kanojo database connecter depending on the given driver
 *
 * @param object $body The request body
 * @return KanojoX The database connector
 */
function test_write_connection_file($body)
{
    $kanojo = pick_connector($body->driver, $body);
    $kanojo->init($body->connection);
    return save_connection("../tmp/conn_file.json", $kanojo);
}
?>