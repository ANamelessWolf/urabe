<?php
use Urabe\Config\UrabeSettings;

require 'resources/Warai.php';

require 'utils/Enum.php';
require 'utils/NumericType.php';
require 'utils/JsonPrettyStyle.php';
require 'utils/JsonPrettyPrint.php';
require 'utils/PrettyPrintFormatter.php';
require 'utils/HasamiUtils.php';
require 'config/ServiceStatus.php';
require 'config/FieldTypeCategory.php';
require 'config/DBDriver.php';
require 'config/UrabeSettings.php';
require 'config/ConnectionError.php';
//Exceptions
require 'runtime/MysteriousParsingException.php';
require 'runtime/UrabeSQLException.php';

switch (UrabeSettings::$language) {
    case 'EN':
        require 'resources/ErrorMessages_en.php';
        require 'resources/WaraiMessages_en.php';
    break;
    default:
    break;
}
require 'config/ErrorHandler.php';
require 'config/KanojoX.php';
require 'config/KanojoXFile.php';
//Model
require 'model/Field.php';
require 'model/Table.php';
//Database
require 'service/UrabeResponse.php';
require 'db/FieldDefinition.php';
require 'db/DateFieldDefinition.php';
require 'db/BooleanFieldDefinition.php';
require 'db/NumericFieldDefinition.php';
require 'db/StringFieldDefinition.php';
require 'db/DBUtils.php';
require 'db/TableDefinition.php';
require 'db/MysteriousParser.php';
require 'db/MysteriousParserForTableDefinition.php';
require 'db/MySQLTableDefinition.php';
require 'db/ORACLETableDefinition.php';
require 'db/PGTableDefinition.php';
require 'db/PreparedStatement.php';
require 'db/InsertStatement.php';
require 'db/InsertBulkStatement.php';
require 'db/UpdateStatement.php';
require 'db/DBKanojoX.php';
require 'db/MYSQLKanojoX.php';
require 'db/ORACLEKanojoX.php';
require 'db/PGKanojoX.php';
require 'db/Selector.php';
require 'db/Executor.php';
//Service
require 'service/VariableCollection.php';
require 'service/GETVariables.php';
require 'service/WebServiceBody.php';
require 'service/WebServiceContent.php';

//Clases
require 'Urabe.php';
