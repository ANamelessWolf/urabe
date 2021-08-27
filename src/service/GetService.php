<?php

namespace Urabe\Service;

use Exception;
use Urabe\Service\WebServiceContent;
use Urabe\Service\HasamiRESTfulService;
use Urabe\Urabe;

/**
 * GET Service Class
 * This class defines a restful service with a request verbose GET. 
 * This method is often used to select un protected data from the database. 
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class GETService extends HasamiRESTfulService
{
    /**
     * @var string The selection filter, a query condition used in the
     * WHERE clause to filter the selection content
     */
    protected $selection_filter;
    /**
     * __construct
     *
     * Initialize a new instance of the GET Service class.
     * A default service task is defined as a callback using the function GETService::default_GET_action
     * 
     * @param WebServiceContent $data The web service content
     * @param Urabe $urabe The database manager
     * @param string $sel_filter The selection filter.
     */
    public function __construct($data, $urabe, $sel_filter)
    {
        $this->selection_filter = $sel_filter;
        parent::__construct($data, $urabe, "default_GET_action");
    }

    /**
     * Wraps the select function from urabe place holders are passed with @index.
     * Once the SQL selection statement is executed the data is parsed as defined in the given parser. 
     * If the parser is null uses the parser defined in the connector object KanojoX::parser
     *
     * @param string $sql The SQL statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement, @1..@n
     * @throws Exception An Exception is thrown if not connected to the database or if the SQL is not valid
     * @return UrabeResponse The SQL selection result
     */
    public function select($sql, $variables = null)
    {
        return $this->urabe->selector->select($sql, $variables);
    }

    /**
     * Defines the default GET action, by default selects all data from the wrapper table name that match the
     * column filter. If the column filter name is not given in the GET variables this function selects
     * all data from the table
     * @param HasamiRESTfulService $service The web service that executes the action
     * @throws Exception An Exception is thrown if the response can be processed correctly
     * @return UrabeResponse The server response
     */
    public function default_GET_action($service)
    {
        try {
            $table_name = $service->get_table()->table_name;
            $filter = $service->selection_filter;
            if (!is_null($filter)) {
                $sql = $service->urabe->format_sql_place_holders("SELECT * FROM $table_name WHERE $filter");
                return $service->urabe->selector->select($sql);
            } else
                return $service->urabe->selector->select_all($table_name);
        } catch (Exception $e) {
            throw new Exception("Error Processing Request, " . $e->getMessage(), $e->getCode());
        }
    }
}
