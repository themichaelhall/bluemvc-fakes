<?php

namespace BlueMvc\Fakes\Tests;

use BlueMvc\Core\Exceptions\InvalidFilePathException;
use BlueMvc\Core\Route;
use BlueMvc\Fakes\FakeApplication;
use BlueMvc\Fakes\Tests\Helpers\TestController;
use BlueMvc\Fakes\Tests\Helpers\TestErrorController;
use BlueMvc\Fakes\Tests\Helpers\TestViewRenderer;
use DataTypes\Exceptions\FilePathInvalidArgumentException;
use DataTypes\FilePath;

/**
 * Test FakeApplication class.
 */
class FakeApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test default constructor.
     */
    public function testDefaultConstructor()
    {
        $DS = DIRECTORY_SEPARATOR;
        $fakeApplication = new FakeApplication();

        self::assertSame(FilePath::parse(getcwd() . $DS)->__toString(), $fakeApplication->getDocumentRoot()->__toString());
    }

    /**
     * Test constructor with document root.
     */
    public function testConstructorWithDocumentRoot()
    {
        $DS = DIRECTORY_SEPARATOR;
        $fakeApplication = new FakeApplication($DS . 'var' . $DS . 'www' . $DS);

        self::assertSame($DS . 'var' . $DS . 'www' . $DS, $fakeApplication->getDocumentRoot()->__toString());
    }

    /**
     * Test constructor with document root with missing trailing directory separator.
     */
    public function testConstructorWithDocumentRootWithMissingTrailingDirectorySeparator()
    {
        $DS = DIRECTORY_SEPARATOR;
        $fakeApplication = new FakeApplication($DS . 'var' . $DS . 'www');

        self::assertSame($DS . 'var' . $DS . 'www' . $DS, $fakeApplication->getDocumentRoot()->__toString());
    }

    /**
     * Test constructor with relative document root.
     */
    public function testConstructorWithRelativeDocumentRoot()
    {
        $DS = DIRECTORY_SEPARATOR;
        $exceptionMessage = '';

        try {
            new FakeApplication('var' . $DS . 'www');
        } catch (InvalidFilePathException $e) {
            $exceptionMessage = $e->getMessage();
        }

        self::assertSame('Document root "var' . $DS . 'www' . $DS . '" is not an absolute path.', $exceptionMessage);
    }

    /**
     * Test constructor with invalid document root.
     */
    public function testConstructorWithInvalidDocumentRoot()
    {
        $DS = DIRECTORY_SEPARATOR;
        $exceptionMessage = '';

        try {
            new FakeApplication($DS . 'var' . $DS . "\0" . 'www');
        } catch (FilePathInvalidArgumentException $e) {
            $exceptionMessage = $e->getMessage();
        }

        self::assertSame('File path "' . $DS . 'var' . $DS . "\0" . 'www' . $DS . '" is invalid: Part of directory "' . "\0" . 'www" contains invalid character "' . "\0" . '".', $exceptionMessage);
    }

    /**
     * Test constructor with invalid document root parameter type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $documentRoot parameter is not a string or null.
     */
    public function testConstructorWithInvalidDocumentRootParameterType()
    {
        new FakeApplication(true);
    }

    /**
     * Test getDocumentRoot method.
     */
    public function testGetDocumentRoot()
    {
        $DS = DIRECTORY_SEPARATOR;
        $fakeApplication = new FakeApplication($DS . 'var' . $DS . 'www' . $DS);

        self::assertSame($DS . 'var' . $DS . 'www' . $DS, $fakeApplication->getDocumentRoot()->__toString());
    }

    /**
     * Test getRoutes method.
     */
    public function testGetRoutes()
    {
        $fakeApplication = new FakeApplication();
        $routes = $fakeApplication->getRoutes();

        self::assertSame(0, count($routes));
    }

    /**
     * Test addRoute method.
     */
    public function testAddRoute()
    {
        $fakeApplication = new FakeApplication();
        $fakeApplication->addRoute(new Route('', TestController::class));
        $routes = $fakeApplication->getRoutes();

        self::assertSame(1, count($routes));
    }

    /**
     * Test getViewPath method.
     */
    public function testGetViewPath()
    {
        $DS = DIRECTORY_SEPARATOR;
        $fakeApplication = new FakeApplication($DS . 'var' . $DS . 'www' . $DS);

        self::assertSame($DS . 'var' . $DS . 'www' . $DS, $fakeApplication->getViewPath()->__toString());
    }

    /**
     * Test setViewPath method.
     */
    public function testSetViewPath()
    {
        $DS = DIRECTORY_SEPARATOR;
        $fakeApplication = new FakeApplication($DS . 'var' . $DS . 'www' . $DS);
        $fakeApplication->setViewPath(FilePath::parse('views/'));

        self::assertSame($DS . 'var' . $DS . 'www' . $DS . 'views' . $DS, $fakeApplication->getViewPath()->__toString());
    }

    /**
     * Test getTempPath method.
     */
    public function testGetTempPath()
    {
        $DS = DIRECTORY_SEPARATOR;
        $fakeApplication = new FakeApplication();

        self::assertSame(sys_get_temp_dir() . $DS . 'bluemvc' . $DS . sha1($fakeApplication->getDocumentRoot()->__toString()) . $DS, $fakeApplication->getTempPath()->__toString());
    }

    /**
     * Test setTempPath method.
     */
    public function testSetTempPath()
    {
        $DS = DIRECTORY_SEPARATOR;
        $fakeApplication = new FakeApplication($DS . 'var' . $DS . 'www' . $DS);
        $fakeApplication->setTempPath(FilePath::parse('temp/'));

        self::assertSame($DS . 'var' . $DS . 'www' . $DS . 'temp' . $DS, $fakeApplication->getTempPath()->__toString());
    }

    /**
     * Test getViewRenderers method.
     */
    public function testGetViewRenderers()
    {
        $fakeApplication = new FakeApplication();

        self::assertSame(0, count($fakeApplication->getViewRenderers()));
    }

    /**
     * Test addViewRenderer method.
     */
    public function testAddViewRenderer()
    {
        $fakeApplication = new FakeApplication();
        $fakeApplication->addViewRenderer(new TestViewRenderer());
        $viewRenderers = $fakeApplication->getViewRenderers();

        self::assertSame(1, count($viewRenderers));
        self::assertInstanceOf(TestViewRenderer::class, $viewRenderers[0]);
    }

    /**
     * Test isDebug method.
     */
    public function testIsDebug()
    {
        $fakeApplication = new FakeApplication();

        self::assertFalse($fakeApplication->isDebug());
    }

    /**
     * Test setDebug method.
     */
    public function testSetDebug()
    {
        $fakeApplication = new FakeApplication();
        $fakeApplication->setDebug(true);

        self::assertTrue($fakeApplication->isDebug());
    }

    /**
     * Test getErrorControllerClass method.
     */
    public function testGetErrorControllerClass()
    {
        $fakeApplication = new FakeApplication();

        self::assertNull($fakeApplication->getErrorControllerClass());
    }

    /**
     * Test setErrorControllerClass method.
     */
    public function testSetErrorControllerClass()
    {
        $fakeApplication = new FakeApplication();
        $fakeApplication->setErrorControllerClass(TestErrorController::class);

        self::assertSame(TestErrorController::class, $fakeApplication->getErrorControllerClass());
    }

    /**
     * Test setErrorControllerClass method with invalid parameter type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $errorControllerClass parameter is not a string.
     */
    public function testSetErrorControllerClassWithInvalidParameterType()
    {
        $fakeApplication = new FakeApplication();
        /** @noinspection PhpParamsInspection */
        $fakeApplication->setErrorControllerClass([]);
    }

    /**
     * Test setErrorControllerClass method with non-existing class name.
     *
     * @expectedException \BlueMvc\Core\Exceptions\InvalidControllerClassException
     * @expectedExceptionMessage "\BlueMvc\Fakes\Foo" is not a valid error controller class.
     */
    public function testSetErrorControllerClassWithNonExistingClassName()
    {
        $fakeApplication = new FakeApplication();
        $fakeApplication->setErrorControllerClass('\\BlueMvc\\Fakes\\Foo');
    }

    /**
     * Test setErrorControllerClass method with non-controller class name.
     *
     * @expectedException \BlueMvc\Core\Exceptions\InvalidControllerClassException
     * @expectedExceptionMessage "BlueMvc\Fakes\FakeApplication" is not a valid error controller class.
     */
    public function testSetErrorControllerWithNonControllerClassName()
    {
        $fakeApplication = new FakeApplication();
        $fakeApplication->setErrorControllerClass(FakeApplication::class);
    }

    /**
     * Test setErrorControllerClass method with ordinary controller class name.
     *
     * @expectedException \BlueMvc\Core\Exceptions\InvalidControllerClassException
     * @expectedExceptionMessage "BlueMvc\Fakes\Tests\Helpers\TestController" is not a valid error controller class.
     */
    public function testSetErrorControllerWithOrdinaryControllerClassName()
    {
        $fakeApplication = new FakeApplication();
        $fakeApplication->setErrorControllerClass(TestController::class);
    }

    /**
     * Test getSessionItems method.
     */
    public function testGetSessionItems()
    {
        $fakeApplication = new FakeApplication();

        self::assertSame([], iterator_to_array($fakeApplication->getSessionItems()));
    }

    /**
     * Test setSessionItem method.
     */
    public function testSetSessionItem()
    {
        $fakeApplication = new FakeApplication();
        $fakeApplication->setSessionItem('Foo', ['Bar', 'Baz']);
        $fakeApplication->setSessionItem('Bar', 12345);

        self::assertSame(['Foo' => ['Bar', 'Baz'], 'Bar' => 12345], iterator_to_array($fakeApplication->getSessionItems()));
    }

    /**
     * Test getSessionItem method.
     */
    public function testGetSessionItem()
    {
        $fakeApplication = new FakeApplication();
        $fakeApplication->setSessionItem('Foo', 1);
        $fakeApplication->setSessionItem('Bar', 2);

        self::assertSame(1, $fakeApplication->getSessionItem('Foo'));
        self::assertSame(2, $fakeApplication->getSessionItem('Bar'));
        self::assertNull($fakeApplication->getSessionItem('Baz'));
        self::assertNull($fakeApplication->getSessionItem('foo'));
    }

    /**
     * Test removeSessionItem method.
     */
    public function testRemoveSessionItem()
    {
        $fakeApplication = new FakeApplication();
        $fakeApplication->setSessionItem('Foo', 1);
        $fakeApplication->setSessionItem('Bar', 2);
        $fakeApplication->removeSessionItem('Foo');
        $fakeApplication->removeSessionItem('Baz');

        self::assertNull($fakeApplication->getSessionItem('Foo'));
        self::assertSame(2, $fakeApplication->getSessionItem('Bar'));
        self::assertNull($fakeApplication->getSessionItem('Baz'));
    }
}
