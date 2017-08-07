<?php

namespace Soldo\Tests\Utils;

use PHPUnit\Framework\TestCase;
use Soldo\Utils\Paginator;

/**
 * Class PaginatorTest
 */
class PaginatorTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidPage()
    {
        $p = new Paginator('FOO', 10);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidPerPage()
    {
        $p = new Paginator(10, 'FOO');
    }

    public function testContructorNegativePage()
    {
        $p = new Paginator(-1, 10);
        $expected = [
            'p' => 0,
            's' => 10,
        ];
        $this->assertEquals($expected, $p->toArray());
    }

    public function testConstructorTooBigPerPage()
    {
        $p = new Paginator(10, 51);
        $expected = [
            'p' => 10,
            's' => 50,
        ];
        $this->assertEquals($expected, $p->toArray());
    }

    public function testContructorNegativePerPage()
    {
        $p = new Paginator(10, -1);
        $expected = [
            'p' => 10,
            's' => 1,
        ];
        $this->assertEquals($expected, $p->toArray());
    }
}
