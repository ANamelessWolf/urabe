<?php

namespace Urabe\Service;

use Urabe\Service\HasamiRESTfulService;
use Urabe\Config\ServiceStatus;

/**
 * Hasami Web Service Class
 * This class saves the web service collection
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class ServiceCollection
{
    /**
     * @var array The Restful services
     */
    protected $services;
    /**
     * @var array The Restful services accesibility status by default all service status
     * are available
     */
    protected $services_status;
    /**
     * @var array The list of supported verbose services
     */
    protected $verbose_list;
    /**
     * Initialize a new instance of the service collection
     */
    public function __construct()
    {
        $this->clear();
    }
    /**
     * Clears the service collection
     */
    public function clear()
    {
        $this->services = array();
        $this->services_status = array();
        $this->verbose_list = array();
    }
    /**
     * This function gets the service value by its verbose
     * In case the service does not exists this functions returns null
     * @param string $verbose The verbose "PUT" "POST" "GET" "DELETE" 
     * @return HasamiRESTfulService The web service manager
     */
    public function get($verbose)
    {
        if ($this->exists($verbose))
            return $this->services[$verbose];
        else
            return null;
    }
    /**
     * This function checks the service accessibility by its verbose
     * In case the service does not exists this functions returns BLOCKED status
     * @param string $verbose The verbose "PUT" "POST" "GET" "DELETE" 
     * @return int The web service manager
     */
    public function check_accessibility($verbose)
    {
        if ($this->exists($verbose))
            return $this->services_status[$verbose];
        else
            return ServiceStatus::BLOCKED;
    }
    /**
     * This function check if the given verbose is defined in the current
     * web service
     * 
     * @param string $verbose The verbose "PUT" "POST" "GET" "DELETE" 
     * @return bool Returns true if the verbose is defined
     */
    public function exists($verbose)
    {
        return in_array($verbose, $this->verbose_list);
    }
    /**
     * This function set a web service to a given verbose
     * 
     * @param string $verbose The verbose "PUT" "POST" "GET" "DELETE" 
     * @param int $accessibility The web service accessibility, by default available
     * @return bool Returns true if the verbose is defined
     */
    public function set($verbose, $services, $accessibility = ServiceStatus::AVAILABLE)
    {
        $this->services[$verbose] = $services;
        $this->services_status[$verbose] = $accessibility;
        array_push($this->verbose_list, $verbose);
    }
    /**
     * This function set a web service accessibility
     * 
     * @param string $verbose The verbose "PUT" "POST" "GET" "DELETE" 
     * @param int $accessibility The web service accessibility, by default available
     * @return bool Returns true if the verbose is defined
     */
    public function set_status($verbose, $accessibility = ServiceStatus::AVAILABLE)
    {
        $this->services_status[$verbose] = $accessibility;
    }
}
