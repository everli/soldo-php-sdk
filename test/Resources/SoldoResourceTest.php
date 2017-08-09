<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Resources\SoldoResource;
use Soldo\Tests\Fixtures\MockResource;


/**
 * Class SoldoResourceTest
 */
class SoldoResourceTest extends TestCase
{
    /** @var MockResource */
    private static $resource;

    public static function setUpBeforeClass()
    {
        $r = new MockResource(['an_attribute' => 'a_value']);
        self::$resource = $r;
    }


    public function testFill()
    {
        /** @var SoldoResource $resource */
        $resource = new MockResource([]);

        $this->assertNull($resource->an_attribute);
        $resource->fill([
            'an_attribute' => 'a_value'
        ]);

        $this->assertNotNull($resource->an_attribute);
        $this->assertEquals('a_value', $resource->an_attribute);
    }

    public function testToArray()
    {
        $resource = new MockResource([]);
        $this->assertEmpty($resource->toArray());

        $resource->fill(
            ['an_attribute' => 'a_value']
        );

        $this->assertEquals(
            ['an_attribute' => 'a_value'],
            $resource->toArray()
        );
    }

    public function testGetRemotePath()
    {
        $resource = new MockResource([]);
        $this->assertEquals('/', $resource->getRemotePath());

        $resource->id = null;
        $this->assertEquals('/', $resource->getRemotePath());

        $resource->id = 1;
        $this->assertEquals('/1', $resource->getRemotePath());

        $resource->id = 'a-string';
        $this->assertEquals('/a-string', $resource->getRemotePath());

        $resource->id = 'a string with spaces';
        $this->assertEquals('/a+string+with+spaces', $resource->getRemotePath());

        $resource = new MockResource([]);
        $resource->setBasePath('/paths');
        $this->assertEquals('/paths/', $resource->getRemotePath());

        $resource->id = null;
        $this->assertEquals('/paths/', $resource->getRemotePath());

        $resource->id = 1;
        $this->assertEquals('/paths/1', $resource->getRemotePath());

        $resource->id = 'a-string';
        $this->assertEquals('/paths/a-string', $resource->getRemotePath());

        $resource->id = 'a string with spaces';
        $this->assertEquals('/paths/a+string+with+spaces', $resource->getRemotePath());


    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage There is no relationship mapped with \Soldo\Resources\Card name
     */
    public function testBuildRelationshipNotMappedRelationship()
    {
        $resource = new MockResource([]);
        $resource->buildRelationship('\Soldo\Resources\Card', []);
    }



}
