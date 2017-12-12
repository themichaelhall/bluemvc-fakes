<?php

namespace BlueMvc\Fakes\Tests;

use BlueMvc\Core\Collections\HeaderCollection;
use BlueMvc\Core\Collections\ParameterCollection;
use BlueMvc\Core\Collections\RequestCookieCollection;
use BlueMvc\Core\RequestCookie;
use BlueMvc\Fakes\Exceptions\InvalidUploadedFileException;
use BlueMvc\Fakes\FakeRequest;

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
     * Test setHeaders method.
     */
    public function testSetHeaders()
    {
        $fakeRequest = new FakeRequest('http://localhost/foo/bar');
        $headers = new HeaderCollection();
        $headers->set('Host', 'example.com');
        $headers->set('User-Agent', 'FakeUserAgent/1.0');
        $fakeRequest->setHeaders($headers);

        self::assertSame(['Host' => 'example.com', 'User-Agent' => 'FakeUserAgent/1.0'], iterator_to_array($fakeRequest->getHeaders()));
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
     * Test uploadFile method.
     */
    public function testUploadFile()
    {
        $DS = DIRECTORY_SEPARATOR;

        $uploadedFilePath = __DIR__ . $DS . 'Helpers' . $DS . 'Files' . $DS . 'helloworld.txt';
        $fakeRequest = new FakeRequest('/', 'POST');
        $fakeRequest->uploadFile('foo', $uploadedFilePath);
        $uploadedFile = $fakeRequest->getUploadedFile('foo');

        self::assertSame(['foo' => $uploadedFile], iterator_to_array($fakeRequest->getUploadedFiles()));
        self::assertNotSame($uploadedFilePath, $uploadedFile->getPath()->__toString());
        self::assertFileExists($uploadedFile->getPath()->__toString());
        self::assertSame('Hello World!', file_get_contents($uploadedFile->getPath()->__toString()));
        self::assertSame($uploadedFilePath, $uploadedFile->getOriginalName());
        self::assertSame(12, $uploadedFile->getSize());
    }

    /**
     * Test uploadFile method with invalid name parameter type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $name parameter is not a string.
     */
    public function testUploadFileWithInvalidNameParameterType()
    {
        $uploadedFilePath = __DIR__ . '/Helpers/Files/helloworld.txt';
        $fakeRequest = new FakeRequest('/', 'POST');
        $fakeRequest->uploadFile(false, $uploadedFilePath);
    }

    /**
     * Test uploadFile method with invalid filename parameter type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $filename parameter is not a string.
     */
    public function testUploadFileWithInvalidFilenameParameterType()
    {
        $fakeRequest = new FakeRequest('/', 'POST');
        $fakeRequest->uploadFile('foo', false);
    }

    /**
     * Test uploadFile method with invalid filename path.
     */
    public function testUploadFileWithInvalidFilenamePath()
    {
        $DS = DIRECTORY_SEPARATOR;

        $fakeRequest = new FakeRequest('/', 'POST');
        $exceptionMessage = null;

        try {
            $fakeRequest->uploadFile('foo', __DIR__ . $DS . 'Helpers' . $DS . 'Files' . $DS . 'foo.dat');
        } catch (InvalidUploadedFileException $exception) {
            $exceptionMessage = $exception->getMessage();
        }

        self::assertSame('File "' . __DIR__ . $DS . 'Helpers' . $DS . 'Files' . $DS . 'foo.dat" does not exist.', $exceptionMessage);
    }

    /**
     * Test getCookies method.
     */
    public function testGetCookies()
    {
        $fakeRequest = new FakeRequest();

        self::assertSame([], iterator_to_array($fakeRequest->getCookies()));
    }

    /**
     * Test getCookies method.
     */
    public function testSetCookies()
    {
        $fooCookie = new RequestCookie('1');
        $barCookie = new RequestCookie('2');

        $cookies = new RequestCookieCollection();
        $cookies->set('foo', $fooCookie);
        $cookies->set('bar', $barCookie);

        $fakeRequest = new FakeRequest();
        $fakeRequest->setCookies($cookies);

        self::assertSame(['foo' => $fooCookie, 'bar' => $barCookie], iterator_to_array($fakeRequest->getCookies()));
    }

    /**
     * Test getCookie method.
     */
    public function testGetCookie()
    {
        $fooCookie = new RequestCookie('1');
        $barCookie = new RequestCookie('2');

        $cookies = new RequestCookieCollection();
        $cookies->set('foo', $fooCookie);
        $cookies->set('bar', $barCookie);

        $fakeRequest = new FakeRequest();
        $fakeRequest->setCookies($cookies);

        self::assertSame($fooCookie, $fakeRequest->getCookie('foo'));
        self::assertSame($barCookie, $fakeRequest->getCookie('bar'));
        self::assertNull($fakeRequest->getCookie('Bar'));
        self::assertNull($fakeRequest->getCookie('baz'));
    }

    /**
     * Test setCookie method.
     */
    public function testSetCookie()
    {
        $fooCookie = new RequestCookie('1');
        $barCookie = new RequestCookie('2');

        $fakeRequest = new FakeRequest();
        $fakeRequest->setCookie('foo', $fooCookie);
        $fakeRequest->setCookie('bar', $barCookie);

        self::assertSame(['foo' => $fooCookie, 'bar' => $barCookie], iterator_to_array($fakeRequest->getCookies()));
    }

    /**
     * Test getRawContent method.
     */
    public function testGetRawContent()
    {
        $fakeRequest = new FakeRequest();

        self::assertSame('', $fakeRequest->getRawContent());
    }

    /**
     * Test setRawContent method.
     */
    public function testSetRawContent()
    {
        $fakeRequest = new FakeRequest();
        $fakeRequest->setRawContent('Foo&Bar');

        self::assertSame('Foo&Bar', $fakeRequest->getRawContent());
    }

    /**
     * Test setRawContent method with invalid content parameter type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $content parameter is not a string.
     */
    public function testSetRawContentWithInvalidContentParameterType()
    {
        $fakeRequest = new FakeRequest();

        $fakeRequest->setRawContent(0);
    }
}
