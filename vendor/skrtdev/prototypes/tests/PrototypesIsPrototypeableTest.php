<?php

namespace skrtdev\PrototypesTests;

use Error;
use skrtdev\Prototypes\Exception;
use PHPUnit\Framework\TestCase;
use skrtdev\Prototypes\Prototypes;

class PrototypesIsPrototypeableTest extends TestCase
{

    public function testPrototypeCanBeCreated(): void
    {
        $this->assertNull(Prototypes::addStaticMethod('prototypeStaticMethod', fn() => true));
    }

    public function testPrototypeCanBeCalled(): void
    {
        $this->assertTrue(Prototypes::prototypeStaticMethod());
    }

    public function testErrorIsThrownInNonExistentMethods(): void
    {
        $this->expectException(Error::class);
        Prototypes::nonExistentStaticMethod();
    }

    public function testCantOverridePrototypes(): void
    {
        $this->expectException(Exception::class);
        Prototypes::addStaticMethod('prototypeStaticMethod', fn() => true);
    }

    public function testCantOverrideMethods(): void
    {
        $this->expectException(Exception::class);
        // isPrototypeable is an existent static method
        Prototypes::addStaticMethod('isPrototypeable', fn() => true);
    }

    public function testCantAddNonStaticMethods(): void
    {
        $this->expectException(Exception::class);
        // Prototypes class is not intended to have non-static methods
        Prototypes::addMethod('justARandomMethod', fn() => true);
    }

}
