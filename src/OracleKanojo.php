<?php 
/**
 * A Database data struct 
 * 
 * Kanojo means girlfriend in japanase and this class saves the connection data structure used to connect to
 * an Oracle database.
 * @version 1.0.0
 * @api Makoto Urabe Oracle
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class OracleKanojoX extends KanojoX
{
    protected function init_connection(){
        return  oci_connect($this->user_name, $this->password, $conn_string, self::DEFAULT_CHAR_SET);
    }
}
?>