<?php
include 'LocalConnectionExample.php';

use Urabe\DB\DBUtils;
use Urabe\Utils\JsonPrettyPrint;
//1: Se crea la conexión a la BD
$conn = new LocalConnection();
//2: Formateador JSON
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
/************************************************
 * Creación de una definición de tabla de forma *
 *            dinamica para MySQL               *
 ************************************************/
$benTabDef = DBUtils::createTableDefinitionFromMySQLTable($conn, "beneficiario");
$benTabDef->save();
$html .= '<div style="width: 50%; margin-left: 2%;">';
$html .= "<h2>Tabla creada desde la BD: </h2>";
$json = json_decode(json_encode($benTabDef->table), true);
$html .= $jsonPretty->get_format($json);
$html .= "</div>";
/************************************************
 * Creación de una definición de tabla de forma *
 *         desde un archivo  JSON               *
 ************************************************/
$jsonFile = "C:\\xampp\\htdocs\\urabe\\testing\\json\\examples\\table-definition-example.json";
$tableDefinition = DBUtils::createTableDefinitionFromJSON($jsonFile);
$html .= '<div style="width: 50%; margin-left: 2%;">';
$html .= "<h2>Tabla creada desde un archivo JSON: </h2>";
$json = json_decode(json_encode($tableDefinition->table), true);
$html .= $jsonPretty->get_format($json);
$html .= "</div>";
$html .= '</body></html>';
echo $html;
