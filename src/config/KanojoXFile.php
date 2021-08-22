<?php

namespace Urabe\Config;
use Urabe\Utils\HasamiUtils;
/**
 * Database connection file
 * 
 * Kanojo means girlfriend in japanese and this class saves the connection data
 * @version 1.0.0
 * @api Makoto Urabe DB Manager database connector
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class KanojoXFile extends KanojoX
{
    /**
     * Creates a Kanojo Connection from a JSON file path
     *
     * @param string $file_path The path to the connection file
     */
    public function __construct($file_path)
    {
        $kanojoObj = HasamiUtils::open_json_file($file_path);
        $this->db_driver = $kanojoObj->db_driver;
        $this->host = $kanojoObj->host;
        $this->port = $kanojoObj->port;
        $this->db_name = $kanojoObj->db_name;
        $this->user_name = $kanojoObj->user_name;
        $this->password = $kanojoObj->password;
    }
}
