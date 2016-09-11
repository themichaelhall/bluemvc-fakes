<?php

use BlueMvc\Core\Http\Method;
use BlueMvc\Fakes\FakeRequest;
use DataTypes\Url;

/**
 * Test FakeRequest class.
 */
class FakeRequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test default constructor.
     */
    public function testDefaultConstructor()
    {
        $fakeRequest = new FakeRequest(Url::parse('http://localhost/foo/bar'));

        $this->assertSame('http://localhost/foo/bar', $fakeRequest->getUrl()->__toString());
        $this->assertSame('GET', $fakeRequest->getMethod()->__toString());
    }

    /**
     * Test constructor with method.
     */
    public function testConstructorWithMethod()
    {
        $fakeRequest = new FakeRequest(Url::parse('http://localhost/foo/bar'), new Method('POST'));

        $this->assertSame('http://localhost/foo/bar', $fakeRequest->getUrl()->__toString());
        $this->assertSame('POST', $fakeRequest->getMethod()->__toString());
    }
}
