<?php

declare(strict_types=1);

namespace Nebkam\FluentTest\Test;

use Nebkam\FluentTest\RequestBuilder;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionObject;

class RequestBuilderTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testStaticUri(): void
    {
        $requestBuilder = RequestBuilder::create();
        $requestBuilder->setUri('/v5/places/54');
        $this->assertEquals('/v5/places/54', self::getPrivateProperty($requestBuilder, 'uri'));
    }

    /**
     * @throws ReflectionException
     */
    public function testSetDynamicUri(): void
    {
        $requestBuilder = RequestBuilder::create();
        $requestBuilder->setUri('/v5/places/%d/basic', 54);
        $this->assertEquals('/v5/places/54/basic', self::getPrivateProperty($requestBuilder, 'uri'));
    }

    /**
     * @throws ReflectionException
     */
    public function testSetDynamicUriWith2Placeholders(): void
    {
        $requestBuilder = RequestBuilder::create();
        $requestBuilder->setUri('/v5/places/%d/basic/%s', 54, 'NY');
        $this->assertEquals('/v5/places/54/basic/NY', self::getPrivateProperty($requestBuilder, 'uri'));
    }

    /**
     * @param $object
     *
     * @throws ReflectionException
     */
    private static function getPrivateProperty($object, string $property): ?string
    {
        $class = new ReflectionObject($object);
        $property = $class->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
