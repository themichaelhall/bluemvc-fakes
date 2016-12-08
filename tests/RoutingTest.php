<?php

use BlueMvc\Core\Http\StatusCode;
use BlueMvc\Core\Route;
use BlueMvc\Fakes\FakeApplication;
use BlueMvc\Fakes\FakeRequest;
use BlueMvc\Fakes\FakeResponse;
use DataTypes\FilePath;

require_once __DIR__ . '/Helpers/TestController.php';
require_once __DIR__ . '/Helpers/TestViewRenderer.php';

/**
 * Test basic routing using FakeApplication class.
 */
class RoutingTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test get index page.
     */
    public function testGetIndexPage()
    {
        $request = new FakeRequest('http://localhost/');
        $response = new FakeResponse($request);
        $this->application->run($request, $response);

        $this->assertSame('Hello World!', $response->getContent());
        $this->assertSame(StatusCode::OK, $response->getStatusCode()->getCode());
    }

    /**
     * Test get view page.
     */
    public function testGetViewPage()
    {
        $request = new FakeRequest('http://localhost/view');
        $response = new FakeResponse($request);
        $this->application->run($request, $response);

        $this->assertSame('ViewData=Bar, Model=Baz', $response->getContent());
        $this->assertSame(StatusCode::OK, $response->getStatusCode()->getCode());
    }

    /**
     * Test get action result page.
     */
    public function testGetActionResultPage()
    {
        $request = new FakeRequest('http://localhost/actionResult');
        $response = new FakeResponse($request);
        $this->application->run($request, $response);

        $this->assertSame('', $response->getContent());
        $this->assertSame(StatusCode::MOVED_PERMANENTLY, $response->getStatusCode()->getCode());
        $this->assertSame('https://localhost/', $response->getHeader('Location'));
    }

    /**
     * Set up.
     */
    public function setUp()
    {
        $this->application = new FakeApplication();
        $this->application->setViewPath(FilePath::parse(__DIR__ . '/Helpers/Views/'));
        $this->application->addViewRenderer(new TestViewRenderer());
        $this->application->addRoute(new Route('', TestController::class));
    }

    /**
     * Tear down.
     */
    public function tearDown()
    {
        $this->application = null;
    }

    /**
     * @var FakeApplication My application.
     */
    private $application;
}
