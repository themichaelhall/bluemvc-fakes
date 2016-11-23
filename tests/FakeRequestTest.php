<?php

use BlueMvc\Fakes\FakeRequest;

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
        $fakeRequest = new FakeRequest('http://localhost/foo/bar');

        $this->assertSame('http://localhost/foo/bar', $fakeRequest->getUrl()->__toString());
        $this->assertSame('GET', $fakeRequest->getMethod()->__toString());
    }

    /**
     * Test constructor with method.
     */
    public function testConstructorWithMethod()
    {
        $fakeRequest = new FakeRequest('http://localhost/foo/bar', 'POST');

        $this->assertSame('http://localhost/foo/bar', $fakeRequest->getUrl()->__toString());
        $this->assertSame('POST', $fakeRequest->getMethod()->__toString());
    }

    /**
     * Test constructor with invalid url.
     *
     * @expectedException \DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "FooBar" is invalid: Scheme is missing.
     */
    public function testConstructorWithInvalidUrl()
    {
        new FakeRequest('FooBar');
    }

    /**
     * Test constructor with invalid url parameter type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $url parameter is not a string.
     */
    public function testConstructorWithInvalidUrlParameterType()
    {
        new FakeRequest(1000);
    }

    /**
     * Test constructor with invalid method.
     *
     * @expectedException BlueMvc\Core\Exceptions\Http\InvalidMethodNameException
     * @expectedExceptionMessage Method "(FOO)" contains invalid character "(".
     */
    public function testConstructorWithInvalidMethod()
    {
        new FakeRequest('http://localhost/foo/bar', '(FOO)');
    }

    /**
     * Test constructor with invalid method parameter type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $method parameter is not a string.
     */
    public function testConstructorWithInvalidMethodParameterType()
    {
        new FakeRequest('http://localhost/foo/bar', false);
    }
}
