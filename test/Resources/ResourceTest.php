<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Exceptions\SoldoInvalidEvent;
use Soldo\Exceptions\SoldoInvalidFingerprintException;
use Soldo\Exceptions\SoldoInvalidRelationshipException;
use Soldo\Resources\Resource;
use Soldo\Tests\Fixtures\MockResource;

/**
 * Class ResourceTest
 *
 * @backupStaticAttributes enabled
 */
class ResourceTest extends TestCase
{
    public function testFill()
    {
        /** @var MockResource $resource */
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
     * @expectedException \Soldo\Exceptions\SoldoInvalidResourceException
     */
    public function testFillCastableInvalidClassName()
    {
        /** @var MockResource $resource */
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
     * @expectedException \Soldo\Exceptions\SoldoInvalidResourceException
     */
    public function testFillCastableNotChildOfSoldoResource()
    {
        /** @var MockResource $resource */
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
     * @expectedException \InvalidArgumentException
     */
    public function testFillCastableNotValidDataset()
    {
        /** @var MockResource $resource */
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
        /** @var MockResource $resource */
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
     * @expectedException \Soldo\Exceptions\SoldoInvalidFingerprintException
     */
    public function testBuildFingerprintOrderWithNoParams()
    {
        $resource = new MockResource();
        $resource->buildFingerprint([], 'foo');
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidFingerprintException
     */
    public function testBuildFingerprintOrderWithOnlyOneParam()
    {
        $resource = new MockResource();
        $resource->buildFingerprint(['foo'], 'foo');
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidFingerprintException
     */
    public function testBuildFingerprintOrderWithNoTokenParam()
    {
        $resource = new MockResource();
        $resource->buildFingerprint(['foo', 'bar'], 'foo');
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidFingerprintException
     */
    public function testBuildFingerprintMissingAttribute()
    {
        $resource = new MockResource();
        $resource->buildFingerprint(['foo', 'token'], 'foo');
    }

    public function testBuildFingerprint()
    {
        $data = [
            'foo' => 'bar',
        ];
        $resource = new MockResource($data);
        $fingerprint = $resource->buildFingerprint(['foo', 'token'], 'a-random-token');
        $this->assertEquals(hash('sha512', 'bara-random-token'), $fingerprint);
    }

    /**
     * @param $value
     */
    private function setMockResourceBasePath($value)
    {
        MockResource::setBasePath($value);
    }

    public function testGetEventTypeNullEventType()
    {
        $resource = new MockResource();
        $this->assertNull($resource->getEventType());

        $resource->setEventType('string-that-not-contains-a-pattern');
        $this->assertNull($resource->getEventType());

    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidEvent
     */
    public function testGetEventTypeMissingAttribute()
    {
        $resource = new MockResource();
        $resource->setEventType('{id}');
        $resource->getEventType();
    }

    public function testGetEventType()
    {
        $resource = new MockResource();
        $resource->setEventType('{id}');
        $resource->id = 'CamelCaseEvent';
        $this->assertEquals('mockresource.camelcaseevent', $resource->getEventType());

        $resource->setEventType('{id}_{foo}');
        $resource->id = 'CamelCaseEvent';
        $resource->foo = ' FooParameter';
        $this->assertEquals('mockresource.camelcaseevent_fooparameter', $resource->getEventType());
    }



    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidPathException
     */
    public function testGetRemotePathMissingBasePath()
    {
        $resource = new MockResource();
        $this->setMockResourceBasePath(null);
        $resource->getRemotePath();
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidPathException
     */
    public function testGetRemotePathMissingId()
    {
        $resource = new MockResource();
        $resource->setPath('/{id}');
        $resource->getRemotePath();
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidPathException
     */
    public function testGetRemotePathInvalidBasePath()
    {
        $resource = new MockResource();
        $resource->setPath('path-without-slash');
        $resource->getRemotePath();

        $resource->setPath('/foo and whitespaces');
        $resource->getRemotePath();
    }

    public function testGetRemotePath()
    {
        $resource = new MockResource();
        $resource->setPath('/{id}');

        $resource->id = 1;
        $this->assertEquals('/resources/1', $resource->getRemotePath());

        $resource->id = 'a-string';
        $this->assertEquals('/resources/a-string', $resource->getRemotePath());

        $resource->id = 'a string with spaces';
        $this->assertEquals('/resources/a+string+with+spaces', $resource->getRemotePath());
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidRelationshipException
     */
    public function testBuildRelationshipNotMappedRelationship()
    {
        $resource = new MockResource();
        $resources = $resource->buildRelationship('resources', []);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidResourceException
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
     * @expectedException \InvalidArgumentException
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
     * @expectedException \Soldo\Exceptions\SoldoInvalidRelationshipException
     */
    public function testGetRelationshipRemotePathNotMappedRelationship()
    {
        $resource = new MockResource();
        $resource->setRelationships([]);
        $remotePath = $resource->getRelationshipRemotePath('resources');
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidResourceException
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
        $resource->setPath('/{id}');
        $resource->setRelationships(['resources' => MockResource::class]);
        $remotePath = $resource->getRelationshipRemotePath('resources');

        $this->assertEquals('/resources/1/resources', $remotePath);
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
