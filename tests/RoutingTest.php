<?php

use BlueMvc\Core\Http\StatusCode;
use BlueMvc\Core\Route;
use BlueMvc\Fakes\FakeApplication;
use BlueMvc\Fakes\FakeRequest;
use BlueMvc\Fakes\FakeResponse;
use BlueMvc\Fakes\Tests\Helpers\TestViewRenderer;
use DataTypes\FilePath;

require_once __DIR__ . '/Helpers/TestController.php';

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

        $this->assertSame('ViewData=Bar, Model=Baz, Url=http://localhost/view, TempDir=' . $this->application->getTempPath(), $response->getContent());
        $this->assertSame(StatusCode::OK, $response->getStatusCode()->getCode());
    }

    /**
     * Test get custom view page.
     */
    public function testGetCustomViewPage()
    {
        $request = new FakeRequest('http://localhost/customView');
        $response = new FakeResponse($request);
        $this->application->run($request, $response);

        $this->assertSame('Custom view, ViewData=Bar, Model=Baz, Url=http://localhost/customView, TempDir=' . $this->application->getTempPath(), $response->getContent());
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
     * Test get json result page.
     */
    public function testGetJsonResultPage()
    {
        $request = new FakeRequest('http://localhost/jsonResult');
        $response = new FakeResponse($request);
        $this->application->run($request, $response);

        $this->assertSame('{"Foo":"Bar"}', $response->getContent());
        $this->assertSame(StatusCode::OK, $response->getStatusCode()->getCode());
        $this->assertSame('application/json', $response->getHeader('Content-Type'));
    }

    /**
     * Test pre-action event.
     */
    public function testPreActionEvent()
    {
        $request = new FakeRequest('http://localhost/');
        $request->setHeader('X-Trigger-PreActionEvent', 'Yes');
        $response = new FakeResponse($request);
        $this->application->run($request, $response);

        $this->assertSame('Pre-action event triggered.', $response->getContent());
        $this->assertSame(StatusCode::OK, $response->getStatusCode()->getCode());
    }

    /**
     * Test post-action event.
     */
    public function testPostActionEvent()
    {
        $request = new FakeRequest('http://localhost/');
        $request->setHeader('X-Trigger-PostActionEvent', 'Yes');
        $response = new FakeResponse($request);
        $this->application->run($request, $response);

        $this->assertSame('Post-action event triggered.', $response->getContent());
        $this->assertSame(StatusCode::FORBIDDEN, $response->getStatusCode()->getCode());
    }

    /**
     * Test failure with exception for non-debug application.
     */
    public function testExceptionForNonDebugApplication()
    {
        $request = new FakeRequest('http://localhost/exception');
        $response = new FakeResponse($request);
        $this->application->run($request, $response);

        $this->assertSame('', $response->getContent());
        $this->assertSame(StatusCode::INTERNAL_SERVER_ERROR, $response->getStatusCode()->getCode());
    }

    /**
     * Test failure with exception for debug application.
     */
    public function testExceptionForDebugApplication()
    {
        $this->application->setDebug(true);
        $request = new FakeRequest('http://localhost/exception');
        $response = new FakeResponse($request);
        $this->application->run($request, $response);

        $this->assertContains('<h1>Throwing exception!</h1>', $response->getContent());
        $this->assertContains('<code>DomainException</code>', $response->getContent());
        $this->assertSame(StatusCode::INTERNAL_SERVER_ERROR, $response->getStatusCode()->getCode());
    }

    /**
     * Test request using error controller.
     */
    public function testErrorController()
    {
        $this->application->setErrorControllerClass(TestErrorController::class);
        $request = new FakeRequest('http://localhost/exception');
        $response = new FakeResponse($request);
        $this->application->run($request, $response);

        $this->assertSame('StatusCode=500', $response->getContent());
        $this->assertSame(StatusCode::INTERNAL_SERVER_ERROR, $response->getStatusCode()->getCode());
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
