<?php

namespace skrtdev\PrototypesTests;

use skrtdev\Prototypes\Prototypeable;

class DemoClassTest {
    use Prototypeable;

    protected static bool $static_property = true;
    protected bool $property = true;

    public function existentMethod(): void
    {

    }

    public static function existentStaticMethod(): void
    {

    }
}