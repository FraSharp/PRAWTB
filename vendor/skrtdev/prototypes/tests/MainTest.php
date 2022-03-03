<?php

namespace skrtdev\PrototypesTests;

use skrtdev\Prototypes\Exception;
use skrtdev\Prototypes\Prototypes;
use PHPUnit\Framework\TestCase;
use stdClass;

class MainTest extends TestCase
{
    public function testNonExistentClass(): void
    {
        $this->expectException(Exception::class);
        Prototypes::addClassMethod('RandomClass', 'RandomMethod', fn() => true);
    }

    public function testNonPrototypeableClass(): void
    {
        $this->expectException(Exception::class);
        Prototypes::addClassMethod(stdClass::class, 'RandomMethod', fn() => true);
    }

    public function testMainClassCannotBeInstantiated(): void
    {
        $this->expectException(Exception::class);
        $_ = new Prototypes();
    }
}
