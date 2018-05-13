<?php

namespace Framework\Database;

class Hydrator
{
    /**
     * Hydrate a new instance of $object with the $params.
     * @param array $array
     * @param $object
     * @return mixed
     */
    public static function hydrate(array $array, $object)
    {
        $instance = new $object();
        foreach ($array as $key => $value) {
            $method = self::getSetter($key);
            if (method_exists($instance, $method)) {
                $instance->$method($value);
            } else {
                $property = lcfirst(self::getProperty($key));
                $instance->$property = $value;
            }
        }

        return $instance;
    }

    /**
     * Call the setter that correspond to the field name.
     * @param string $fieldName
     * @return string
     */
    private static function getSetter(string $fieldName): string
    {
        return 'set' . self::getProperty($fieldName);
    }

    /**
     * Get the entity attribute that correspond to the field name.
     * @param string $fieldName
     * @return string
     */
    private static function getProperty(string $fieldName): string
    {
        return join('', array_map('ucfirst', explode('_', $fieldName)));
    }
}
