<?php
include_once("Warai.php");
/**
 * Urabe web service settings
 * @version 1.0.0
 * @api Makoto Urabe Oracle
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class UrabeSettings
{
    /**
     * @var string Defines the type of parameters to use by the web services.
     * Available modes URL_PARAM or GET_PARAM or GET_AND_URL_PARAM
     */
    public static $parameter_mode = URL_PARAM;

}
?>