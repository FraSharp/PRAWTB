<?php

namespace skrtdev\Prototypes;

use Closure, Error;

class Prototypes {

    use Prototypeable;

    /**
     * @var array[]
     */
    protected static array $methods = [];

    /**
     * @var Closure[]
     */
    protected static array $static_methods = [];

    /**
     * @var Closure[]
     */
    protected static array $classes = [];


    /**
     * @throws Exception
     */
    protected static function validateClassMethod(string $class_name, string $name): void{
        if(self::isPrototypeable($class_name)){
            self::$methods[$class_name] ??= [];
            self::$static_methods[$class_name] ??= [];
            if(!method_exists($class_name, $name)){
                if(self::classHasPrototypeMethod($class_name, $name)){
                    throw new Exception("Invalid method name provided for class '$class_name': method '$name' is already a Prototype");
                }
            }
            else{
                throw new Exception("Invalid method name provided for class '$class_name': method '$name' already exists");
            }
        }
        else{
            throw new Exception("Invalid class provided: class '$class_name' is not Prototypeable");
        }
    }

    /**
     * @param callable|Closure $callable
     */
    protected static function normalizeCallable($callable): Closure
    {
        if(!($callable instanceof Closure)){
            $callable = Closure::fromCallable($callable);
        }
        return $callable;
    }


    /**
     * @param string $class_name
     * @param string $name
     * @param callable $fun
     * @throws Exception
     */
    public static function addClassMethod(string $class_name, string $name, callable $fun): void
    {
        self::validateClassMethod($class_name, $name);
        self::$methods[$class_name][$name] = self::normalizeCallable($fun);
    }

    /**
     * @param string $class_name
     * @param string $name
     * @param callable $fun
     * @throws Exception
     */
    public static function addClassStaticMethod(string $class_name, string $name, callable $fun): void
    {
        self::validateClassMethod($class_name, $name);
        self::$static_methods[$class_name][$name] = self::normalizeCallable($fun);
    }

    /**
     * @param object $obj
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public static function call(object $obj, string $name, array $args)
    {
        $class_name = get_class($obj);
        self::$methods[$class_name] ??= [];
        if(isset(self::$methods[$class_name][$name])){
            $closure = self::$methods[$class_name][$name];
            return $closure->call($obj, ...$args);
        }
        else{
            throw new Error("Call to undefined method $class_name::$name()");
        }
    }

    /**
     * @param string $class_name
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public static function callStatic(string $class_name, string $name, array $args)
    {
        self::$static_methods[$class_name] ??= [];
        if(isset(self::$static_methods[$class_name][$name])){
            $closure = self::$static_methods[$class_name][$name];
            return ($closure->bindTo(null, $class_name))(...$args);
        }
        else{
            throw new Error("Call to undefined static method $class_name::$name()");
        }
    }

    /**
     * @param string $class_name
     * @return bool
     * @throws Exception
     */
    public static function isPrototypeable(string $class_name): bool
    {
        return self::$classes[$class_name] ??= in_array(Prototypeable::class, self::getClassTraits($class_name));
    }

    /**
     * @param string $class_name
     * @param string $method_name
     * @return bool
     */
    public static function classHasPrototypeMethod(string $class_name, string $method_name): bool
    {
        return isset(self::$methods[$class_name][$method_name]) || isset(self::$static_methods[$class_name][$method_name]);
    }

    /**
     * @param string $class
     * @return array
     * @throws Exception
     */
    protected static function getClassTraits(string $class): array
    {
        if(!class_exists($class)){
            throw new Exception("Class $class does not exist");
        }
        $traits = [];
        do {
            $traits = array_merge(class_uses($class), $traits);
        }
        while($class = get_parent_class($class));

        foreach ($traits as $trait) {
            $traits = array_merge(class_uses($trait), $traits);
        }

        return array_unique(array_values($traits));
    }


    /**
     * Prototypes constructor that disallow instantiation
     * @throws Exception
     */
    public function __construct()
    {
        throw new Exception(static::class.' class can not be instantiated');
    }

    /**
     * @param mixed ...$_
     * @throws Exception
     */
    final public static function addMethod(...$_): void
    {
        throw new Exception('Adding normal method to '. static::class . ' does not make sense. Did you mean addStaticMethod?');
    }

}
