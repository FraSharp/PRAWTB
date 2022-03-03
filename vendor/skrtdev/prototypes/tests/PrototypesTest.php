<?php

namespace skrtdev\PrototypesTests;

use Error;
use PHPUnit\Framework\TestCase;
use skrtdev\Prototypes\Exception;


class PrototypesTest extends TestCase
{
    public function testPrototypeCanBeCreated(): void
    {
        $this->assertNull(DemoClassTest::addMethod('prototypeMethod', fn() => $this->property));
    }

    public function testPrototypeCanBeCalled(): void
    {
        $this->assertTrue((new DemoClassTest)->prototypeMethod());
    }

    public function testErrorIsThrownInNonExistentMethods(): void
    {
        $this->expectException(Error::class);
        (new DemoClassTest)->nonExistentMethod();
    }

    public function testCantOverridePrototypes(): void
    {
        $this->expectException(Exception::class);
        DemoClassTest::addMethod('prototypeMethod', fn() => $this->property);
    }

    public function testCantOverrideMethods(): void
    {
        $this->expectException(Exception::class);
        DemoClassTest::addMethod('existentMethod', fn() => $this->property);
    }

    public function testCanUseCallable(): void
    {
        $this->assertNull(DemoClassTest::addMethod('unexistentMethod', 'file_get_contents'));
    }

    public function testCanUseNamedArguments(): void
    {
        DemoClassTest::addMethod('methodWithNamedArguments', function (int $named_argument){
            return $named_argument;
        });
        $this->assertEquals(12, (new DemoClassTest)->methodWithNamedArguments(named_argument: 12));
        $this->assertEquals(12, (new DemoClassTest)->methodWithNamedArguments(...['named_argument' => 12]));
    }

}
