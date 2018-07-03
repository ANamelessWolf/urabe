<?php 
include_once "JsonPrettyPrint.php";
/**
 * Json Pretty Print Class
 * 
 * This class creates a HTML format from a JSON object, that are based on a Light background
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class JsonPrettyPrintLight extends JsonPrettyPrint
{
    /**
     * __construct
     *
     * Initialize a new instance of the JSON Pretty Print format
     *
     */
    function __construct()
    {
        $this->symbol_color = "#808388";
        $this->boolean_value_color = "#00a2e8";
        $this->property_name_color = "#000";
        $this->text_value_color = "#008000";
        $this->number_value_color = "#e23400";
    }
}
?>