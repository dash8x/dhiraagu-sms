<?php

namespace Dash8x\Tests\Unit;

use Dash8x\DhiraaguSms\Hello;
use Dash8x\Tests\TestCase;

class HelloTest extends TestCase
{
    /**
     * @var Hello
     */
    private $object;

    public function setUp()
    {
        $this->object = new Hello();
    }

    public function testGetGreeting()
    {
        $this->assertSame('Hello World!', $this->object->getGreeting());
    }
}
