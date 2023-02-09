<?php

namespace Soldo\Tests\Resources;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Soldo\Exceptions\SoldoInvalidEvent;
use Soldo\Exceptions\SoldoInvalidFingerprintException;
use Soldo\Exceptions\SoldoInvalidPathException;
use Soldo\Exceptions\SoldoInvalidRelationshipException;
use Soldo\Exceptions\SoldoInvalidResourceException;
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
        $this->assertIsArray($resource->castable_attribute);
        $this->assertEquals([
            'foo' => 'bar',
            'john' => 'doe',
        ], $resource->castable_attribute);
    }


    public function testFillCastableInvalidClassName()
    {
        $this->expectException(SoldoInvalidResourceException::class);

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

    public function testFillCastableNotChildOfSoldoResource()
    {
        $this->expectException(SoldoInvalidResourceException::class);

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

    public function testFillCastableNotValidDataset()
    {
        $this->expectException(InvalidArgumentException::class);
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
        $this->assertIsArray($resource->toArray());
        $this->assertIsArray($resource->toArray());
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

    public function testBuildFingerprintOrderWithNoParams()
    {
        $this->expectException(SoldoInvalidFingerprintException::class);

        $resource = new MockResource();
        $resource->buildFingerprint([], 'foo');
    }

    public function testBuildFingerprintOrderWithOnlyOneParam()
    {
        $this->expectException(SoldoInvalidFingerprintException::class);

        $resource = new MockResource();
        $resource->buildFingerprint(['foo'], 'foo');
    }

    public function testBuildFingerprintOrderWithNoTokenParam()
    {
        $this->expectException(SoldoInvalidFingerprintException::class);

        $resource = new MockResource();
        $resource->buildFingerprint(['foo', 'bar'], 'foo');
    }

    public function testBuildFingerprintMissingAttribute()
    {
        $this->expectException(SoldoInvalidFingerprintException::class);

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

    public function testGetEventTypeMissingAttribute()
    {
        $this->expectException(SoldoInvalidEvent::class);

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

    public function testGetRemotePathMissingBasePath()
    {
        $this->expectException(SoldoInvalidPathException::class);

        $resource = new MockResource();
        $this->setMockResourceBasePath(null);
        $resource->getRemotePath();
    }

    public function testGetRemotePathMissingId()
    {
        $this->expectException(SoldoInvalidPathException::class);

        $resource = new MockResource();
        $resource->setPath('/{id}');
        $resource->getRemotePath();
    }

    public function testGetRemotePathInvalidBasePath()
    {
        $this->expectException(SoldoInvalidPathException::class);

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

    public function testBuildRelationshipNotMappedRelationship()
    {
        $this->expectException(SoldoInvalidRelationshipException::class);

        $resource = new MockResource();
        $resource->buildRelationship('resources', []);
    }

    public function testBuildRelationshipWithInvalidClassName()
    {
        $this->expectException(SoldoInvalidResourceException::class);

        /** @var MockResource $resource */
        $resource = new MockResource();
        $resource->setRelationships(['resources' => 'InvalidClassName']);
        $resource->buildRelationship('resources', []);
    }

    public function testBuildRelationshipRawDataNotAnArray()
    {
        $this->expectException(SoldoInvalidRelationshipException::class);

        /** @var MockResource $resource */
        $resource = new MockResource();
        $resource->setRelationships(['resources' => MockResource::class]);
        $resource->buildRelationship('resources', 'not-an-array');
    }

    public function testBuildRelationshipEmptyRowData()
    {
        $this->expectException(SoldoInvalidRelationshipException::class);

        /** @var MockResource $resource */
        $resource = new MockResource();
        $resource->setRelationships(['resources' => MockResource::class]);
        $resource->buildRelationship('resources', []);
    }

    public function testBuildRelationshipNotAMultidimensionalArray()
    {
        $this->expectException(InvalidArgumentException::class);

        $resource = new MockResource();
        $resource->setRelationships(['resources' => MockResource::class]);
        $resource->buildRelationship('resources', ['resources' => ['foo' => 'bar']]);
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

    public function testGetRelationshipRemotePathNotMappedRelationship()
    {
        $this->expectException(SoldoInvalidRelationshipException::class);

        $resource = new MockResource();
        $resource->setRelationships([]);
        $resource->getRelationshipRemotePath('resources');
    }

    public function testGetRelationshipRemotePathInvalidClassName()
    {
        $this->expectException(SoldoInvalidResourceException::class);
        $resource = new MockResource();
        $resource->setRelationships(['resources' => 'InvalidClassName']);
        $resource->getRelationshipRemotePath('resources');
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
