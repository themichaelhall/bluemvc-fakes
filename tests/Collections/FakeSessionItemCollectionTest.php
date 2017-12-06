<?php

namespace BlueMvc\Fakes\Tests\Collections;

use BlueMvc\Fakes\Collections\FakeSessionItemCollection;

/**
 * Test FakeSessionItemCollection class.
 */
class FakeSessionItemCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test count for empty collection.
     */
    public function testCountForEmptyCollection()
    {
        $fakeSessionItemCollection = new FakeSessionItemCollection();

        self::assertSame(0, count($fakeSessionItemCollection));
    }

    /**
     * Test get method.
     */
    public function testGet()
    {
        $fakeSessionItemCollection = new FakeSessionItemCollection();

        self::assertNull($fakeSessionItemCollection->get('Foo'));
    }

    /**
     * Test get method with invalid name parameter type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $name parameter is not a string.
     */
    public function testGetMethodWithInvalidNameParameterType()
    {
        $fakeSessionItemCollection = new FakeSessionItemCollection();

        $fakeSessionItemCollection->get(true);
    }

    /**
     * Test set method.
     */
    public function testSet()
    {
        $fakeSessionItemCollection = new FakeSessionItemCollection();
        $fakeSessionItemCollection->set('Foo', 'xxx');
        $fakeSessionItemCollection->set('bar', false);
        $fakeSessionItemCollection->set('foo', ['One' => 1, 'Two' => 2]);

        self::assertSame(3, count($fakeSessionItemCollection));
        self::assertSame('xxx', $fakeSessionItemCollection->get('Foo'));
        self::assertSame(false, $fakeSessionItemCollection->get('bar'));
        self::assertSame(['One' => 1, 'Two' => 2], $fakeSessionItemCollection->get('foo'));
    }

    /**
     * Test set method with invalid name parameter type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $name parameter is not a string.
     */
    public function testSetMethodWithInvalidNameParameterType()
    {
        $fakeSessionItemCollection = new FakeSessionItemCollection();

        $fakeSessionItemCollection->set(10, 'Foo');
    }

    /**
     * Test remove method.
     */
    public function testRemove()
    {
        $fakeSessionItemCollection = new FakeSessionItemCollection();
        $fakeSessionItemCollection->set('Foo', 'xxx');
        $fakeSessionItemCollection->set('bar', false);

        $fakeSessionItemCollection->remove('Foo');
        $fakeSessionItemCollection->remove('baz');

        self::assertSame(['bar' => false], iterator_to_array($fakeSessionItemCollection));
    }

    /**
     * Test remove method with invalid name parameter type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $name parameter is not a string.
     */
    public function testRemoveMethodWithInvalidNameParameterType()
    {
        $fakeSessionItemCollection = new FakeSessionItemCollection();

        $fakeSessionItemCollection->remove(1.0);
    }

    /**
     * Test iterator functionality for empty collection.
     */
    public function testIteratorForEmptyCollection()
    {
        $fakeSessionItemCollection = new FakeSessionItemCollection();

        $sessionItemArray = iterator_to_array($fakeSessionItemCollection, true);

        self::assertSame([], $sessionItemArray);
    }

    /**
     * Test iterator functionality for non-empty collection.
     */
    public function testIteratorForNonEmptyCollection()
    {
        $fakeSessionItemCollection = new FakeSessionItemCollection();
        $fakeSessionItemCollection->set('Foo', false);
        $fakeSessionItemCollection->set('Bar', 'Baz');

        $sessionItemArray = iterator_to_array($fakeSessionItemCollection, true);

        self::assertSame(['Foo' => false, 'Bar' => 'Baz'], $sessionItemArray);
    }
}
