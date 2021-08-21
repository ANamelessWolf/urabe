<?php
include '../src/UrabeAPI.php';

use Urabe\Urabe;
use Urabe\Config\KanojoX;
use Urabe\Config\DBDriver;
use Urabe\DB\DBUtils;
use Urabe\DB\InsertStatement;
use Urabe\DB\InsertBulkStatement;
use Urabe\DB\UpdateStatement;
use Urabe\Utils\JsonPrettyPrint;
use Urabe\Service\UrabeResponse;

class LocalConnection extends KanojoX
{
    public function __construct()
    {
        $this->db_driver = DBDriver::MYSQL;
        $this->host = "127.0.0.1";
        $this->port = 3306;
        $this->db_name = 'employees';
        $this->user_name = 'root';
        $this->password = "";
    }
}

//1: Se crea la conexión a la BD
$conn = new LocalConnection();
//2: Seleccionando las tablas de definición
$benTabDef = DBUtils::createTableDefinitionFromMySQLTable($conn, "employees");
$departmentTabDef = DBUtils::createTableDefinitionFromMySQLTable($conn, "current_dept_emp");
//3: Inicialización del objeto Urabe
$urabe = new Urabe($conn, $benTabDef);
//5: Formateador JSON
$jsonPretty = new JsonPrettyPrint();
$html .= '<html><head>' .
'<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">' .
'<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>' .
'<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>' .
'<style>' .
'body { background-color: ' . $bg_color . '} ' .
//'div { padding:0; margin:0; display:inline-block; float:left; }' .
'</style>' .
'</head>' .
'<body>';
/*************
 * Selección *
 *************/
$html .= "<h2>Selección: </h2>";
$html .= '<div style="width: 50%; margin-left: 2%;">';
$result = $urabe->selector->select("SELECT * FROM employees ORDER BY emp_no DESC LIMIT 1");
$json = json_decode(json_encode($result), true);
$html .= $jsonPretty->get_format($json);
$html .= "</div><br>";
//Cambio de tabla de parseo
$html .= '<div style="width: 50%; margin-left: 2%;">';
$urabe->update_parser($departmentTabDef);
$result = $urabe->selector->select("SELECT * FROM current_dept_emp ORDER BY emp_no DESC LIMIT 1");
$json = json_decode(json_encode($result), true);
$html .= $jsonPretty->get_format($json);
$html .= "</div>";
//Otros ejemplos de selección 
$urabe->update_parser($benTabDef);
$emp_no = $urabe->selector->select_one("SELECT emp_no FROM employees ORDER BY emp_no DESC LIMIT 1");
$emp_list = $urabe->selector->select_items("SELECT emp_no FROM employees ORDER BY emp_no DESC LIMIT 10");

/*******
 * Fin *
 *******/
//Cierre HTML
$html .= '</body></html>';
echo $html;
?>