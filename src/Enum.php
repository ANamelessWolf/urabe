<?php 
require_once "/resources/Warai.php";
/**
 * This class wraps an ENUM definition, this idea was solve using the post of Brian Cline
 * under the stack overflow platform
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 * @example location description https://stackoverflow.com/questions/254514/php-and-enumerations?answertab=active#tab-top
 */
abstract class Enum
{
    /**
     * @var mixed The enum constants
     */
    private static $constCacheArray = null;
    /**
     * Retrieves the enum constants as an a key value paired array
     * Once the constants are retrieve their values are stored in $constCacheArray
     * @return array The enum constants inside an array
     */
    private static function getConstants()
    {
        if (self::$constCacheArray == null)
            self::$constCacheArray = [];
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }
    /**
     * Check if the given name is a valid member of
     * the ENUM class
     *
     * @param string $name The name to validate
     * @param boolean $strict True if case sensitive will apply to compare the name
     * @return boolean True if the name is a valid ENUM member
     */
    public static function isValidName($name, $strict = false)
    {
        $constants = self::getConstants();
        if ($strict)
            return array_key_exists($name, $constants);
        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }
    /**
     * Check if the given value belongs to an ENUM member
     *
     * @param string $value The value to validate
     * @param boolean $strict True if case sensitive will apply to compare the value
     * @return boolean True if the value belongs to an ENUM member
     */
    public static function isValidValue($value, $strict = true)
    {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict);
    }
    /**
     * Gets the member name assigned to a value
     *
     * @param mixed $value The value to extract its member name
     * @param boolean $strict True if case sensitive will apply to compare the value
     * @throws Exception An exception is thrown if the value does not belong to an Enum member
     * @return string The member name
     */
    public static function getName($value, $strict = true)
    {
        $constants = self::getConstants();
        $values = array_values($constants);
        if (in_array($value, $values, $strict)) {
            $index = array_search($value, $values);
            return array_keys($constants)[$index];
        } else
            throw new Exception(sprintf(ERR_ENUM_INVALID_VALUE, $value, get_called_class()));
    }
    /**
     * Gets the value assigned to an ENUM member
     *
     * @param string $name The ENUM member name
     * @param boolean $strict True if case sensitive will apply to compare the ENUM member name
     * @throws Exception An exception is thrown if the name is not a member name
     * @return mixed The ENUM member value
     */
    public static function getValue($name, $strict = true)
    {
        $constants = self::getConstants();
        if ($strict)
            $keys = array_key_exists($name, $constants);
        else
            $keys = array_map('strtolower', array_keys($constants));
        if (array_key_exists($name, $keys)) {
            $index = array_search($name, $keys);
            return array_values($constants)[$index];
        } else
            throw new Exception(sprintf(ERR_ENUM_INVALID_NAME, $name, get_called_class()));
        return array_keys($constants)[$index];
    }
}
?>