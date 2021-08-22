<?php
include 'LocalConnectionExample.php';

use Urabe\DB\DBUtils;
use Urabe\Config\UrabeSettings;
use Urabe\Utils\PrettyPrintFormatter;
//1: Se crea la conexión a la BD
$conn = new LocalConnection();
//2: Formateador JSON
$jsonFormatter = new PrettyPrintFormatter(UrabeSettings::$default_pp_style);
/************************************************
 * Creación de una definición de tabla de forma *
 *            dinamica para MySQL               *
 ************************************************/
$benTabDef = DBUtils::createTableDefinitionFromMySQLTable($conn, "beneficiario");
$benTabDef->save();
$jsonFormatter->append_title("Tabla creada desde la BD: ");
$jsonFormatter->append_response($benTabDef->table);
/************************************************
 * Creación de una definición de tabla de forma *
 *         desde un archivo  JSON               *
 ************************************************/
$jsonFile = "C:\\xampp\\htdocs\\urabe\\testing\\json\\examples\\table-definition-example.json";
$tableDefinition = DBUtils::createTableDefinitionFromJSON($jsonFile);
$jsonFormatter->append_title("Tabla creada desde un archivo JSON: ");
$jsonFormatter->append_response($tableDefinition->table);
/*******
 * Fin *
 *******/
$jsonFormatter->close();
$jsonFormatter->print();