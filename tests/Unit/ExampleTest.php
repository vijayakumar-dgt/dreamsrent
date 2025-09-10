<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
     public function it_can_check_strings_are_equal()
    {
        $greeting = "Hello World";
        $this->assertSame("Hello World", $greeting);
    }
}
