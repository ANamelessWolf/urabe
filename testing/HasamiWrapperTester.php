<?php
include_once "./utils/HasamiWrapper.php";
include_once "../src/HasamiWrapper.php";
/**
* HasamiWrapperTestService Class
* 
* This class is used to test the functionality of a web service built with HasamiWrapper 
* @version 1.0.0
* @api Makoto Urabe DB Manager database connector
* @author A nameless wolf <anamelessdeath@gmail.com>
* @copyright 2015-2020 Nameless Studios
*/
class HasamiWrapperTestService extends HasamiWrapper
{
    /**
     * The connection data
     *
     * @var [type]
     */
    public $connection;
    /**
     * Initialize a new instance of the test service
     */
    public function __construct()
    {

    }
}
?>