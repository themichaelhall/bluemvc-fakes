<?php

declare(strict_types=1);

namespace BlueMvc\Fakes\Tests;

use BlueMvc\Core\Http\StatusCode;
use BlueMvc\Core\Route;
use BlueMvc\Fakes\FakeApplication;
use BlueMvc\Fakes\FakeRequest;
use BlueMvc\Fakes\FakeResponse;
use BlueMvc\Fakes\Tests\Helpers\TestController;
use BlueMvc\Fakes\Tests\Helpers\TestErrorController;
use BlueMvc\Fakes\Tests\Helpers\TestViewRenderer;
use DataTypes\FilePath;
use PHPUnit\Framework\TestCase;

/**
 * Test basic routing using FakeApplication class.
 */
class RoutingTest extends TestCase
{
    /**
     * Test get index page.
     */
    public function testGetIndexPage()
    {
        $request = new FakeRequest('http://localhost/');
        $response = new FakeResponse();
        $this->application->run($request, $response);

        self::assertSame('Hello World!', $response->getContent());
        self::assertSame(StatusCode::OK, $response->getStatusCode()->getCode());
    }

    /**
     * Test get view page.
     */
    public function testGetViewPage()
    {
        $request = new FakeRequest('http://localhost/view');
        $response = new FakeResponse();
        $this->application->run($request, $response);

        self::assertSame('ViewData=Bar, Model=Baz, Url=http://localhost/view, TempDir=' . $this->application->getTempPath() . "\n", self::normalizeEndOfLine($response->getContent()));
        self::assertSame(StatusCode::OK, $response->getStatusCode()->getCode());
    }

    /**
     * Test get custom view page.
     */
    public function testGetCustomViewPage()
    {
        $request = new FakeRequest('http://localhost/customView');
        $response = new FakeResponse();
        $this->application->run($request, $response);

        self::assertSame('Custom view, ViewData=Bar, Model=Baz, Url=http://localhost/customView, TempDir=' . $this->application->getTempPath() . "\n", self::normalizeEndOfLine($response->getContent()));
        self::assertSame(StatusCode::OK, $response->getStatusCode()->getCode());
    }

    /**
     * Test get action result page.
     */
    public function testGetActionResultPage()
    {
        $request = new FakeRequest('http://localhost/actionResult');
        $response = new FakeResponse();
        $this->application->run($request, $response);

        self::assertSame('', $response->getContent());
        self::assertSame(StatusCode::MOVED_PERMANENTLY, $response->getStatusCode()->getCode());
        self::assertSame('https://localhost/', $response->getHeader('Location'));
    }

    /**
     * Test get json result page.
     */
    public function testGetJsonResultPage()
    {
        $request = new FakeRequest('http://localhost/jsonResult');
        $response = new FakeResponse();
        $this->application->run($request, $response);

        self::assertSame('{"Foo":"Bar"}', $response->getContent());
        self::assertSame(StatusCode::OK, $response->getStatusCode()->getCode());
        self::assertSame('application/json', $response->getHeader('Content-Type'));
    }

    /**
     * Test get scalar result page.
     */
    public function testGetScalarResultPage()
    {
        $request = new FakeRequest('http://localhost/scalar');
        $response = new FakeResponse();
        $this->application->run($request, $response);

        self::assertSame('12.5', $response->getContent());
        self::assertSame(StatusCode::OK, $response->getStatusCode()->getCode());
    }

    /**
     * Test get null result page.
     */
    public function testGetNullResultPage()
    {
        $request = new FakeRequest('http://localhost/null');
        $response = new FakeResponse();
        $this->application->run($request, $response);

        self::assertSame('', $response->getContent());
        self::assertSame(StatusCode::OK, $response->getStatusCode()->getCode());
    }

    /**
     * Test pre-action event.
     */
    public function testPreActionEvent()
    {
        $request = new FakeRequest('http://localhost/');
        $request->setHeader('X-Trigger-PreActionEvent', 'Yes');
        $response = new FakeResponse();
        $this->application->run($request, $response);

        self::assertSame('Pre-action event triggered.', $response->getContent());
        self::assertSame(StatusCode::OK, $response->getStatusCode()->getCode());
    }

    /**
     * Test post-action event.
     */
    public function testPostActionEvent()
    {
        $request = new FakeRequest('http://localhost/');
        $request->setHeader('X-Trigger-PostActionEvent', 'Yes');
        $response = new FakeResponse();
        $this->application->run($request, $response);

        self::assertSame('Post-action event triggered.', $response->getContent());
        self::assertSame(StatusCode::FORBIDDEN, $response->getStatusCode()->getCode());
    }

    /**
     * Test failure with exception for non-debug application.
     */
    public function testExceptionForNonDebugApplication()
    {
        $request = new FakeRequest('http://localhost/exception');
        $response = new FakeResponse();
        $this->application->run($request, $response);

        self::assertSame('', $response->getContent());
        self::assertSame(StatusCode::INTERNAL_SERVER_ERROR, $response->getStatusCode()->getCode());
    }

    /**
     * Test failure with exception for debug application.
     */
    public function testExceptionForDebugApplication()
    {
        $this->application->setDebug(true);
        $request = new FakeRequest('http://localhost/exception');
        $response = new FakeResponse();
        $this->application->run($request, $response);

        self::assertStringContainsString('<h1>Throwing exception!</h1>', $response->getContent());
        self::assertStringContainsString('<code>DomainException</code>', $response->getContent());
        self::assertSame(StatusCode::INTERNAL_SERVER_ERROR, $response->getStatusCode()->getCode());
    }

    /**
     * Test request using error controller.
     */
    public function testErrorController()
    {
        $this->application->setErrorControllerClass(TestErrorController::class);
        $request = new FakeRequest('http://localhost/exception');
        $response = new FakeResponse();
        $this->application->run($request, $response);

        self::assertSame("StatusCode=500\n", self::normalizeEndOfLine($response->getContent()));
        self::assertSame(StatusCode::INTERNAL_SERVER_ERROR, $response->getStatusCode()->getCode());
    }

    /**
     * Test preserving session between requests.
     */
    public function testPreserveSessionBetweenRequests()
    {
        $request = new FakeRequest('http://localhost/session');
        $response = new FakeResponse();
        $this->application->run($request, $response);

        self::assertSame(StatusCode::OK, $response->getStatusCode()->getCode());
        self::assertSame('', $response->getContent());
        self::assertSame([], iterator_to_array($request->getSessionItems()));

        $session = $request->getSessionItems();
        $request = new FakeRequest('http://localhost/session', 'post');
        $request->setSessionItems($session);
        $request->setFormParameter('Name', 'Foo');
        $request->setFormParameter('Value', 'Bar');
        $response = new FakeResponse();
        $this->application->run($request, $response);

        self::assertSame(StatusCode::OK, $response->getStatusCode()->getCode());
        self::assertSame('Foo=Bar', $response->getContent());
        self::assertSame(['Foo' => 'Bar'], iterator_to_array($request->getSessionItems()));

        $session = $request->getSessionItems();
        $request = new FakeRequest('http://localhost/session', 'post');
        $request->setSessionItems($session);
        $request->setFormParameter('Name', '1');
        $request->setFormParameter('Value', '2');
        $response = new FakeResponse();
        $this->application->run($request, $response);

        self::assertSame(StatusCode::OK, $response->getStatusCode()->getCode());
        self::assertSame('Foo=Bar,1=2', $response->getContent());
        self::assertSame(['Foo' => 'Bar', 1 => '2'], iterator_to_array($request->getSessionItems()));

        $session = $request->getSessionItems();
        $request = new FakeRequest('http://localhost/session');
        $request->setSessionItems($session);
        $response = new FakeResponse();
        $this->application->run($request, $response);

        self::assertSame(StatusCode::OK, $response->getStatusCode()->getCode());
        self::assertSame('Foo=Bar,1=2', $response->getContent());
        self::assertSame(['Foo' => 'Bar', 1 => '2'], iterator_to_array($request->getSessionItems()));
    }

    /**
     * Set up.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->application = new FakeApplication();
        $this->application->setViewPath(FilePath::parse(__DIR__ . '/Helpers/Views/'));
        $this->application->addViewRenderer(new TestViewRenderer());
        $this->application->addRoute(new Route('', TestController::class));
    }

    /**
     * Tear down.
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $this->application = null;
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

    /**
     * @var FakeApplication My application.
     */
    private $application;
}
