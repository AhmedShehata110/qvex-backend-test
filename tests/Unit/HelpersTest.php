<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    public function test_convert_to_array_with_null()
    {
        $this->assertSame([], convertToArray(null));
    }

    public function test_convert_to_array_with_string()
    {
        $this->assertSame(['a', 'b'], convertToArray('a,b'));
    }

    public function test_convert_to_array_with_array()
    {
        $this->assertSame([1, 2, 3], convertToArray([1, 2, 3]));
    }

    public function test_convert_to_array_with_collection()
    {
        $collection = new \Illuminate\Support\Collection(['x', 'y']);
        $this->assertSame(['x', 'y'], convertToArray($collection));
    }

    public function test_uuid_returns_string()
    {
        $u = uuid();
        $this->assertIsString($u);
        $this->assertNotEmpty($u);
    }
}
