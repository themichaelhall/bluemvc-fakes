<?php

namespace BlueMvc\Fakes\Tests;

use BlueMvc\Core\Collections\ParameterCollection;
use BlueMvc\Core\Collections\UploadedFileCollection;
use BlueMvc\Core\UploadedFile;
use BlueMvc\Fakes\FakeRequest;
use DataTypes\FilePath;

/**
 * Test FakeRequest class.
 */
class FakeRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test empty constructor.
     */
    public function testEmptyConstructor()
    {
        $fakeRequest = new FakeRequest();

        self::assertSame('http://localhost/', $fakeRequest->getUrl()->__toString());
        self::assertSame('GET', $fakeRequest->getMethod()->__toString());
    }

    /**
     * Test default constructor.
     */
    public function testDefaultConstructor()
    {
        $fakeRequest = new FakeRequest('http://domain.com/foo/bar');

        self::assertSame('http://domain.com/foo/bar', $fakeRequest->getUrl()->__toString());
        self::assertSame('GET', $fakeRequest->getMethod()->__toString());
    }

    /**
     * Test default constructor with relative path.
     */
    public function testDefaultConstructorWithRelativePath()
    {
        $fakeRequest = new FakeRequest('/bar/baz');

        self::assertSame('http://localhost/bar/baz', $fakeRequest->getUrl()->__toString());
        self::assertSame('GET', $fakeRequest->getMethod()->__toString());
    }

    /**
     * Test constructor with method.
     */
    public function testConstructorWithMethod()
    {
        $fakeRequest = new FakeRequest('http://localhost/foo/bar', 'POST');

        self::assertSame('http://localhost/foo/bar', $fakeRequest->getUrl()->__toString());
        self::assertSame('POST', $fakeRequest->getMethod()->__toString());
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
     * @expectedException \BlueMvc\Core\Exceptions\Http\InvalidMethodNameException
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

        self::assertSame(['Host' => 'localhost:81'], iterator_to_array($fakeRequest->getHeaders()));
    }

    /**
     * Test setHeader method.
     */
    public function testSetHeader()
    {
        $fakeRequest = new FakeRequest('http://localhost:81/foo/bar');
        $fakeRequest->setHeader('Host', 'foo.com');
        $fakeRequest->setHeader('Accept-Language', 'en');

        self::assertSame(['Host' => 'foo.com', 'Accept-Language' => 'en'], iterator_to_array($fakeRequest->getHeaders()));
    }

    /**
     * Test addHeader method.
     */
    public function testAddHeader()
    {
        $fakeRequest = new FakeRequest('http://localhost:81/foo/bar');
        $fakeRequest->addHeader('Accept-Language', 'sv');
        $fakeRequest->addHeader('Accept-Language', 'en');

        self::assertSame(['Host' => 'localhost:81', 'Accept-Language' => 'sv, en'], iterator_to_array($fakeRequest->getHeaders()));
    }

    /**
     * Test getHeader method.
     */
    public function testGetHeader()
    {
        $fakeRequest = new FakeRequest('http://localhost:81/foo/bar');

        self::assertSame('localhost:81', $fakeRequest->getHeader('Host'));
        self::assertNull($fakeRequest->getHeader('Accept-Language'));
    }

    /**
     * Test getUserAgent method without user agent set.
     */
    public function testGetUserAgentWithoutUserAgentSet()
    {
        $fakeRequest = new FakeRequest();

        self::assertSame('', $fakeRequest->getUserAgent());
    }

    /**
     * Test getUserAgent method with user agent set.
     */
    public function testGetUserAgentWithUserAgentSet()
    {
        $fakeRequest = new FakeRequest();
        $fakeRequest->setHeader('User-Agent', 'FakeUserAgent/1.0');

        self::assertSame('FakeUserAgent/1.0', $fakeRequest->getUserAgent());
    }

    /**
     * Test getFormParameters method.
     */
    public function testGetFormParameters()
    {
        $fakeRequest = new FakeRequest();

        self::assertSame([], iterator_to_array($fakeRequest->getFormParameters()));
    }

    /**
     * Test setFormParameters method.
     */
    public function testSetFormParameters()
    {
        $fakeRequest = new FakeRequest();
        $formParameters = new ParameterCollection();
        $formParameters->set('Foo', 'Bar');
        $fakeRequest->setFormParameters($formParameters);

        self::assertSame(['Foo' => 'Bar'], iterator_to_array($fakeRequest->getFormParameters()));
    }

    /**
     * Test getFormParameter method.
     */
    public function testGetFormParameter()
    {
        $fakeRequest = new FakeRequest();
        $formParameters = new ParameterCollection();
        $formParameters->set('Foo', 'Bar');
        $fakeRequest->setFormParameters($formParameters);

        self::assertSame('Bar', $fakeRequest->getFormParameter('Foo'));
        self::assertNull($fakeRequest->getFormParameter('Bar'));
    }

    /**
     * Test setFormParameter method.
     */
    public function testSetFormParameter()
    {
        $fakeRequest = new FakeRequest();
        $fakeRequest->setFormParameter('Foo', 'Bar');

        self::assertSame(['Foo' => 'Bar'], iterator_to_array($fakeRequest->getFormParameters()));
    }

    /**
     * Test getQueryParameters method without query parameters set.
     */
    public function testGetQueryParametersWithoutQueryParametersSet()
    {
        $fakeRequest = new FakeRequest();

        self::assertSame([], iterator_to_array($fakeRequest->getQueryParameters()));
    }

    /**
     * Test getQueryParameters method with query parameters set.
     */
    public function testGetQueryParametersWithQueryParametersSet()
    {
        $fakeRequest = new FakeRequest('http://localhost/?foo=1&bar[]=2&bar[]=3');

        self::assertSame(['foo' => '1', 'bar' => '2'], iterator_to_array($fakeRequest->getQueryParameters()));
    }

    /**
     * Test getQueryParameter method.
     */
    public function testGetQueryParameter()
    {
        $fakeRequest = new FakeRequest('http://localhost/?foo=1&bar=2&bar=3');

        self::assertSame('1', $fakeRequest->getQueryParameter('foo'));
        self::assertSame('3', $fakeRequest->getQueryParameter('bar'));
        self::assertNull($fakeRequest->getQueryParameter('baz'));
    }

    /**
     * Test getUploadedFiles method without uploaded files set.
     */
    public function testGetUploadedFilesWithoutUploadedFilesSet()
    {
        $fakeRequest = new FakeRequest('/', 'POST');

        self::assertSame([], iterator_to_array($fakeRequest->getUploadedFiles()));
    }

    /**
     * Test setUploadedFiles method.
     */
    public function testGetUploadedFilesWithUploadedFilesSet()
    {
        $fakeRequest = new FakeRequest('/', 'POST');
        $file = new UploadedFile(FilePath::parse('/tmp/foo'), 'Foo', 10000);
        $uploadedFiles = new UploadedFileCollection();
        $uploadedFiles->set('file', $file);
        $fakeRequest->setUploadedFiles($uploadedFiles);

        self::assertSame(['file' => $file], iterator_to_array($fakeRequest->getUploadedFiles()));
    }
}
