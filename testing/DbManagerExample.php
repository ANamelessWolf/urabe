<?php
include '../src/UrabeAPI.php';

use Urabe\Config\UrabeSettings;
use Urabe\DB\DBUtils;
use Urabe\DB\MysteriousParser;
use Urabe\DB\MYSQLKanojoX;
use Urabe\DB\PreparedStatement;
use Urabe\DB\InsertStatement;
use Urabe\DB\InsertBulkStatement;
use Urabe\DB\UpdateStatement;
use Urabe\Utils\PrettyPrintFormatter;
use Urabe\Service\UrabeResponse;

class LocalConnection extends Urabe\Config\KanojoX
{
    public function __construct()
    {
        $this->db_driver = Urabe\Config\DBDriver::MYSQL;
        $this->host = "127.0.0.1";
        $this->port = 3306;
        $this->db_name = 'employees';
        $this->user_name = 'root';
        $this->password = "";
    }
}


//1: Se crea la conexión a la BD
$conn = new LocalConnection();
//2: Creación de la definición de la tabla
$benTabDef = DBUtils::createTableDefinitionFromMySQLTable($conn, "employees");
//3: Creación del parser
$parser = new MysteriousParser($benTabDef);
//4: Definiendo el conector
$connector = new MYSQLKanojoX($conn, $parser);
$connector->connect();
//5: Formateador JSON
$jsonFormatter = new PrettyPrintFormatter(UrabeSettings::$default_pp_style);
/*************
 * Selección *
 *************/
$jsonFormatter->append_title("Selección: ");
$sql = "SELECT * FROM employees ORDER BY emp_no DESC LIMIT 10";
$result = $connector->fetch_assoc($sql);
$response = new UrabeResponse();
$response = $response->get_response("Empleados seleccionados", $result, $sql);
$lastId = $response["result"][0]["emp_no"];
$jsonFormatter->append_response($response);
/*************
 * Inserción *
 *************/
$jsonFormatter->append_title("Inserción: ");
$query_format = "INSERT INTO employees (%s) VALUES (%s)";
$values = array("emp_no" => ++$lastId, "birth_date" => "1986-10-01", "first_name" => "Miguel", "last_name" => "Alanis", "gender" => "M", "hire_date" => "2019-03-21");
$stmt = new InsertStatement($connector, $values);
$sql = $stmt->build_sql($query_format);
$response = $connector->execute($sql, $stmt->values);
$response->query = $stmt->check_sql($query_format);
$response->result = array();
array_push($response->result, array("emp_no" => $lastId));
$jsonFormatter->append_response($response);
/******************
 * Inserción Bulk *
 ******************/
$jsonFormatter->append_title("Inserción Bulk: ");
$query_format = "INSERT INTO employees (%s) VALUES %s";
$emp1 = array("emp_no" => ++$lastId, "birth_date" => "1986-10-01", "first_name" => "Miguel", "last_name" => "Alanis", "gender" => "M", "hire_date" => "2019-03-21");
$emp2 = array("emp_no" => ++$lastId, "birth_date" => "1986-10-01", "first_name" => "Miguel", "last_name" => "Alanis", "gender" => "M", "hire_date" => "2019-03-21");
$values = array();
array_push($values, $emp1, $emp2);
$stmt = new InsertBulkStatement($connector, $values);
$sql = $stmt->build_sql($query_format);
$response = $connector->execute($sql, $stmt->values);
$response->query = $stmt->check_sql($query_format);
$response->result = array();
array_push($response->result, array("emp_no" => $lastId - 1), array("emp_no" => $lastId));
$jsonFormatter->append_response($response);
/**************
 * Actualizar *
 **************/
$jsonFormatter->append_title("Actualizar: ");
$emp_no = $lastId - 3;
$query_format = "UPDATE employees SET %s WHERE emp_no >= $emp_no";
$values = array("birth_date" => "1921-12-24");
$stmt = new UpdateStatement($connector, $values);
$sql = $stmt->build_sql($query_format);
$response = $connector->execute($sql, $stmt->values);
$response->query = $stmt->check_sql($query_format);
$response->result = array();
$jsonFormatter->append_response($response);
/************
 * Eliminar *
 ************/
$jsonFormatter->append_title("Eliminar: ");
$emp_no = $lastId - 3;
$query_format = "DELETE FROM employees WHERE emp_no >= %s";
$values = array("emp_no" => $emp_no);
$stmt = new PreparedStatement($connector, $values);
$sql = $stmt->build_sql($query_format);
$response = $connector->execute($sql, $stmt->values);
$response->query = $stmt->check_sql($query_format);
$response->result = array();
$jsonFormatter->append_response($response);
/*******
 * Fin *
 *******/
$jsonFormatter->close();
$jsonFormatter->print();
?>