<?php
include '../src/UrabeAPI.php';

use Urabe\Urabe;
use Urabe\Config\KanojoX;
use Urabe\Config\DBDriver;
use Urabe\Config\UrabeSettings;
use Urabe\DB\DBUtils;
use Urabe\Utils\PrettyPrintFormatter;
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
$jsonFormatter = new PrettyPrintFormatter(UrabeSettings::$default_pp_style);
/**********
 * Select *
 **********/
//Select data using place holders
$jsonFormatter->append_title("Selección con place holders: ");
$result = $urabe->selector->select("SELECT * FROM employees WHERE emp_no IN (?, ?)", array(500026, 500025));
$jsonFormatter->append_response($result);
//Change the parsing data
$urabe->update_parser($departmentTabDef);
$jsonFormatter->append_title("Selección normal: ");
$result = $urabe->selector->select("SELECT * FROM current_dept_emp ORDER BY emp_no DESC LIMIT 1");
$jsonFormatter->append_response($result);
//Otros ejemplos de selección 
$urabe->update_parser($benTabDef);
$jsonFormatter->append_title("Selección simple y por columnas: ");
$emp_no = $urabe->selector->select_one("SELECT emp_no FROM employees ORDER BY emp_no DESC LIMIT 1");
$emp_list = $urabe->selector->select_items("SELECT emp_no FROM employees ORDER BY emp_no DESC LIMIT 10");
$result = array("select_one"=>$emp_no, "select_items"=> $emp_list);
$jsonFormatter->append_response($result);
/**********
 * Insert *
 **********/


/*******
 * Fin *
 *******/
$jsonFormatter->close();
$jsonFormatter->print();
?>