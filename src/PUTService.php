<?php
include_once "HasamiRestfulService.php";

/**
 * PUT Service Class
 * This class defines a restful service with a request verbose PUT. 
 * This method is often used to insert data to the database. 
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class PUTService extends HasamiRestfulService
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
        $data->extra->{TAB_INS_COLS} = $wrapper->get_insert_columns();
        $urabe = $wrapper->get_urabe();
        parent::__construct($data, $urabe);
        $this->wrapper = $wrapper;
        $this->service_task = function ($data, $urabe) {
            return $this->default_PUT_action($data, $urabe);
        };
    }
    /**
     * Defines the default PUT action, by default selects all data from the wrapper table name that match the
     * column filter. Insert values are sent in the body as defined in the insert JSON documentation
     * @param WebServiceContent $data The web service content
     * @param Urabe $urabe The database manager
     * @throws Exception An Exception is thrown if the response can be processed correctly
     * @return UrabeResponse The server response
     */
    protected function default_PUT_action($data, $urabe)
    {
        try {
            $table_name = $data->extra->{TAB_NAME};
            $ins_columns = $data->extra->{TAB_INS_COLS};
            //Validate column data
            $this->validate_columns($data->body, 'insert');
            if (property_exists($data->body->insert->values) && is_array($data->body->insert->values)) {
                $columns = $data->body->insert->columns;
                $values = $data->body->insert->values;
                if (count($values) == 1)
                    $urabe->insert($data->body->insert);
            } else
                throw new Exception(sprintf(ERR_INCOMPLETE_DATA, 'insert', 'values'));

            if ($data->in_GET_variables($col_name)) {
                $sql = $urabe->format_sql_place_holders("SELECT * FROM $table_name WHERE $col_name = @1");
                return $urabe->select($sql, array($col_name));
            } else
                return $urabe->select_all($table_name);
        } catch (Exception $e) {
            throw new Exception("Error Processing Request, " . $e->getMessage(), $e->getCode());
        }
    }
}
?>