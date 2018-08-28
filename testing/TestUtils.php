<?php

/**
 * This file contains functions that help to test Urabe project
 *  
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */

/**
 * @var string TEST_VAR_NAME
 * The name of the variable in GET Vars that saves the test to run
 */
const TEST_VAR_NAME = "test";

/**
 * Picks a Kanojo database connecter depending on the given driver
 *
 * @param string $driver The driver name; ORACLE|PG|MYSQL
 * @param mixed $body The request body
 * @throws Exception An exception is thrown if the driver is not supported
 * @return KanojoX The database connector
 */
function pick_connector($driver, $body)
{
    if ($driver == "ORACLE") {
        $kanojo = new ORACLEKanojoX();
        $kanojo->owner = $body->owner;
    } else if ($driver == "PG") {
        $kanojo = new PGKanojoX();
        $kanojo->schema = $body->schema;
    } else if ($driver == "MYSQL")
        $kanojo = new MYSQLKanojoX();
    else
        throw new Exception("Driver " + (isset($driver) ? $driver . "not supported." : " not valid."));
    return $kanojo;
}
?>