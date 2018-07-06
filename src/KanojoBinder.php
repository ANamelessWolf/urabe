<?php

/**
 * Binds a PHP variable to an Database placeholder
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class KanojoBinder
{
    /**
     * @var string $bv_name
     * The colon-prefixed bind variable placeholder used in the statement. The colon is optional in bv_name. 
     * Oracle does not use question marks for placeholders.
     *
     */
    public $bv_name;
    /**
     * @var string $variable
     * The PHP variable to be associated with bv_name
     */
    public $variable;
}
?>