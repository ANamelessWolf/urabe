<?php
include_once "HasamiRESTfulService.php";

/**
 * PUT Service Class
 * This class defines a restful service with a request verbose PUT. 
 * This method is often used to insert data to the database. 
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class PUTService extends HasamiRESTfulService
{
    /**
     * __construct
     *
     * Initialize a new instance of the PUT Service class.
     * A default service task is defined as a callback using the function PUTService::default_PUT_action
     * 
     * @param IHasami $wrapper The web service wrapper
     */
    public function __construct($wrapper)
    {
        $data = $wrapper->get_request_data();
        $data->extra->{TAB_NAME} = $wrapper->get_table_name();
        $data->extra->{CAP_INSERT} = $wrapper->get_insert_columns();
        $urabe = $wrapper->get_urabe();
        parent::__construct($data, $urabe);
        $this->wrapper = $wrapper;
        $this->service_task = function ($data, $urabe) {
            return $this->default_PUT_action($data, $urabe);
        };
    }
    /**
     * Wraps the insert function from urabe
     * @param string $table_name The table name.
     * @param object $values The values to insert as key value pair array. 
     * Column names as keys and insert values as associated value, place holders can not be identifiers only values.
     * @throws Exception An Exception is raised if the connection is null or executing a bad query
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    public function insert($table_name, $values)
    {
        return $this->urabe->insert($table_name, $values);
    }
    /**
     * Wraps the insert_bulk function from urabe
     *
     * @param string $table_name The table name.
     * @param array $values The values to insert as key value pair array. 
     * Column names as keys and insert values as associated value, place holders can not be identifiers only values.
     * @throws Exception An Exception is raised if the connection is null or executing a bad query
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    public function insert_bulk($table_name, $columns, $values)
    {
        return $this->urabe->insert_bulk($table_name, $columns, $values);
    }
    /**
     * Defines the default PUT action, by default execute an insertion query with the given data passed
     * in the body properties insert_values
     * @param WebServiceContent $data The web service content
     * @param Urabe $urabe The database manager
     * @throws Exception An Exception is thrown if the response can be processed correctly
     * @return UrabeResponse The server response
     */
    protected function default_PUT_action($data, $urabe)
    {
        return $this->default_action($data, $urabe);
    }
    /**
     * Defines the default PUT action, by default execute an insertion query with the given data passed
     * in the body properties insert_values
     * @param WebServiceContent $data The web service content
     * @param Urabe $urabe The database manager
     * @throws Exception An Exception is thrown if the response can be processed correctly
     * @return UrabeResponse The server response
     */
    public function default_action($data, $urabe)
    {
        try {
            $table_name = $data->extra->{TAB_NAME};
            $insert = $data->extra->{CAP_INSERT};
            //Validate column data
            $this->validate_columns('insert_values', $insert);
            //Validate values
            if (!property_exists($this->data->body->insert_values, NODE_VAL))
                throw new Exception(sprintf(ERR_INCOMPLETE_DATA, 'insert_values', NODE_VAL));
            //Formats values with table definition
            $values = $this->wrapper->format_values($this->data->body->insert_values->values);
            $columns = $this->data->body->insert_values->columns;
            //Build insert query
            if (is_array($values))
                $response = $this->urabe->insert_bulk($table_name, $columns, $values);
            else
                $response = $this->urabe->insert($table_name, $values);
            return $response;
        } catch (Exception $e) {
            throw new Exception("Error Processing Request, " . $e->getMessage(), $e->getCode());
        }
    }
}
