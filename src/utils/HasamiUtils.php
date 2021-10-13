<?php

namespace Urabe\Utils;

use Exception;
use Urabe\Utils\JsonPrettyStyle;

/**
 * Urabe API Utilities
 * 
 * This class encapsulates stylish and service function utilities
 * 
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class HasamiUtils
{
    /******************
     * Misc functions *
     *******************/
    /**
     * Creates a pretty json print from a JSON object, defining a pretty
     * print format. 
     *
     * @param stdClass $data The JSON data to format
     * @param JsonPrettyStyle $style The JSON pretty format
     * @return string The response encoded as a pretty HTML
     */
    public static function pretty_print_format($data, $style)
    {
        $jsonFormatter = new PrettyPrintFormatter($style);
        $jsonFormatter->append_response($data);
        $jsonFormatter->close();
        return $jsonFormatter->html;
    }
    /**
     * Converts a value to MySQL date format
     *
     * @param string $date_format The date format
     * @param string $value The value to parse
     * @return string The date value formatted
     */
    public static function to_date($date_format, $value)
    {
        try {
            $dateValue = date_parse($value);
            $value = sprintf($date_format, $dateValue["year"], $dateValue["month"], $dateValue["day"]);
            return $value;
        } catch (Exception $e) {
            throw new Exception(ERR_BAD_DATE_FORMAT);
        }
    }
    /*************************************
     ************ File utils *************
     *************************************/
    /**
     * Creates a JSON object from a JSON file
     *
     * @param string $file_path The JSON file path
     * @throws Exception An Exception is thrown if theres an error reading the file
     * @return object The JSON Object
     */
    public static function open_json_file($file_path)
    {
        if (file_exists($file_path)) {
            $file_string = file_get_contents($file_path);
            //Remove escaping characters
            $file_string = preg_replace('!/\*.*?\*/!s', '', $file_string);
            $file_string = preg_replace('/(\/\/).*/', '', $file_string);
            $file_string = preg_replace('/\n\s*\n/', "\n", $file_string);
            //Encode as UTF8
            $file_string = utf8_encode($file_string);
            $json_object = json_decode($file_string);
            if (is_null($json_object))
                throw new Exception(sprintf(ERR_READING_JSON_FILE, $file_path));
            else
                return $json_object;
        } else
            throw new Exception(sprintf(ERR_READING_JSON_FILE, $file_path));
    }
}
