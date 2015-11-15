<?php


namespace AppBundle\Tests\TestUtils;


/**
 * Class Utils
 * @package AppBundle\Tests\TestUtils
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */
class Utils {

    /**
     * Set private/protected property for an object
     *
     * @param object $object
     * @param string $propertyName
     * @param mixed $propertyValue
     */
    public static function setProperty($object, $propertyName, $propertyValue) {
        $reflection = new \ReflectionClass(get_class($object));
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $propertyValue);
    }

    /**
     * Call protected/private method of a class and return the method return value.
     *
     * @param object $object Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array $parameters Array of parameters to pass into method.
     *
     * @return mixed The invoked method return value.
     */
    public static function invokeMethod($object, $methodName, array $parameters = array()) {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

}