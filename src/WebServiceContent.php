<?php
/**
 * This class obtains the web service content from GET variables, URL parameters and POST body
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class WebServiceContent
{
    /**
     * @var array The web service get variables
     */
    public $get_variables;
    /**
     * @var array The web service url parameters, the
     * parameters can be associated in pairs or by given index. The parameters are
     * specified in the UrabeSettings
     */
    public $url_params;
    /**
     * @var object The web service body is expected to be in a JSON
     * When the service is GET the body is NULL
     */
    public $body;
    /**
     * @var object The web service request method string value
     */
    public $method;

    /**
     * __construct
     *
     * Initialize a new instance of the web service content
     */
    public function __construct()
    {
        $this->get_variables = array();
        //GET Variables
        foreach ($_GET as $key => $value)
            $this->get_variables[$key] = $value;
        //URL parameters
        if (isset($_SERVER['PATH_INFO']))
            $this->url_params = $this->parse_url_params(explode('/', trim($_SERVER['PATH_INFO'], '/')));
        else
            $this->url_params = array();
        //POST content, must be a JSON string
        $this->body = file_get_contents('php://input');
        $this->body = json_decode($this->body);
        //The Request method
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Returns the url parameters depending on the Urabe settings variable
     * url_params_in_pairs. When url parameters in pairs is enable and odd number of parameters will result in the last
     * parameter having a null value
     *
     * @param array $params The read parameters taken from the url as a String array
     * @return array The url parameters
     */
    private function parse_url_params($params)
    {
        $items_count = count($params);
        if (KanojoX::$settings->url_params_in_pairs) {
            if ($items_count == 1)
                return array($params[0] => null);
            else {
                $url_params = array();
                for ($i = 0; $i < $items_count; $i += 2)
                    $url_params[$params[$i]] = $i < $i + 1 ? $params[$i + 1] : null;
            }
            return $url_params;
        }
    }
}
?>