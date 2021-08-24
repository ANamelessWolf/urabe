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
$lastId = $urabe->selector->select_one("SELECT MAX(emp_no) emp_no FROM employees");
$jsonFormatter->append_title("Insert row: ");
$values = array("emp_no" => ++$lastId, "birth_date" => "1986-10-01", "first_name" => "Miguel", "last_name" => "Alanis", "gender" => "M", "hire_date" => "2019-03-21");
$result = $urabe->executor->insert("employees", $values);
$jsonFormatter->append_response($result);
/**************
 * Insert Bulk*
 **************/
$jsonFormatter->append_title("Insert bulk: ");
$emp1 = array("emp_no" => ++$lastId, "birth_date" => "1986-10-01", "first_name" => "Miguel", "last_name" => "Alanis", "gender" => "M", "hire_date" => "2019-03-21");
$emp2 = array("emp_no" => ++$lastId, "birth_date" => "1986-10-01", "first_name" => "Miguel", "last_name" => "Alanis", "gender" => "M", "hire_date" => "2019-03-21");
$values = array();
array_push($values, $emp1, $emp2);
$result = $urabe->executor->insert_bulk("employees", $values);
$jsonFormatter->append_response($result);
/***********
 * Updater *
 ***********/
$jsonFormatter->append_title("Updater: ");
$condition = "emp_no >= $emp_no";
$values = array("birth_date" => "1921-12-24");
$result = $urabe->executor->update("employees", $values, $condition);
$jsonFormatter->append_response($result);
//Update by field condition
$jsonFormatter->append_title("Update by Field: ");
$result = $urabe->executor->update_by_field("employees", $values, "emp_no", $emp_no);
$jsonFormatter->append_response($result);
/***********
 * Delete *
 ***********/
$jsonFormatter->append_title("Delete: ");
$condition = "emp_no = $emp_no";
$result = $urabe->executor->delete("employees", $condition);
$jsonFormatter->append_response($result);
//Delete by field condition
$jsonFormatter->append_title("Delete by condition: ");
$result = $urabe->executor->delete_by_field("employees", "emp_no", $emp_no);
$jsonFormatter->append_response($result);
/*******
 * Fin *
 *******/
$jsonFormatter->close();
$jsonFormatter->print();
