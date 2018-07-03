<?php
/**
 * Costomizable Service Interface
 * Allow to modify the default webservice action from a Hasami Wrappert service
 * @version 1.0.0
 * @api Makoto Urabe Oracle
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
interface ICustomizableService
{
    /**
     * Defines the task that is called when a GET verbose is
     * called
     *
     * @param HasamiRESTfulService $service The Restfull service
     * @return QueryResult|string The server response
     */
    public function GETServiceTask($service);
}
?>