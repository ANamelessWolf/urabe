<?php 
namespace Urabe\Utils;
/**
 * Json Pretty Print Style
 * 
 * This class represent the color style used by the JSON Pretty Print
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios 
 */
class JsonPrettyStyle
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
     * @var string Defines the color used for null value
     */
    public $null_value_color;
    /**
     * @var string Defines the color used for boolean values in the JSON string.
     */
    public $boolean_value_color;

    /**
     * Defines the style used in white backgrounds
     *
     * @return JsonPrettyStyle The JSON style
     */
    public static function LightStyle()
    {
        $style = new JsonPrettyStyle();
        $style->symbol_color = "#808388";
        $style->boolean_value_color = "#00a2e8";
        $style->property_name_color = "#000";
        $style->text_value_color = "#008000";
        $style->number_value_color = "#e23400";
        $style->null_value_color ="#730202";
        return $style;
    }

    /**
     * Defines the style used in black backgrounds
     * This is the default PP Style
     *
     * @return JsonPrettyStyle The JSON style
     */
    public static function DarkStyle()
    {
        $style = new JsonPrettyStyle();
        $style->symbol_color = "#ffffff";
        $style->boolean_value_color = "#66d5ef";
        $style->property_name_color = "#a6e22e";
        $style->text_value_color = "#FAB02F";
        $style->number_value_color = "#9481dc";
        $style->null_value_color ="#e92647";
        return $style;
    }
}
?>