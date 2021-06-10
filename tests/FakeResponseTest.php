<?php

declare(strict_types=1);

namespace BlueMvc\Fakes\Tests;

use BlueMvc\Core\Collections\HeaderCollection;
use BlueMvc\Core\Collections\ResponseCookieCollection;
use BlueMvc\Core\Http\StatusCode;
use BlueMvc\Core\ResponseCookie;
use BlueMvc\Fakes\FakeResponse;
use DataTypes\Net\Host;
use DataTypes\Net\UrlPath;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

/**
 * Test FakeResponse class.
 */
class FakeResponseTest extends TestCase
{
    /**
     * Test getContent method.
     */
    public function testGetContent()
    {
        $response = new FakeResponse();

        self::assertSame('', $response->getContent());
    }

    /**
     * Test setContent method.
     */
    public function testSetContent()
    {
        $response = new FakeResponse();
        $response->setContent('Hello world!');

        self::assertSame('Hello world!', $response->getContent());
    }

    /**
     * Test getStatusCode method.
     */
    public function testGetStatusCode()
    {
        $response = new FakeResponse();

        self::assertSame(StatusCode::OK, $response->getStatusCode()->getCode());
    }

    /**
     * Test setStatusCode method.
     */
    public function testSetStatusCode()
    {
        $response = new FakeResponse();
        $response->setStatusCode(new StatusCode(StatusCode::INTERNAL_SERVER_ERROR));

        self::assertSame(StatusCode::INTERNAL_SERVER_ERROR, $response->getStatusCode()->getCode());
    }

    /**
     * Test get headers for response with no additional headers.
     */
    public function testGetHeadersForResponseWithNoAdditionalHeaders()
    {
        $response = new FakeResponse();

        self::assertSame([], iterator_to_array($response->getHeaders()));
    }

    /**
     * Test get headers for response with additional headers.
     */
    public function testGetHeadersForResponseWithAdditionalHeaders()
    {
        $response = new FakeResponse();
        $response->setHeader('Content-Type', 'text/plain');

        self::assertSame(['Content-Type' => 'text/plain'], iterator_to_array($response->getHeaders()));
    }

    /**
     * Test getHeader method.
     */
    public function testGetHeader()
    {
        $response = new FakeResponse();
        $response->setHeader('Content-Type', 'text/plain');

        self::assertSame('text/plain', $response->getHeader('content-type'));
        self::assertNull($response->getHeader('Location'));
    }

    /**
     * Test setHeader method.
     */
    public function testSetHeader()
    {
        $response = new FakeResponse();
        $response->setHeader('allow', 'GET');
        $response->setHeader('Allow', 'POST');

        self::assertSame(['Allow' => 'POST'], iterator_to_array($response->getHeaders()));
    }

    /**
     * Test addHeader method.
     */
    public function testAddHeader()
    {
        $response = new FakeResponse();
        $response->setHeader('allow', 'GET');
        $response->addHeader('Allow', 'POST');

        self::assertSame(['Allow' => 'GET, POST'], iterator_to_array($response->getHeaders()));
    }

    /**
     * Test setHeaders method.
     */
    public function testSetHeaders()
    {
        $response = new FakeResponse();
        $headers = new HeaderCollection();
        $headers->set('Content-Type', 'text/plain');
        $headers->set('Cache-Control', 'private');
        $response->setHeaders($headers);

        self::assertSame(['Content-Type' => 'text/plain', 'Cache-Control' => 'private'], iterator_to_array($response->getHeaders()));
    }

    /**
     * Test setExpiry method.
     */
    public function testSetExpiry()
    {
        $response = new FakeResponse();
        $expiry = (new DateTimeImmutable())->add(new DateInterval('PT24H'));
        $response->setExpiry($expiry);

        self::assertSame($expiry->setTimezone(new DateTimeZone('UTC'))->format('D, d M Y H:i:s \G\M\T'), $response->getHeader('Expires'));
        self::assertSame('public, max-age=86400', $response->getHeader('Cache-Control'));
    }

    /**
     * Test getCookies method.
     */
    public function testGetCookies()
    {
        $response = new FakeResponse();

        self::assertSame([], iterator_to_array($response->getCookies()));
    }

    /**
     * Test setCookies method.
     */
    public function testSetCookies()
    {
        $response = new FakeResponse();
        $cookies = new ResponseCookieCollection();
        $fooCookie = new ResponseCookie('aaa', new DateTimeImmutable(), UrlPath::parse('/baz/'), Host::parse('www.example.com'), true, true);
        $cookies->set('foo', $fooCookie);
        $barCookie = new ResponseCookie('bbb');
        $cookies->set('bar', $barCookie);
        $response->setCookies($cookies);

        self::assertSame(['foo' => $fooCookie, 'bar' => $barCookie], iterator_to_array($response->getCookies()));
    }

    /**
     * Test getCookie method.
     */
    public function testGetCookie()
    {
        $response = new FakeResponse();
        $cookies = new ResponseCookieCollection();
        $fooCookie = new ResponseCookie('aaa', new DateTimeImmutable(), UrlPath::parse('/baz/'), Host::parse('www.example.com'), true, true);
        $cookies->set('foo', $fooCookie);
        $response->setCookies($cookies);

        self::assertSame($fooCookie, $response->getCookie('foo'));
        self::assertNull($response->getCookie('Foo'));
        self::assertNull($response->getCookie('bar'));
    }

    /**
     * Test setCookie method.
     */
    public function testSetCookie()
    {
        $response = new FakeResponse();
        $fooCookie = new ResponseCookie('aaa', new DateTimeImmutable(), UrlPath::parse('/baz/'), Host::parse('www.example.com'), true, true);
        $response->setCookie('foo', $fooCookie);

        self::assertSame(['foo' => $fooCookie], iterator_to_array($response->getCookies()));
    }
}
