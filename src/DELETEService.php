<?php
include_once "HasamiRestfulService.php";

/**
 * DELETE Service Class
 * This class defines a restful service with a request verbose DELETE. 
 * This method is often used to delete or access protected data from the database. 
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class DELETEService extends HasamiRestfulService
{
    /**
     * @var string The delete condition
     */
    public $delete_condition;
    /**
     * __construct
     *
     * Initialize a new instance of the DELETE Service class.
     * A default service task is defined as a callback using the function DELETEService::default_DELETE_action
     * 
     * @param IHasami $wrapper The web service wrapper
     * @param string $delete_condition The delete condition
     */
    public function __construct($wrapper, $delete_condition = null)
    {
        $data = $wrapper->get_request_data();
        $data->extra->{TAB_NAME} = $wrapper->get_table_name();
        $this->extra->{CAP_DELETE} = $delete_condition;
        $urabe = $wrapper->get_urabe();
        parent::__construct($data, $urabe);
        $this->wrapper = $wrapper;
        $this->service_task = function ($data, $urabe) {
            return $this->default_DELETE_action($data, $urabe);
        };
    }
    /**
     * Wraps the delete function from urabe
     * @param string $table_name The table name.
     * @param string $condition The condition to match
     * @throws Exception An Exception is raised if the connection is null or executing a bad query
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    public function delete($table_name, $condition)
    {
        return $this->urabe->delete($table_name, $condition);
    }
    /** 
     * Wraps the delete_by_field function from urabe
     *
     * @param string $table_name The table name.
     * @param string $column_name The column name used in the condition.
     * @param string $column_value The column value used in the condition.
     * @throws Exception An Exception is raised if the connection is null or executing a bad query
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    public function delete_by_field($table_name, $column_name, $column_value)
    {
        return $this->urabe->delete_by_field($table_name, $column_name, $column_value);
    }
    /**
     * Defines the default DELETE action, by default deletes all values that match a condition
     * A condition is needed to delete values.
     * @param WebServiceContent $data The web service content
     * @param Urabe $urabe The database manager
     * @throws Exception An Exception is thrown if the response can be processed correctly
     * @return UrabeResponse The server response
     */
    protected function default_DELETE_action($data, $urabe)
    {
        try {
            $table_name = $data->extra->{TAB_NAME};
            //Validate body
            $condition = property_exists($this->data->body, "condition") ? $this->data->body->delete_condition : $data->extra->{CAP_DELETE};
            if ($condition == null)
                throw new Exception(sprintf(ERR_INCOMPLETE_DATA, $property_name, NODE_CONDITION));
            //Build delete query
            $response = $this->urabe->delete($table_name, $this->data->body->delete_condition);
            return $response;
        } catch (Exception $e) {
            throw new Exception("Error Processing Request, " . $e->getMessage(), $e->getCode());
        }
    }
}
?>