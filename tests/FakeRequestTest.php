<?php

use BlueMvc\Fakes\FakeRequest;

/**
 * Test FakeRequest class.
 */
class FakeRequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test empty constructor.
     */
    public function testEmptyConstructor()
    {
        $fakeRequest = new FakeRequest();

        $this->assertSame('http://localhost/', $fakeRequest->getUrl()->__toString());
        $this->assertSame('GET', $fakeRequest->getMethod()->__toString());
    }

    /**
     * Test default constructor.
     */
    public function testDefaultConstructor()
    {
        $fakeRequest = new FakeRequest('http://domain.com/foo/bar');

        $this->assertSame('http://domain.com/foo/bar', $fakeRequest->getUrl()->__toString());
        $this->assertSame('GET', $fakeRequest->getMethod()->__toString());
    }

    /**
     * Test default constructor with relative path.
     */
    public function testDefaultConstructorWithRelativePath()
    {
        $fakeRequest = new FakeRequest('/bar/baz');

        $this->assertSame('http://localhost/bar/baz', $fakeRequest->getUrl()->__toString());
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
     * @expectedException \DataTypes\Exceptions\UrlPathLogicException
     * @expectedExceptionMessage Url path "/" can not be combined with url path "../foo": Absolute path is above root level.
     */
    public function testConstructorWithInvalidUrl()
    {
        new FakeRequest('../foo');
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

    /**
     * Test getHeaders method.
     */
    public function testGetHeaders()
    {
        $fakeRequest = new FakeRequest('http://localhost:81/foo/bar');

        $this->assertSame(['Host' => 'localhost:81'], iterator_to_array($fakeRequest->getHeaders()));
    }

    /**
     * Test setHeader method.
     */
    public function testSetHeader()
    {
        $fakeRequest = new FakeRequest('http://localhost:81/foo/bar');
        $fakeRequest->setHeader('Host', 'foo.com');
        $fakeRequest->setHeader('Accept-Language', 'en');

        $this->assertSame(['Host' => 'foo.com', 'Accept-Language' => 'en'], iterator_to_array($fakeRequest->getHeaders()));
    }

    /**
     * Test addHeader method.
     */
    public function testAddHeader()
    {
        $fakeRequest = new FakeRequest('http://localhost:81/foo/bar');
        $fakeRequest->addHeader('Accept-Language', 'sv');
        $fakeRequest->addHeader('Accept-Language', 'en');

        $this->assertSame(['Host' => 'localhost:81', 'Accept-Language' => 'sv, en'], iterator_to_array($fakeRequest->getHeaders()));
    }

    /**
     * Test getHeader method.
     */
    public function testGetHeader()
    {
        $fakeRequest = new FakeRequest('http://localhost:81/foo/bar');

        $this->assertSame('localhost:81', $fakeRequest->getHeader('Host'));
        $this->assertNull($fakeRequest->getHeader('Accept-Language'));
    }

    /**
     * Test getUserAgent method without user agent set.
     */
    public function testGetUserAgentWithoutUserAgentSet()
    {
        $fakeRequest = new FakeRequest();

        $this->assertSame('', $fakeRequest->getUserAgent());
    }

    /**
     * Test getUserAgent method with user agent set.
     */
    public function testGetUserAgentWithUserAgentSet()
    {
        $fakeRequest = new FakeRequest();
        $fakeRequest->setHeader('User-Agent', 'FakeUserAgent/1.0');

        $this->assertSame('FakeUserAgent/1.0', $fakeRequest->getUserAgent());
    }

    /**
     * Test getFormParameters method.
     */
    public function testGetFormParameters()
    {
        $fakeRequest = new FakeRequest();

        $this->assertSame([], iterator_to_array($fakeRequest->getFormParameters()));
    }
}
