<?php

namespace Urabe\Service;

use Urabe\Service\VariableCollection;

/**
 * This class obtains the web service content from GET variables and manage 
 * the access to them
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class GETVariables extends VariableCollection
{
    /**
     * __construct
     *
     * Initialize a new instance of the get variables
     */
    public function __construct()
    {
        $this->vars = array();
        //Get the variables
        foreach ($_GET as $key => $value)
            $this->vars[$key] = $value;
        //Get the keys
        $this->keys = array_keys($this->vars);
    }
    /**
     * Builds a simple condition using the column name to be equals to the  condition variable
     * store value.
     *
     * @param string $column_name The column name
     * @return array One record array with key value pair value, column_name => condition_value
     */
    public function build_simple_condition($column_name)
    {
        $result = array();
        if ($column_name != null && $this->exists($column_name)) {
            $result["$column_name"] = $this->vars[NODE_CONDITION];
            return $result;
        } else
            return null;
    }
}
