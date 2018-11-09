<?php
include_once "HasamiRestfulService.php";

/**
 * POST Service Class
 * This class defines a restful service with a request verbose POST. 
 * This method is often used to update or access protected data from the database. 
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class POSTService extends HasamiRestfulService
{
    /**
     * @var string The update condition
     */
    public $update_condition;
    /**
     * __construct
     *
     * Initialize a new instance of the POST Service class.
     * A default service task is defined as a callback using the function POSTService::default_POST_action
     * 
     * @param IHasami $wrapper The web service wrapper
     * @param string $update_condition The delete condition
     */
    public function __construct($wrapper, $update_condition = null)
    {
        $data = $wrapper->get_request_data();
        $data->extra->{TAB_NAME} = $wrapper->get_table_name();
        $data->extra->{CAP_UPDATE} = is_null($update_condition) ? "" : $update_condition;
        $urabe = $wrapper->get_urabe();
        parent::__construct($data, $urabe);
        $this->wrapper = $wrapper;
        $this->service_task = function ($data, $urabe) {
            return $this->default_POST_action($data, $urabe);
        };
    }
    /**
     * Wraps the update function from urabe
     * @param string $table_name The table name.
     * @param object $values The values to update as column key value paired
     * Column names as keys and updates values as associated value, place holders can not be identifiers only values.
     * @param string $condition The condition to match
     * @throws Exception An Exception is raised if the connection is null or executing a bad query
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    public function update($table_name, $values, $condition)
    {
        return $this->urabe->update($table_name, $values, $condition);
    }
    /** 
     * Wraps the update_by_field function from urabe
     *
     * @param string $table_name The table name.
     * @param array $values The values to update as key value pair array. 
     * Column names as keys and update values as associated value, place holders can not be identifiers only values.
     * @param string $column_name The column name used in the condition.
     * @param string $column_value The column value used in the condition.
     * @throws Exception An Exception is raised if the connection is null or executing a bad query
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    public function update_by_field($table_name, $values, $column_name, $column_value)
    {
        return $this->urabe->update_by_field($table_name, $values, $column_name, $column_value);
    }
    /**
     * Defines the default POST action, by default updates the given values.
     * A condition is needed to update values.
     * @param WebServiceContent $data The web service content
     * @param Urabe $urabe The database manager
     * @throws Exception An Exception is thrown if the response can be processed correctly
     * @return UrabeResponse The server response
     */
    protected function default_POST_action($data, $urabe)
    {
        try {
            $table_name = $data->extra->{TAB_NAME};
            //Validate body
            $this->validate_body('update_values');
            //Validate values and given condition
            if (!property_exists($this->data->body->update_values, NODE_VAL))
                throw new Exception(sprintf(ERR_INCOMPLETE_DATA, $property_name, NODE_VAL . ", " . NODE_CONDITION));
            else
                $condition = property_exists($this->data->body->insert_values, NODE_CONDITION) ? $this->data->body->insert_values->condition : $data->extra->{CAP_UPDATE};
            if ($condition == null)
                throw new Exception(sprintf(ERR_INCOMPLETE_DATA, $property_name, NODE_CONDITION));
            //Build update query
            $response = $this->urabe->update($table_name, $this->data->body->update_values->values, $condition);
            return $response;
        } catch (Exception $e) {
            throw new Exception("Error Processing Request, " . $e->getMessage(), $e->getCode());
        }
    }
}
?>