<?php

namespace skrtdev\PrototypesTests;

use Error;
use skrtdev\Prototypes\Exception;
use PHPUnit\Framework\TestCase;

class StaticPrototypesTest extends TestCase
{

    public function testPrototypeCanBeCreated(): void
    {
        $this->assertNull(DemoClassTest::addStaticMethod('prototypeStaticMethod', fn() => self::$static_property));
    }

    public function testPrototypeCanBeCalled(): void
    {
        $this->assertTrue(DemoClassTest::prototypeStaticMethod());
    }

    public function testErrorIsThrownInNonExistentMethods(): void
    {
        $this->expectException(Error::class);
        DemoClassTest::nonExistentStaticMethod();
    }

    public function testCantOverridePrototypes(): void
    {
        $this->expectException(Exception::class);
        DemoClassTest::addStaticMethod('prototypeStaticMethod', fn() => self::$static_property);
    }

    public function testCantOverrideMethods(): void
    {
        $this->expectException(Exception::class);
        DemoClassTest::addStaticMethod('existentStaticMethod', fn() => self::$static_property);
    }

    public function testCanUseNamedArguments(): void
    {
        DemoClassTest::addStaticMethod('staticMethodWithNamedArguments', function (int $named_argument){
            return $named_argument;
        });
        $this->assertEquals(12, DemoClassTest::staticMethodWithNamedArguments(named_argument: 12));
        $this->assertEquals(12, DemoClassTest::staticMethodWithNamedArguments(...['named_argument' => 12]));
    }

}
