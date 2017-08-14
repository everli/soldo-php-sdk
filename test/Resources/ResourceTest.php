<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Exceptions\SoldoInvalidRelationshipException;
use Soldo\Resources\Resource;
use Soldo\Tests\Fixtures\MockResource;

/**
 * Class ResourceTest
 */
class ResourceTest extends TestCase
{
    public function testFill()
    {
        /** @var Resource $resource */
        $resource = new MockResource();

        $this->assertNull($resource->foo);
        $resource->fill([
            'foo' => 'bar',
            'castable_attribute' => [
                'foo' => 'bar',
                'john' => 'doe',
            ],
        ]);

        $this->assertNotNull($resource->foo);
        $this->assertEquals('bar', $resource->foo);

        $this->assertNotNull($resource->castable_attribute);
        $this->assertInternalType('array', $resource->castable_attribute);
        $this->assertEquals([
            'foo' => 'bar',
            'john' => 'doe',
        ], $resource->castable_attribute);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoCastException
     * @expectedExceptionMessage Could not cast castable_attribute. NotExistentClassName doesn't exist
     */
    public function testFillCastableInvalidClassName()
    {
        /** @var Resource $resource */
        $resource = new MockResource();
        $resource->setCast(
            ['castable_attribute' => 'NotExistentClassName']
        );

        $resource->fill([
            'castable_attribute' => [
                'foo' => 'bar',
                'john' => 'doe',
            ],
        ]);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoCastException
     * @expectedExceptionMessage Could not cast castable_attribute. stdClass is not a Resource child
     */
    public function testFillCastableNotChildOfSoldoResource()
    {
        /** @var Resource $resource */
        $resource = new MockResource();
        $resource->setCast(
            ['castable_attribute' => \stdClass::class]
        );

        $resource->fill([
            'castable_attribute' => [
                'foo' => 'bar',
                'john' => 'doe',
            ],
        ]);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoCastException
     * @expectedExceptionMessage Could not cast castable_attribute. $data is not a valid data set
     */
    public function testFillCastableNotValidDataset()
    {
        /** @var Resource $resource */
        $resource = new MockResource();
        $resource->setCast(
            ['castable_attribute' => MockResource::class]
        );

        $resource->fill(
            ['castable_attribute' => 'not_an_array']
        );
    }

    public function testFillWithCastableAttribute()
    {
        /** @var Resource $resource */
        $resource = new MockResource([]);
        $resource->setCast(
            ['castable_attribute' => MockResource::class]
        );

        $resource->fill([
            'foo' => 'bar',
            'castable_attribute' => [
                'foo' => 'bar',
                'john' => 'doe',
            ],
        ]);

        $this->assertInstanceOf(MockResource::class, $resource->castable_attribute);
        $this->assertEquals('bar', $resource->castable_attribute->foo);
        $this->assertEquals('doe', $resource->castable_attribute->john);
    }

    public function testToArrayEmptyData()
    {
        $resource = new MockResource();
        $this->assertInternalType('array', $resource->toArray());
        $this->assertEmpty($resource->toArray());
    }

    public function testToArrayLinearData()
    {
        $data = ['foo' => 'bar'];
        $resource = new MockResource($data);
        $this->assertEquals(
            $data,
            $resource->toArray()
        );
    }

    public function testToArrayMultidimensionalArray()
    {
        $data = [
            'foo' => 'bar',
            'lorem_ipsum' => [
                'foo' => 'bar',
                'john' => 'doe',
            ],
        ];
        $resource = new MockResource($data);
        $this->assertEquals(
            $data,
            $resource->toArray()
        );
    }

    public function testToArrayWithCastedAttributes()
    {
        $data = [
            'foo' => 'bar',
            'lorem_ipsum' => [
                'foo' => 'bar',
                'john' => 'doe',
            ],
        ];
        $resource = new MockResource();
        $resource->setCast(
            [ 'lorem_ipsum' => MockResource::class ]
        );
        $resource->fill($data);

        // no real need for testing this, it's just to be sure
        $this->assertInstanceOf(MockResource::class, $resource->lorem_ipsum);
        $this->assertNotNull($resource->foo);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'lorem_ipsum' => [
                    'foo' => 'bar',
                    'john' => 'doe',
                ],
            ],
            $resource->toArray()
        );
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidPathException
     * @expectedExceptionMessage Cannot retrieve remote path for Soldo\Tests\Fixtures\MockResource. "basePath" attribute is not defined.
     */
    public function testGetRemotePathMissingBasePath()
    {
        $resource = new MockResource();
        $resource->getRemotePath();
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidPathException
     * @expectedExceptionMessage Cannot retrieve remote path for Soldo\Tests\Fixtures\MockResource. "id" attribute is not defined.
     */
    public function testGetRemotePathMissingId()
    {
        $resource = new MockResource();
        $resource->setBasePath('/{id}');
        $resource->getRemotePath();
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidPathException
     * @expectedExceptionMessage Cannot retrieve remote path for Soldo\Tests\Fixtures\MockResource. "basePath" seems to be not a valid path.
     */
    public function testGetRemotePathInvalidBasePath()
    {
        $resource = new MockResource();

        $resource->setBasePath('path-without-slash');
        $resource->getRemotePath();

        $resource->setBasePath('/foo and whitespaces');
        $resource->getRemotePath();
    }

    public function testGetRemotePath()
    {
        $resource = new MockResource();
        $resource->setBasePath('/foo/{id}');

        $resource->id = 1;
        $this->assertEquals('/foo/1', $resource->getRemotePath());

        $resource->id = 'a-string';
        $this->assertEquals('/foo/a-string', $resource->getRemotePath());

        $resource->id = 'a string with spaces';
        $this->assertEquals('/foo/a+string+with+spaces', $resource->getRemotePath());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage There is no relationship mapped with "resources" name
     */
    public function testBuildRelationshipNotMappedRelationship()
    {
        $resource = new MockResource();
        $resources = $resource->buildRelationship('resources', []);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid resource class name InvalidClassName doesn't exist
     */
    public function testBuildRelationshipWithInvalidClassName()
    {
        /** @var MockResource $resource */
        $resource = new MockResource();
        $resource->setRelationships(['resources' => 'InvalidClassName']);
        $resources = $resource->buildRelationship('resources', []);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidRelationshipException
     */
    public function testBuildRelationshipRawDataNotAnArray()
    {
        /** @var MockResource $resource */
        $resource = new MockResource();
        $resource->setRelationships(['resources' => MockResource::class]);
        $resources = $resource->buildRelationship('resources', 'not-an-array');
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidRelationshipException
     */
    public function testBuildRelationshipEmptyRowData()
    {
        /** @var MockResource $resource */
        $resource = new MockResource();
        $resource->setRelationships(['resources' => MockResource::class]);
        $resources = $resource->buildRelationship('resources', []);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidRelationshipException
     */
    public function testBuildRelationshipNotAMultidimensionalArray()
    {
        $resource = new MockResource();
        $resource->setRelationships(['resources' => MockResource::class]);
        $resources = $resource->buildRelationship('resources', ['resources' => ['foo' => 'bar']]);
    }

    public function testBuildRelationship()
    {
        $resource = new MockResource();
        $resource->setRelationships(['resources' => MockResource::class]);
        $resources = $resource->buildRelationship('resources', ['resources' => [
            ['foo' => 'bar'],
            ['lorem' => 'ipsum'],
        ]]);

        $this->assertCount(2, $resources);
        foreach ($resources as $r) {
            /** @var MockResource $r */
            $this->assertInstanceOf(MockResource::class, $r);
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage There is no relationship mapped with "resources" name
     */
    public function testGetRelationshipRemotePathNotMappedRelationship()
    {
        $resource = new MockResource();
        $resource->setRelationships([]);
        $remotePath = $resource->getRelationshipRemotePath('resources');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid resource class name InvalidClassName doesn't exist
     */
    public function testGetRelationshipRemotePathInvalidClassName()
    {
        $resource = new MockResource();
        $resource->setRelationships(['resources' => 'InvalidClassName']);
        $remotePath = $resource->getRelationshipRemotePath('resources');
    }

    public function testGetRelationshipRemotePath()
    {
        $resource = new MockResource(['id' => 1]);
        $resource->setBasePath('/resource/{id}');
        $resource->setRelationships(['resources' => MockResource::class]);
        $remotePath = $resource->getRelationshipRemotePath('resources');

        $this->assertEquals('/resource/1/resources', $remotePath);
    }

    public function testFilterWhiteList()
    {
        $resource = new MockResource();

        $data = ['foo' => 'bar', 'john' => 'doe', 'lorem' => 'ipsum'];
        $whitelistedData = $resource->filterWhiteList($data);
        $this->assertEquals([], $whitelistedData);

        $resource->setWhitelisted(['mos']);
        $whitelistedData = $resource->filterWhiteList($data);
        $this->assertEquals([], $whitelistedData);

        $resource->setWhitelisted(['foo']);
        $whitelistedData = $resource->filterWhiteList($data);
        $this->assertEquals(['foo' => 'bar'], $whitelistedData);

        $resource->setWhitelisted(['foo', 'john']);
        $whitelistedData = $resource->filterWhiteList($data);
        $this->assertEquals(['foo' => 'bar', 'john' => 'doe'], $whitelistedData);
    }
}
