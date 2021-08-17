<?php
namespace Urabe\Utils;

use Urabe\Config\UrabeSettings;
use Urabe\Utils\JsonPrettyStyle;
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
    /**
     * @var string PLUS_BUTTON
     * HTML classes to insert a plus glyph icon
     */
    const PLUS_BUTTON = 'glyphicon glyphicon-plus-sign';
    /**
     * @var string GLYPH_BUTTON
     * glyph icon HTML snippet
     */
    const GLYPH_BUTTON = '<a href="#group_%s" class="%s" data-toggle="collapse" style="padding-left:%spx;"></a>';
    /**
     * @var string COLLAPSE_AREA_OPEN
     * Initial area group HTML snippet
     */
    const COLLAPSE_AREA_OPEN = '<div id="group_%s" class="collapse in">';
    /**
     * @var string COLLAPSE_AREA_CLOSE
     * Close area group HTML snippet
     */
    const COLLAPSE_AREA_CLOSE = '</div>';
    /**
     * @var string HTML_FORMAT_FONT_LIGHTER
     * HTML snippet for writing a lighter text
     */
    const HTML_FORMAT_FONT_LIGHTER = '<span style="color:%s; padding-left:%spx; font-weight:lighter">';
    /**
     * @var string HTML_FORMAT_FONT_BOLD
     * HTML snippet for writing a bold text
     */
    const HTML_FORMAT_FONT_BOLD = '<span style="color:%s; padding-left:%spx; font-weight:bold">';
    /**
     * @var string HTML_FORMAT_CLOSE
     * HTML snippet for closing a written text
     */
    const HTML_FORMAT_CLOSE = '</span>';
    /**
     * @var string LEFT_PADDING_PX
     * Defines the TAB padding size
     */
    const LEFT_PADDING_PX = "20";
    /**
     * Undocumented variable
     *
     * @var integer
     */
    private $groupIndex = 0;
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
     * @param JsonPrettyStyle $style The JSON pretty format style
     */
    public function __construct($style = null)
    {
        if (is_null($style))
            $this->style = UrabeSettings::$default_pp_style;
        else
            $this->style = $style;
    }
    /**
     * Gets the pretty print format from a JSON object
     *
     * @param object $json The JSON object
     * @return string The JSON formatted in the pretty print format
     */
    public function get_format($json)
    {
        $html = $this->format_json($json, 0);
        return $html;
    }
    /**
     * Formats a JSON object at a given depth level
     * @param object $json The JSON object
     * @param int $level The JSON level depth
     * @return string The JSON formatted in the pretty print format
     */
    public function format_json($json, $level)
    {
        $html = "";
        if (is_object($json))
            $html .= $this->format_object($json, $level);
        else if (is_array($json))
            $html .= $this->format_array($json, $level);
        else
            $html .= $this->format_value($json, 0);
        return $html;
    }
    /**
     * Formats a JSON object at a given depth level and desired tab offset
     * @param object $json The JSON object
     * @param int $level The JSON level depth
     * @param offset $offset The JSON tab offset
     * @return string The JSON formatted in the pretty print format
     */
    public function format_object($json, $level, $offset = 0)
    {
        $html = "";
        $html .= $this->new_line($html);
        $html = $this->open_group("{", $offset);
        $properties = array_keys(get_object_vars($json));
        for ($i = 0; $i < count($properties); $i++) {
            $html .= $this->new_line($html);
            $html .= $this->print_property($properties[$i], $level + 1);
            $html .= $this->print_symbol(" : ", 0);
            $html .= $this->format_json($json->{$properties[$i]}, $level + 1);
            $html = $this->append_comma($i, count($properties), $html);
        }
        $html .= $this->new_line($html);
        $html .= $this->print_symbol("}", $level);
        $html .= $this->close_group();
        return $html;
    }
    /**
     * Formats a JSON array at a given depth level and desired tab offset
     * @param array $array The JSON array
     * @param int $level The JSON level depth
     * @param offset $offset The JSON tab offset
     * @return string The JSON formatted in the pretty print format
     */
    public function format_array($array, $level, $offset = 0)
    {
        $html = "";
        if (count($array) == 0)
            $html .= $this->print_symbol(" [ ] ", 0);
        else {
            $keys = array_keys($array);
            $is_array_of_objects = is_string($keys[0]);
            $symbol = ($is_array_of_objects ? "{" : "[");
            $html = $this->open_group(" $symbol", $offset);
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
                $html = $this->append_comma($i, count($keys), $html);
            }
            $html .= $this->new_line($html);
            $symbol = ($is_array_of_objects ? "}" : "]");
            $html .= $this->print_symbol("$symbol", $level);
            $html .= $this->close_group();
        }
        return $html;
    }
    /**
     * Formats a JSON value at a given depth level
     * @param mixed $value The JSON value
     * @param int $level The JSON level depth
     * @return string The JSON formatted in the pretty print format
     */
    public function format_value($value, $level)
    {
        $html = "";
        if (is_string($value)) {
            if (strtolower($value) == "true" || strtolower($value) == "false")
                $html .= $this->print_bool_value($value == "true", $level);
            else
                $html .= $this->print_text_value($value, $level);
        } else if (is_numeric($value))
            $html .= $this->print_number_value($value, $level);
        else if (is_null($value))
            $html .= $this->print_null_value($level);
        else if (is_bool($value))
            $html .= $this->print_bool_value($value == "true", $level);
        else
            $html .= $this->format_array($value, $level, $level);
        return $html;
    }
    /*************************************
     * Values are formatted with span tag*
     *************************************/
    /**
     * Opens a JSON group that can be collapsed via clicking the a glyph icon
     *
     * @param string $symbol The symbol that opens the group can be a "{" or a "["
     * @param int $level The JSON level depth
     * @return string The html snippet
     */
    private function open_group($symbol, $level)
    {
        $html = "";
        $html .= sprintf(self::GLYPH_BUTTON, ++$this->groupIndex, self::PLUS_BUTTON, $level * self::LEFT_PADDING_PX);
        $html .= sprintf(self::HTML_FORMAT_FONT_BOLD . '%s' . self::HTML_FORMAT_CLOSE, $this->style->symbol_color, 0, " " . $symbol);
        $html .= sprintf(self::COLLAPSE_AREA_OPEN, $this->groupIndex);
        return $html;
    }
    /**
     * Returns the HTML close group tag
     *
     * @return string The html snippet
     */
    private function close_group()
    {
        return self::COLLAPSE_AREA_CLOSE;
    }
    /**
     * Prints a symbol with the pretty JSON format.
     *
     * @param string $symbol The symbol to print
     * @param int $level The JSON level depth
     * @return string The html snippet
     */
    private function print_symbol($symbol, $level)
    {
        return sprintf(self::HTML_FORMAT_FONT_BOLD . '%s' . self::HTML_FORMAT_CLOSE, $this->style->symbol_color, $level * self::LEFT_PADDING_PX, $symbol);
    }
    /**
     * Prints a property name with the pretty JSON format.
     *
     * @param string $property The property name
     * @param int $level The JSON level depth
     * @return string The html snippet
     */
    private function print_property($property, $level)
    {
        return sprintf(self::HTML_FORMAT_FONT_LIGHTER . '"%s"' . self::HTML_FORMAT_CLOSE, $this->style->property_name_color, $level * self::LEFT_PADDING_PX, $property);
    }
    /**
     * Prints a text value with the pretty JSON format.
     *
     * @param string $text The text value
     * @param int $level The JSON level depth
     * @return string The html snippet
     */
    private function print_text_value($text, $level)
    {
        return sprintf(self::HTML_FORMAT_FONT_LIGHTER . '"%s"' . self::HTML_FORMAT_CLOSE, $this->style->text_value_color, $level * self::LEFT_PADDING_PX, $text);
    }
    /**
     * Prints a null value with the pretty JSON format.
     *
     * @param int $level The JSON level depth
     * @return string The html snippet
     */
    private function print_null_value($level)
    {
        return sprintf(self::HTML_FORMAT_FONT_BOLD . 'null' . self::HTML_FORMAT_CLOSE, $this->style->null_value_color, $level * self::LEFT_PADDING_PX, $level);
    }
    /**
     * Prints a number value with the pretty JSON format.
     *
     * @param string $number The number value.
     * @param int $level The JSON level depth
     * @return string The html snippet
     */
    private function print_number_value($number, $level)
    {
        return sprintf(self::HTML_FORMAT_FONT_BOLD . '%s' . self::HTML_FORMAT_CLOSE, $this->style->number_value_color, $level * self::LEFT_PADDING_PX, $number);
    }
    /**
     * Prints a boolean value with the pretty JSON format.
     *
     * @param string $bool The boolean value.
     * @param int $level The JSON level depth
     * @return string The html snippet
     */
    private function print_bool_value($bool, $level)
    {
        return sprintf(self::HTML_FORMAT_FONT_BOLD . '%s' . self::HTML_FORMAT_CLOSE, $this->style->boolean_value_color, $level * self::LEFT_PADDING_PX, $bool ? "true" : "false");
    }

    /**
     * Inserts a new line in HTML tag if the previous item is a collapse are TAG
     *
     * @param string $html The html code
     * @return string The html snippet
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
    /**
     * Appends a comma at the end of a given element
     *
     * @param int $index The element index
     * @param int $elements_count The total number of elements
     * @param string $html The html code
     * @return string The html code with an appended comma
     */
    private function append_comma($index, $elements_count, $html)
    {
        if ($index < ($elements_count - 1)) {
            $closeDivSize = strlen(self::COLLAPSE_AREA_CLOSE);
            $last_tag_was_div = strlen($html) > $closeDivSize && substr($html, strlen($html) - $closeDivSize) == self::COLLAPSE_AREA_CLOSE;
            if ($last_tag_was_div) {
                $html = substr($html, 0, strlen($html) - strlen(self::COLLAPSE_AREA_CLOSE));
                $html .= $this->print_symbol(",", 0);
                $html .= $this->close_group();
            } else
                $html .= $this->print_symbol(", ", 0);
        }
        return $html;
    }
}
