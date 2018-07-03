<?php 
/**
 * Json Pretty Print Class
 * 
 * This class creates a HTML format from a JSON object
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios 
 */
class JsonPrettyPrint
{
    /**
     * @var string Defines the color used for symbols like "{" or "," in the JSON string.
     */
    public $symbol_color;
    /**
     * @var string Defines the color used for properties names in the JSON string.
     */
    public $property_name_color;
    /**
     * @var string Defines the color used for text values in the JSON string..
     */
    public $text_value_color;
    /**
     * @var string Defines the color used for number values in the JSON string..
     */
    public $number_value_color;
    /**
     * @var string Defines the color used for boolean values in the JSON string.
     */
    public $boolean_value_color;
    /**
     * __construct
     *
     * Initialize a new instance of the JSON pretty print class.
     */
    function __construct()
    {
        $this->symbol_color = "#ffffff";
        $this->boolean_value_color = "#66d5ef";
        $this->property_name_color = "#a6e22e";
        $this->text_value_color = "#e92647";
        $this->number_value_color = "#9481dc";
    }
    /**
     * Gets the pretty print format from a JSON object
     *
     * @param stdClass $json The json object
     * @param int $tab The number of times to append a tabulation
     * @param boolean $parent_is_array True if the parent node is an array
     * @return The json in pretty print format
     */
    public function get_format($json, $tab_times = 0, $parent_is_array = FALSE)
    {
        $html = "";
        $tab_symbol = $this->create_tabulation($tab_times);
        $coma = $this->print_symbol(", ");
        //Inicio JSON
        $html .= $tab_symbol . $this->print_symbol("{");
        $html .= $this->new_line();
        //Contenido del JSON
        foreach ($json as $key => $value) {
            $html .= $tab_symbol . $this->print_property($key) . $this->print_symbol(" : ");
            if (is_array($value)) {
                if (count($value) > 0)
                    $html .= $this->new_line() . $this->print_symbol("[") . $this->new_line();
                else
                    $html .= $this->print_symbol("[");
                $tab_times++;
                foreach ($value as &$arr_value) {
                    if (is_object($arr_value) || is_array($arr_value))
                        $html .= $this->get_format($arr_value, $tab_times, TRUE);
                    else {
                        if (is_object($arr_value))
                            $html .= $this->get_format($arr_value, $tab_times++);
                        else if (is_numeric($arr_value))
                            $html .= $this->print_number_value($arr_value);
                        else if (is_bool($arr_value))
                            $html .= $this->print_bool_value($arr_value);
                        else
                            $html .= $this->print_text_value($arr_value);
                    }
                    $html .= $coma . $this->new_line();
                }
                if (count($value) > 0) {
                    $remove_line = $coma . $this->new_line();
                    $html = substr($html, 0, (strlen($html) - (strlen($remove_line) + 1))) . $this->new_line();
                    $html .= $this->new_line() . $this->print_symbol(" ]");
                }
                else
                    $html .= $this->print_symbol(" ]");
            }
            else if (is_object($value))
                $html .= $this->get_format($value, $tab_times++);
            else if (is_numeric($value))
                $html .= $this->print_number_value($value);
            else if (is_bool($value))
                $html .= $this->print_bool_value($value);
            else
                $html .= $this->print_text_value($value);
            $html .= $coma . $this->new_line();
        }
        //Se remueve la última línea con la coma
        $remove_line = $coma . $this->new_line();
        $html = substr($html, 0, (strlen($html) - (strlen($remove_line) + 1))) . $this->new_line();
        //Fin JSON
        $html .= $this->new_line() . $tab_symbol . $this->print_symbol("}");
        return $html;
    }
    /**
     * Prints a symbol with the pretty JSONJ format.
     *
     * @param string $symbol The symbol to print.
     * @return string The symbol in the pretty JSON format.
     */
    private function print_symbol($symbol)
    {
        return sprintf('<span style="color:%s; font-weight:bold">%s</span>', $this->symbol_color, $symbol);
    }
    /**
     * Prints a text value with the pretty JSONJ format.
     *
     * @param string $text The text value.
     * @return string The text value in the pretty JSON format.
     */
    private function print_text_value($text)
    {
        return sprintf('<span style="color:%s; font-weight:lighter">"%s"</span>', $this->text_value_color, $text);
    }
    /**
     * Prints a number value with the pretty JSONJ format.
     *
     * @param string $number The number value.
     * @return string The number value in the pretty JSON format.
     */
    private function print_number_value($number)
    {
        return sprintf('<span style="color:%s; font-weight:lighter">%s</span>', $this->number_value_color, $number);
    }
    /**
     * Prints a boolean value with the pretty JSONJ format.
     *
     * @param string $bool The boolean value.
     * @return string The boolean value in the pretty JSON format.
     */
    private function print_bool_value($bool)
    {
        return sprintf('<span style="color:%s; font-weight:lighter">%s</span>', $this->boolean_value_color, $bool ? "true" : "false");
    }
    /**
     * Prints a property name with the pretty JSONJ format.
     *
     * @param string $property The property name.
     * @return string The property name in the pretty JSON format.
     */
    private function print_property($property)
    {
        return sprintf('<span style="color:%s; font-weight:lighter">"%s"</span>', $this->property_name_color, $property);
    }
    /**
     * Creates of tabulation.
     *
     * @param  int $times The number of tabulations to append
     * @return string The formatted line
     */
    private function create_tabulation($times)
    {
        $tab = "&nbsp;&nbsp;";
        $str = "";
        for ($i = 0; $i < $times; $i++)
            $str .= $tab;
        return $str;
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