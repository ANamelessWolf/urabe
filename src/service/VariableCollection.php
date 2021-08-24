<?php

namespace Urabe\Service;

use Exception;

/**
 * This class manage the use of a variables with key value pair
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class VariableCollection
{
    /**
     * @var array The variables collection
     */
    protected $vars;
    /**
     * @var array The variable names as key array
     */
    public $keys;
    /**
     * This function check if the given variable name is defined in the current
     * variable collection
     * 
     * @param string $var_name The variable name
     * @return bool Returns true when the variable is defined
     */
    public function exists($var_name)
    {
        return in_array($var_name, $this->keys);
    }
    /**
     * This function gets the variable value by its name
     * In case the variable does not exists this functions returns null
     * @param string $var_name The variable name
     * @return mixed The variable value
     */
    public function get($var_name)
    {
        if ($this->exists($var_name))
            return $this->vars[$var_name];
        else
            return null;
    }
    /**
     * This function compares if the variable value is equals to the given value
     * @param string $var_name The variable name
     * @param string $var_value The given value to compare
     * @return bool Returns true when the variable is equals to the given value
     */
    public function compare($var_name, $var_value)
    {
        return $this->exists($var_name) && $this->vars[$var_name] == $var_value;
    }
    /**
     * This function picks a set of variables values by its names. 
     * If the variable is not defined this method throws an Exception
     * 
     * @param array $var_names The variables name to pick its values
     * @return array The picked values
     */
    public function pick_values(...$var_names)
    {
        $values = array();
        foreach ($var_names as $var_name) {
            if ($this->exists($var_name))
                array_push($values, $this->vars[$var_name]);
            else
                throw new Exception(sprintf(ERR_INCOMPLETE_DATA, CAP_GET_VARS, "'" . implode("', '", $var_names) . "'"));
        }
        return $values;
    }

    /**
     * Validates if the passed variables names contains all the obligatory names.
     * As the names are considered obligatory, if one not found an
     * exception is thrown.
     * @param array $var_names The variables names
     * @throws Exception Throws an Exception if any of the variables are missing
     * @return boolean True if all variables are defined
     */
    public function validate_obligatory(...$var_names)
    {
        $vCount = 0;
        foreach ($var_names as $var_name)
            $vCount += $this->exists($var_name) ? 1 : 0;
        if (count($var_names) == $vCount)
            return true;
        else {
            $names = implode("', '", $var_names);
            throw new Exception(sprintf(ERR_INCOMPLETE_DATA, CAP_GET_VARS, "'" . $names . "'"));
        }
    }
}
