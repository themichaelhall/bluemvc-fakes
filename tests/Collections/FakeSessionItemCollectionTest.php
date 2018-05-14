<?php

declare(strict_types=1);

namespace BlueMvc\Fakes\Tests\Collections;

use BlueMvc\Fakes\Collections\FakeSessionItemCollection;
use PHPUnit\Framework\TestCase;

/**
 * Test FakeSessionItemCollection class.
 */
class FakeSessionItemCollectionTest extends TestCase
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
     * Test set method.
     */
    public function testSet()
    {
        $fakeSessionItemCollection = new FakeSessionItemCollection();
        $fakeSessionItemCollection->set('Foo', 'xxx');
        $fakeSessionItemCollection->set('bar', false);
        $fakeSessionItemCollection->set('foo', ['One' => 1, 'Two' => 2]);
        $fakeSessionItemCollection->set('1', 2);

        self::assertSame(4, count($fakeSessionItemCollection));
        self::assertSame('xxx', $fakeSessionItemCollection->get('Foo'));
        self::assertSame(false, $fakeSessionItemCollection->get('bar'));
        self::assertSame(['One' => 1, 'Two' => 2], $fakeSessionItemCollection->get('foo'));
        self::assertSame(2, $fakeSessionItemCollection->get('1'));
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
        $fakeSessionItemCollection->set('1', 2);

        $sessionItemArray = iterator_to_array($fakeSessionItemCollection, true);

        self::assertSame(['Foo' => false, 'Bar' => 'Baz', 1 => 2], $sessionItemArray);
    }
}
