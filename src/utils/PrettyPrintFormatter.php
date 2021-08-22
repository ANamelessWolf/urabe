<?php

namespace Urabe\Utils;

use Urabe\Utils\JsonPrettyStyle;
use Urabe\Utils\JsonPrettyPrint;

/**
 * Pretty Print Formatter Class
 * 
 * This class creates a HTML format from a JSON object
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios 
 */
class PrettyPrintFormatter
{
    /**
     * @var JsonPrettyPrint The json pretty print formatter
     */
    public $formatter;
    /**
     * @var string The build html
     */
    public $html;
    /**
     * @var string The header color style
     */
    public $header_style;
    /**
     * __construct
     *
     * Initialize a new instance of the JSON pretty print formatter class.
     * @param JsonPrettyStyle $style The JSON pretty format style
     * @param bool $bg_black True if a dark background is applied otherwise 
     *the background will be white 
     */
    public function __construct($style, $bg_black = true)
    {
        $bg_color = $bg_black ? '#090909;' : '#B1D9D2';
        $this->header_style = $bg_black ? "white" : "black";
        $this->formatter = new JsonPrettyPrint($style);
        $this->html .= '<html><head>' .
            '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">' .
            '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>' .
            '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>' .
            '<style>' .
            'body { background-color: ' . $bg_color . '} ' .
            '</style>' .
            '</head>' .
            '<body>';
    }
    /**
     * Appends a response result as an HTML code
     *
     * @param UrabeResponse $response The response
     * @return void
     */
    public function append_response($response)
    {
        $this->html .= '<div class="container">';
        $json = json_decode(json_encode($response), true);
        $this->html .= $this->formatter->get_format($json);
        $this->html .= "</div>";
    }
    /**
     * Appends a header to the html
     * Use H2 Tag
     *
     * @param string $title The message to add
     * @return void
     */
    public function append_title($title)
    {
        $this->html .= '<div class="container">';
        $this->html .= sprintf('<h2 style="color:%s;">%s</h2>',$this->header_style, $title);
        $this->html .= "</div>";
    }
    /**
     * Close the html document
     * @return void
     */
    public function close()
    {
        $this->html .= '</body></html>';
    }
    /**
     * Prints the Html
     *
     * @return void
     */
    public function print(){
        echo $this->html;
    }
}
