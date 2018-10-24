<?php 

require_once "JsonPrettyStyle.php";

/**
 * Json Pretty Print Class
 * 
 * This class creates a HTML format from a JSON object
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios 
 */
class JsonPrettyPrint
{

    const HTML_FORMAT_FONT_LIGHTER = '<span style="color:%s; padding-left:%spx; font-weight:lighter">';
    const HTML_FORMAT_FONT_BOLD = '<span style="color:%s; padding-left:%spx; font-weight:bold">';
    const HTML_FORMAT_CLOSE ='</span>';
    const LEFT_PADDING_PX = "20";


    /**
     * Defines the given JSON Style
     *
     * @var JsonPrettyStyle The JSON Style
     */
    public $style;

    /**
     * __construct
     *
     * Initialize a new instance of the JSON pretty print class.
     * @param JsonPrettyStyle $style The PP JSON style
     */
    function __construct($style = null)
    {
        if (is_null($style))
            $this->style = KanojoX::$settings->default_pp_style;
        else
            $this->style = $style;
    }

    public function format_json($json, $level)
    {
        if (is_object($json))
            $html .= $this->format_object($json, $level);
        else if (is_array($json))
            $html .= $this->format_array($json, $level);
        else
            $html .= $this->format_value($json, 0);
        return $html;
    }

    public function format_object($json, $level, $offset = 0)
    {
        $html .= $this->new_line();
        $html = $this->print_symbol("{", $offset);
        $properties = array_keys(get_object_vars($json));
        for ($i = 0; $i < count($properties); $i++) {
            $html .= $this->new_line();
            $html .= $this->print_property($properties[$i], $level + 1);
            $html .= $this->print_symbol(" : ", 0);
            $html .= $this->format_json($json->{$properties[$i]}, $level + 1);
            if ($i < (count($properties) - 1)) {
                $html .= $this->print_symbol(", ", 0);
            }
        }
        $html .= $this->new_line();
        $html .= $this->print_symbol("}", $level);
        return $html;
    }

    public function format_array($array, $level, $offset = 0)
    {
        if (count($array) == 0)
            $html .= $this->print_symbol(" [ ] ", 0);
        else {
            $keys = array_keys($array);
            $is_array_of_objects = is_string($keys[0]);
            $symbol = ($is_array_of_objects ? "{" : "[");
            $html = $this->print_symbol(" $symbol", $offset);

            for ($i = 0; $i < count($array); $i++) {
                if (is_string($keys[$i])) {
                    $html .= $this->new_line();
                    $html .= $this->print_property($keys[$i], $level + 1);
                    $html .= $this->print_symbol(" : ", 0);
                    $html .= $this->format_json($array[$keys[$i]], $level + 1);

                } else {
                    $html .= $this->new_line();
                    $html .= $this->format_value($array[$keys[$i]], $level + 1);
                }
                if ($i < (count($array) - 1))
                    $html .= $this->print_symbol(", ", 0);
            }
            $html .= $this->new_line();
            $symbol = ($is_array_of_objects ? "}" : "]");
            $html .= $this->print_symbol("$symbol", $level);
        }
        return $html;
    }


    public function format_value($value, $level)
    {
        if (is_string($value)) {
            if (strtolower($value) == "true" || strtolower($value) == "false")
                $html .= $this->print_bool_value($value == "true", $level);
            else
                $html .= $this->print_text_value($value, $level);

        } else if (is_numeric($value))
            $html .= $this->print_number_value($value, $level);
        else if (is_null($value))
            $html .= $this->print_null_value($level);
        else
            $html .= $this->format_array($value, $level, $level);
        return $html;
    }


    /**
     * Gets the pretty print format from a JSON object
     *
     * @param stdClass $json The json object
     * @param int $tab The number of times to append a tabulation
     * @param boolean $parent_is_array True if the parent node is an array
     * @return The json in pretty print format
     */
    public function get_format($json)
    {
        return $this->format_json($json, 0);
    }
    /*************************************
     * Values are formatted with as span *
     *************************************/
    /**
     * Prints a symbol with the pretty JSON format.
     *
     * @param string $symbol The symbol to print.
     * @return string The symbol in the pretty JSON format.
     */
    private function print_symbol($symbol, $level)
    {
        return sprintf(self::HTML_FORMAT_FONT_BOLD . '%s'.HTML_FORMAT_CLOSE, $this->style->symbol_color, $level * self::LEFT_PADDING_PX, $symbol);
    }
    /**
     * Prints a property name with the pretty JSON format.
     *
     * @param string $property The property name.
     * @return string The property name in the pretty JSON format.
     */
    private function print_property($property, $level)
    {
        return sprintf(self::HTML_FORMAT_FONT_LIGHTER . '"%s"'.HTML_FORMAT_CLOSE, $this->style->property_name_color, $level * self::LEFT_PADDING_PX, $property);
    }
    /**
     * Prints a text value with the pretty JSON format.
     *
     * @param string $text The text value.
     * @return string The text value in the pretty JSON format.
     */
    private function print_text_value($text, $level)
    {
        return sprintf(self::HTML_FORMAT_FONT_LIGHTER . '"%s"'.HTML_FORMAT_CLOSE, $this->style->text_value_color, $level * self::LEFT_PADDING_PX, $text);
    }
    /**
     * Prints a null value with the pretty JSON format.
     *
     * @return string The text value in the pretty JSON format.
     */
    private function print_null_value($level)
    {
        return sprintf(self::HTML_FORMAT_FONT_BOLD . 'null'.HTML_FORMAT_CLOSE, $this->style->null_value_color, $level * self::LEFT_PADDING_PX, $level);
    }
    /**
     * Prints a number value with the pretty JSON format.
     *
     * @param string $number The number value.
     * @return string The number value in the pretty JSON format.
     */
    private function print_number_value($number, $level)
    {
        return sprintf(self::HTML_FORMAT_FONT_BOLD . '%s'.HTML_FORMAT_CLOSE, $this->style->number_value_color, $level * self::LEFT_PADDING_PX, $number);
    }
    /**
     * Prints a boolean value with the pretty JSON format.
     *
     * @param string $bool The boolean value.
     * @return string The boolean value in the pretty JSON format.
     */
    private function print_bool_value($bool, $level)
    {
        return sprintf(self::HTML_FORMAT_FONT_BOLD . '%s'.HTML_FORMAT_CLOSE, $this->style->boolean_value_color, $level * self::LEFT_PADDING_PX, $bool ? "true" : "false");
    }

    /**
     * Inserts a new line html tag
     *
     * @return string The new line html tag
     */
    private function new_line()
    {
        return "</br>";
    }
}
?>