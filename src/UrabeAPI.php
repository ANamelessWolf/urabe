<?php
use Urabe\Config\UrabeSettings;

require 'resources/Warai.php';

require 'utils/Enum.php';
require 'utils/NumericType.php';
require 'utils/JsonPrettyStyle.php';
require 'config/ServiceStatus.php';
require 'config/FieldTypeCategory.php';
require 'config/UrabeSettings.php';
require 'config/ConnectionError.php';

switch (UrabeSettings::$language) {
    case '"EN"':
        require 'resources/ErrorMessages_en.php';
        require 'resources/WaraiMessages_en.php';
    break;
    default:
    break;
}
require 'config/DBDriver.php';
require 'config/ErrorHandler.php';
require 'config/KanojoX.php';

//Database
require 'db/FieldDefinition.php';
require 'db/DateFieldDefinition.php';
require 'db/BooleanFieldDefinition.php';
require 'db/NumericFieldDefinition.php';
require 'db/StringFieldDefinition.php';
require 'db/DBUtils.php';
require 'db/TableDefinition.php';
