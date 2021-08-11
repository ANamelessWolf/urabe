<?php
namespace Urabe\Runtime;
use Exception;
use Urabe\Config\ConnectionError;
/**
 * This class represents a SQL exception
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class MysteriousParsingException extends Exception
{
    /**
     * @var string $sql The SQL statement text. If there was no statement, this is an empty string. 
     */
    public $sql;
    /**
     * Initialize a new MysteriousParsingException
     *
     * @param ConnectionError $error The connection error
     */
    public function __construct($error)
    {
        $search = array("\t", "\n", "\r", "   ", "  ", "\\n");
        $replace = array("", " ", " ", "", "", " ");
        $msg = str_replace($search, $replace, $error->message);        
        $msg = sprintf(ERR_BAD_QUERY, $msg);
        parent::__construct($msg, $error->code);
        $this->sql = $error->sql;
    }
}
?>