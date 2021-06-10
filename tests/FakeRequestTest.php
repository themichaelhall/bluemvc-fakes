<?php

declare(strict_types=1);

namespace BlueMvc\Fakes\Tests;

use BlueMvc\Core\Collections\HeaderCollection;
use BlueMvc\Core\Collections\ParameterCollection;
use BlueMvc\Core\Collections\RequestCookieCollection;
use BlueMvc\Core\Exceptions\Http\InvalidMethodNameException;
use BlueMvc\Core\Http\Method;
use BlueMvc\Core\RequestCookie;
use BlueMvc\Fakes\Collections\FakeSessionItemCollection;
use BlueMvc\Fakes\Exceptions\InvalidUploadedFileException;
use BlueMvc\Fakes\FakeRequest;
use DataTypes\Net\Exceptions\UrlPathLogicException;
use DataTypes\Net\IPAddress;
use DataTypes\Net\Url;
use PHPUnit\Framework\TestCase;

/**
 * Test FakeRequest class.
 */
class FakeRequestTest extends TestCase
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
     */
    public function testConstructorWithInvalidUrl()
    {
        self::expectException(UrlPathLogicException::class);
        self::expectExceptionMessage('Url path "/" can not be combined with url path "../foo": Absolute path is above root level.');

        new FakeRequest('../foo');
    }

    /**
     * Test constructor with invalid method.
     */
    public function testConstructorWithInvalidMethod()
    {
        self::expectException(InvalidMethodNameException::class);
        self::expectExceptionMessage('Method "(FOO)" contains invalid character "(".');

        new FakeRequest('http://localhost/foo/bar', '(FOO)');
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
        $uploadedFileContent = file_get_contents($uploadedFile->getPath()->__toString());

        self::assertSame(['foo' => $uploadedFile], iterator_to_array($fakeRequest->getUploadedFiles()));
        self::assertNotSame($uploadedFilePath, $uploadedFile->getPath()->__toString());
        self::assertFileExists($uploadedFile->getPath()->__toString());
        self::assertSame("Hello World!\n", self::normalizeEndOfLine($uploadedFileContent));
        self::assertSame($uploadedFilePath, $uploadedFile->getOriginalName());
        self::assertSame(strlen($uploadedFileContent), $uploadedFile->getSize());
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
     * Test that the method parameter is case insensitive.
     */
    public function testMethodParameterIsCaseInsensitive()
    {
        $fakeRequest = new FakeRequest('/', 'Get');

        self::assertTrue($fakeRequest->getMethod()->isGet());
        self::assertSame('GET', $fakeRequest->getMethod()->getName());
    }

    /**
     * Test getClientIp method.
     */
    public function testGetClientIp()
    {
        $fakeRequest = new FakeRequest();

        self::assertSame('127.0.0.1', $fakeRequest->getClientIp()->__toString());
    }

    /**
     * Test setClientIp method.
     */
    public function testSetClientIp()
    {
        $clientIp = IPAddress::parse('192.168.1.1');

        $fakeRequest = new FakeRequest();
        $fakeRequest->setClientIp($clientIp);

        self::assertSame($clientIp, $fakeRequest->getClientIp());
    }

    /**
     * Test getSessionItems method.
     */
    public function testGetSessionItems()
    {
        $fakeRequest = new FakeRequest();

        self::assertSame([], iterator_to_array($fakeRequest->getSessionItems()));
    }

    /**
     * Test setSessionItem method.
     */
    public function testSetSessionItem()
    {
        $fakeRequest = new FakeRequest();
        $fakeRequest->setSessionItem('Foo', ['Bar', 'Baz']);
        $fakeRequest->setSessionItem('Bar', 12345);

        self::assertSame(['Foo' => ['Bar', 'Baz'], 'Bar' => 12345], iterator_to_array($fakeRequest->getSessionItems()));
    }

    /**
     * Test getSessionItem method.
     */
    public function testGetSessionItem()
    {
        $fakeRequest = new FakeRequest();
        $fakeRequest->setSessionItem('Foo', 1);
        $fakeRequest->setSessionItem('Bar', 2);

        self::assertSame(1, $fakeRequest->getSessionItem('Foo'));
        self::assertSame(2, $fakeRequest->getSessionItem('Bar'));
        self::assertNull($fakeRequest->getSessionItem('Baz'));
        self::assertNull($fakeRequest->getSessionItem('foo'));
    }

    /**
     * Test removeSessionItem method.
     */
    public function testRemoveSessionItem()
    {
        $fakeRequest = new FakeRequest();
        $fakeRequest->setSessionItem('Foo', 1);
        $fakeRequest->setSessionItem('Bar', 2);
        $fakeRequest->removeSessionItem('Foo');
        $fakeRequest->removeSessionItem('Baz');

        self::assertNull($fakeRequest->getSessionItem('Foo'));
        self::assertSame(2, $fakeRequest->getSessionItem('Bar'));
        self::assertNull($fakeRequest->getSessionItem('Baz'));
    }

    /**
     * Test setSessionItems method.
     */
    public function testSetSessionItems()
    {
        $sessionItems = new FakeSessionItemCollection();
        $sessionItems->set('Foo', 1);
        $sessionItems->set('Bar', 2);

        $fakeRequest = new FakeRequest();
        $fakeRequest->setSessionItems($sessionItems);

        self::assertSame(['Foo' => 1, 'Bar' => 2], iterator_to_array($fakeRequest->getSessionItems()));
    }

    /**
     * Test setUrl method.
     */
    public function testSetUrl()
    {
        $fakeRequest = new FakeRequest();
        $fakeRequest->setUrl(Url::parse('https://example.com:8080/foo/bar'));

        self::assertSame('https://example.com:8080/foo/bar', $fakeRequest->getUrl()->__toString());
        self::assertSame('GET', $fakeRequest->getMethod()->__toString());
        self::assertSame(['Host' => 'example.com:8080'], iterator_to_array($fakeRequest->getHeaders()));
    }

    /**
     * Test setMethod method.
     */
    public function testSetMethod()
    {
        $fakeRequest = new FakeRequest();
        $fakeRequest->setMethod(new Method('PUT'));

        self::assertSame('http://localhost/', $fakeRequest->getUrl()->__toString());
        self::assertSame('PUT', $fakeRequest->getMethod()->__toString());
        self::assertSame(['Host' => 'localhost'], iterator_to_array($fakeRequest->getHeaders()));
    }

    /**
     * Normalizes the end of line character(s) to \n, so tests will pass, event if the newline(s) in tests files are converted, e.g. by Git.
     *
     * @param string $s
     *
     * @return string
     */
    private static function normalizeEndOfLine(string $s): string
    {
        return str_replace("\r\n", "\n", $s);
    }
}
