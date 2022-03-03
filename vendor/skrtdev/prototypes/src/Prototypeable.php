<?php

namespace skrtdev\Prototypes;

trait Prototypeable{

    /**
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function __call(string $name, array $args)
    {
        return Prototypes::call($this, $name, $args);
    }

    /**
     * @param string $name
     * @param callable $fun
     * @throws Exception
     */
    public static function addMethod(string $name, callable $fun): void
    {
        Prototypes::addClassMethod(static::class, $name, $fun);
    }

    /**
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public static function __callStatic(string $name, array $args)
    {
        return Prototypes::callStatic(static::class, $name, $args);
    }

    /**
     * @param string $name
     * @param callable $fun
     * @throws Exception
     */
    public static function addStaticMethod(string $name, callable $fun): void
    {
        Prototypes::addClassStaticMethod(static::class, $name, $fun);
    }
}

