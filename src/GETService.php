<?php
include_once "HasamiRestfulService.php";

/**
 * GET Service Class
 * This class defines a restful service with a request verbose GET. 
 * This method is often used to select un protected data from the database. 
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class GETService extends HasamiRestfulService
{
    /**
     * __construct
     *
     * Initialize a new instance of the GET Service class.
     * A default service task is defined as a callback using the function GETService::default_GET_action
     * 
     * @param IHasami $wrapper The web service wrapper
     */
    public function __construct($wrapper)
    {
        $data = $wrapper->get_request_data();
        $data->extra->{TAB_NAME} = $wrapper->get_table_name();
        $data->extra->{TAB_COL_FILTER} = $wrapper->get_default_filter_column_name();
        $urabe = $wrapper->get_urabe();
        parent::__construct($data, $urabe);
        $this->wrapper = $wrapper;
        $this->service_task = function ($data, $urabe) {
            return $this->default_GET_action($data, $urabe);
        };
    }

    /**
     * Defines the default GET action, by default selects all data from the wrapper table name that match the
     * column filter. If the column filter name is not given in the GET variables this function selects
     * all data from the table
     * @param WebServiceContent $data The web service content
     * @param Urabe $urabe The database manager
     * @throws Exception An Exception is thrown if the response can be processed correctly
     * @return UrabeResponse The server response
     */
    protected function default_GET_action($data, $urabe)
    {
        try {
            $table_name = $data->extra->{TAB_NAME};
            $col_name = $data->extra->{TAB_COL_FILTER};
            if ($data->in_GET_variables($col_name)) {
                $sql = $urabe->format_sql_place_holders("SELECT * FROM $table_name WHERE $col_name = @1");
                return $urabe->select($sql, array($col_name));
            } else
                return $urabe->select_all($table_name);
        } catch (Exception $e) {
            throw new Exception("Error Processing Request, " . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Wraps the select function from urabe place holders are passed with @index.
     * Once the SQL selection statement is executed the data is parsed as defined in the given parser. 
     * If the parser is null uses the parser defined in the connector object KanojoX::parser
     *
     * @param string $sql The SQL statement
     * @param array $variables The colon-prefixed bind variables placeholder used in the statement, @1..@n
     * @param MysteriousParser $row_parser The row parser. 
     * @throws Exception An Exception is thrown if not connected to the database or if the SQL is not valid
     * @return UrabeResponse The SQL selection result
     */
    public function select($sql, $variables = null, $row_parser = null)
    {
        return $this->urabe->select($sql, $variables);
    }

}
?>