<?php

namespace PhilKra\Tests;

class PHPUnitUtils
{

    /**
     * Credit @link https://stackoverflow.com/a/8702347
     */
    public static function callMethod($obj, $name, array $args)
    {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }

}