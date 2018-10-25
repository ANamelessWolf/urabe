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
    const PLUS_BUTTON = 'glyphicon glyphicon-plus-sign';
    const GLYPH_BUTTON = '<a href="#group_%s" class="%s" data-toggle="collapse" style="padding-left:%spx;"></a>';
    const COLLAPSE_AREA_OPEN = '<div id="group_%s" class="collapse in">';
    const COLLAPSE_AREA_CLOSE = '</div>';
    const HTML_FORMAT_FONT_LIGHTER = '<span style="color:%s; padding-left:%spx; font-weight:lighter">';
    const HTML_FORMAT_FONT_BOLD = '<span style="color:%s; padding-left:%spx; font-weight:bold">';
    const HTML_FORMAT_CLOSE = '</span>';
    const LEFT_PADDING_PX = "20";

    public $groupIndex = 0;
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
        $html .= $this->new_line($html);
        $html = $this->open_group("{", $offset);
        $properties = array_keys(get_object_vars($json));
        for ($i = 0; $i < count($properties); $i++) {
            $html .= $this->new_line($html);
            $html .= $this->print_property($properties[$i], $level + 1);
            $html .= $this->print_symbol(" : ", 0);
            $html .= $this->format_json($json->{$properties[$i]}, $level + 1);
            if ($i < (count($properties) - 1)) {
                $last_tag_was_div = strlen($html) > 6 && substr($html, strlen($html) - 6) == "</div>";
                if ($last_tag_was_div) {
                    $html = substr($html, 0, strlen($html) - strlen(self::COLLAPSE_AREA_CLOSE));
                    $html .= $this->print_symbol(",", 0);
                    $html .= $this->close_group();
                } else
                    $html .= $this->print_symbol(", ", 0);
            }
        }
        $html .= $this->new_line($html);
        $html .= $this->print_symbol("}", $level);
        $html .= $this->close_group();
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
            $html = $this->open_group(" $symbol", $offset);
            //$html = $this->print_symbol(" $symbol", $offset);

            for ($i = 0; $i < count($array); $i++) {
                if (is_string($keys[$i])) {
                    $html .= $this->new_line($html);
                    $html .= $this->print_property($keys[$i], $level + 1);
                    $html .= $this->print_symbol(" : ", 0);
                    $html .= $this->format_json($array[$keys[$i]], $level + 1);

                } else {
                    $html .= $this->new_line($html);
                    $html .= $this->format_value($array[$keys[$i]], $level + 1);
                }
                if ($i < (count($array) - 1)) {
                    $last_tag_was_div = strlen($html) > 6 && substr($html, strlen($html) - 6) == "</div>";
                    if ($last_tag_was_div) {
                        $html = substr($html, 0, strlen($html) - strlen(self::COLLAPSE_AREA_CLOSE));
                        $html .= $this->print_symbol(",", 0);
                        $html .= $this->close_group();
                    } else
                        $html .= $this->print_symbol(", ", 0);
                }
            }
            $html .= $this->new_line($html);
            $symbol = ($is_array_of_objects ? "}" : "]");
            $html .= $this->print_symbol("$symbol", $level);
            $html .= $this->close_group();
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
        $html = $this->format_json($json, 0);
        // $size = strlen($this->print_symbol("},")) + strlen($this->close_group()) ;
        // $html = substr($html, 0, strlen($html) - $size);
        // $html .= $this->print_symbol("}");
        // $html .= $this->close_group();
        return $html;
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
        return sprintf(self::HTML_FORMAT_FONT_BOLD . '%s' . self::HTML_FORMAT_CLOSE, $this->style->symbol_color, $level * self::LEFT_PADDING_PX, $symbol);
    }
    private function open_group($symbol, $level)
    {
        $html .= sprintf(self::GLYPH_BUTTON, ++$this->groupIndex, self::PLUS_BUTTON, $level * self::LEFT_PADDING_PX);
        $html .= sprintf(self::HTML_FORMAT_FONT_BOLD . '%s' . self::HTML_FORMAT_CLOSE, $this->style->symbol_color, 0, " " . $symbol);
        $html .= sprintf(self::COLLAPSE_AREA_OPEN, $this->groupIndex);
        return $html;
    }
    private function close_group()
    {
        return self::COLLAPSE_AREA_CLOSE;
    }
    /**
     * Prints a property name with the pretty JSON format.
     *
     * @param string $property The property name.
     * @return string The property name in the pretty JSON format.
     */
    private function print_property($property, $level)
    {
        return sprintf(self::HTML_FORMAT_FONT_LIGHTER . '"%s"' . self::HTML_FORMAT_CLOSE, $this->style->property_name_color, $level * self::LEFT_PADDING_PX, $property);
    }
    /**
     * Prints a text value with the pretty JSON format.
     *
     * @param string $text The text value.
     * @return string The text value in the pretty JSON format.
     */
    private function print_text_value($text, $level)
    {
        return sprintf(self::HTML_FORMAT_FONT_LIGHTER . '"%s"' . self::HTML_FORMAT_CLOSE, $this->style->text_value_color, $level * self::LEFT_PADDING_PX, $text);
    }
    /**
     * Prints a null value with the pretty JSON format.
     *
     * @return string The text value in the pretty JSON format.
     */
    private function print_null_value($level)
    {
        return sprintf(self::HTML_FORMAT_FONT_BOLD . 'null' . self::HTML_FORMAT_CLOSE, $this->style->null_value_color, $level * self::LEFT_PADDING_PX, $level);
    }
    /**
     * Prints a number value with the pretty JSON format.
     *
     * @param string $number The number value.
     * @return string The number value in the pretty JSON format.
     */
    private function print_number_value($number, $level)
    {
        return sprintf(self::HTML_FORMAT_FONT_BOLD . '%s' . self::HTML_FORMAT_CLOSE, $this->style->number_value_color, $level * self::LEFT_PADDING_PX, $number);
    }
    /**
     * Prints a boolean value with the pretty JSON format.
     *
     * @param string $bool The boolean value.
     * @return string The boolean value in the pretty JSON format.
     */
    private function print_bool_value($bool, $level)
    {
        return sprintf(self::HTML_FORMAT_FONT_BOLD . '%s' . self::HTML_FORMAT_CLOSE, $this->style->boolean_value_color, $level * self::LEFT_PADDING_PX, $bool ? "true" : "false");
    }

    /**
     * Inserts a new line html tag
     *
     * @return string The new line html tag
     */
    private function new_line($html)
    {
        $closeDivSize = strlen(self::COLLAPSE_AREA_CLOSE);
        $openDivSize = strlen(sprintf(self::COLLAPSE_AREA_OPEN, $this->groupIndex));
        $htmlLen = strlen($html);
        $last_tag_was_div_open = strlen($html) > $openDivSize && substr($html, strlen($html) - $openDivSize) == sprintf(self::COLLAPSE_AREA_OPEN, $this->groupIndex);
        //No spaces after div open or close tags
        $last_tag_was_div_close = strlen($html) > $closeDivSize && substr($html, strlen($html) - $closeDivSize) == self::COLLAPSE_AREA_CLOSE;
        if (!$last_tag_was_div_open && !$last_tag_was_div_close)
            return "<br>";
        else
            return "";
    }
}
?>