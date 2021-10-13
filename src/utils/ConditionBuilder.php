<?php

namespace Urabe\Utils;

use Exception;


/**
 * Urabe API Utilities
 * 
 * This class helps to create conditions for
 * selection query in SQL
 * 
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class ConditionBuilder
{
    /**
     * Extract the values from the GET variables, that matches the
     * variable names
     * @param array $variable_names The variable names
     * @param GETVariables $get_vars The get variables
     * @return array The extracted values
     */
    public static function extract_values($variable_names, $get_vars)
    {
        $values = array();
        foreach ($variable_names as &$variable) {
            $value = $get_vars->get($variable);
            if (strlen($value) == 0 or $value == "null")
                $value = null;
            array_push($values, $value);
        }
        return $values;
    }

    /**
     * Creates an equal condition comparing the column name to the equal value
     *
     * @param array $column_names The column names
     * @param array $values The column values
     * @return string The sql condition
     */
    public static function IdEqualCondition($column_names, $values)
    {
        $equalCondition = "";
        for ($i = 0; $i <= 2; $i++) {
            if (intval($values[$i]) > 0) {
                $con = sprintf("%s=%d", $column_names[$i], intval($values[$i]));
                $equalCondition .= strlen($equalCondition) > 0 ? " AND " .  $con : $con;
            }
        }
        return $equalCondition;
    }

    /**
     * Creates a condtion using a range of dates. If just one date is set, 
     * the condition is based from the given date value
     *
     * @param string $column_name The column name
     * @param string $date_from The date from string value
     * @param string $date_to The date to string value
     * @param string $date_format The date format
     * @return string The sql condition
     */
    public static function dateCondition($column_name, $date_format, $from_date = null, $to_date = null)
    {
        $date_condition = "";
        if (!is_null($from_date) and !is_null($to_date))
            $date_condition = sprintf("%s BETWEEN CAST('%s' AS DATE) AND CAST('%s' AS DATE)", $column_name, HasamiUtils::to_date($date_format, $from_date), HasamiUtils::to_date($date_format, $to_date));
        else if (!is_null($from_date))
            $date_condition = sprintf("%s >= '%s'", $column_name, HasamiUtils::to_date($date_format, $from_date));
        else if (!is_null($to_date))
            $date_condition = sprintf("%s >= '%s'", $column_name, HasamiUtils::to_date($date_format, $to_date));
        return $date_condition;
    }

    /**
     * Creates a condition using a like condition. Comparing all column names
     *
     * @param array $column_names The column names
     * @param string $value The like value
     * @return string The sql condition
     */
    public static function likeCondition($column_names, $value)
    {
        $like_condition = "";
        $fields = array();
        foreach ($column_names as &$column_name) {
            $field = sprintf("%s like '%%@query%%'", $column_name);
            array_push($fields, $field);
        }
        if (sizeof($fields) > 0) {
            $like = implode(" OR ", $fields);
            $like_condition = str_replace("@query", $value, $like);
        }
        return $like_condition;
    }
}
