<?php

use BlueMvc\Core\Http\StatusCode;
use BlueMvc\Fakes\FakeRequest;
use BlueMvc\Fakes\FakeResponse;

/**
 * Test FakeResponse class.
 */
class FakeResponseTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test getRequest method.
     */
    public function testGetRequest()
    {
        $request = new FakeRequest('http://domain.com/');
        $response = new FakeResponse($request);

        $this->assertSame($request, $response->getRequest());
    }

    /**
     * Test getContent method.
     */
    public function testGetContent()
    {
        $request = new FakeRequest('http://domain.com/');
        $response = new FakeResponse($request);

        $this->assertSame('', $response->getContent());
    }

    /**
     * Test setContent method.
     */
    public function testSetContent()
    {
        $request = new FakeRequest('http://domain.com/');
        $response = new FakeResponse($request);
        $response->setContent('Hello world!');

        $this->assertSame('Hello world!', $response->getContent());
    }

    /**
     * Test getStatusCode method.
     */
    public function testGetStatusCode()
    {
        $request = new FakeRequest('http://domain.com/');
        $response = new FakeResponse($request);

        $this->assertSame(StatusCode::OK, $response->getStatusCode()->getCode());
    }

    /**
     * Test setStatusCode method.
     */
    public function testSetStatusCode()
    {
        $request = new FakeRequest('http://domain.com/');
        $response = new FakeResponse($request);
        $response->setStatusCode(new StatusCode(StatusCode::INTERNAL_SERVER_ERROR));

        $this->assertSame(StatusCode::INTERNAL_SERVER_ERROR, $response->getStatusCode()->getCode());
    }

    /**
     * Test get headers for response with no additional headers.
     */
    public function testGetHeadersForResponseWithNoAdditionalHeaders()
    {
        $request = new FakeRequest('http://localhost/');
        $response = new FakeResponse($request);

        $this->assertSame([], iterator_to_array($response->getHeaders()));
    }

    /**
     * Test get headers for response with additional headers.
     */
    public function testGetHeadersForResponseWithAdditionalHeaders()
    {
        $request = new FakeRequest('http://localhost/');
        $response = new FakeResponse($request);
        $response->setHeader('Content-Type', 'text/plain');

        $this->assertSame(['Content-Type' => 'text/plain'], iterator_to_array($response->getHeaders()));
    }

    /**
     * Test getHeader method.
     */
    public function testGetHeader()
    {
        $request = new FakeRequest('http://localhost/');
        $response = new FakeResponse($request);
        $response->setHeader('Content-Type', 'text/plain');

        $this->assertSame('text/plain', $response->getHeader('content-type'));
        $this->assertNull($response->getHeader('Location'));
    }

    /**
     * Test addHeader method.
     */
    public function testAddHeader()
    {
        $request = new FakeRequest('http://localhost/');
        $response = new FakeResponse($request);
        $response->setHeader('allow', 'GET');
        $response->addHeader('Allow', 'POST');

        $this->assertSame(['Allow' => 'GET, POST'], iterator_to_array($response->getHeaders()));
    }
}
