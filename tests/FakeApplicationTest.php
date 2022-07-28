<?php

declare(strict_types=1);

namespace BlueMvc\Fakes\Tests;

use BlueMvc\Core\Collections\CustomItemCollection;
use BlueMvc\Core\Exceptions\InvalidControllerClassException;
use BlueMvc\Core\Exceptions\InvalidFilePathException;
use BlueMvc\Core\Route;
use BlueMvc\Fakes\FakeApplication;
use BlueMvc\Fakes\Tests\Helpers\TestController;
use BlueMvc\Fakes\Tests\Helpers\TestErrorController;
use BlueMvc\Fakes\Tests\Helpers\TestViewRenderer;
use DataTypes\System\Exceptions\FilePathInvalidArgumentException;
use DataTypes\System\FilePath;
use PHPUnit\Framework\TestCase;

/**
 * Test FakeApplication class.
 */
class FakeApplicationTest extends TestCase
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
     *
     * @noinspection PhpDeprecationInspection
     */
    public function testGetViewPath()
    {
        $DS = DIRECTORY_SEPARATOR;
        $fakeApplication = new FakeApplication($DS . 'var' . $DS . 'www' . $DS);

        self::assertSame($DS . 'var' . $DS . 'www' . $DS, $fakeApplication->getViewPath()->__toString());
    }

    /**
     * Test getViewPaths method.
     */
    public function testGetViewPaths()
    {
        $DS = DIRECTORY_SEPARATOR;
        $fakeApplication = new FakeApplication($DS . 'var' . $DS . 'www' . $DS);

        self::assertCount(1, $fakeApplication->getViewPaths());
        self::assertSame($DS . 'var' . $DS . 'www' . $DS, $fakeApplication->getViewPaths()[0]->__toString());
    }

    /**
     * Test setViewPath method.
     */
    public function testSetViewPath()
    {
        $DS = DIRECTORY_SEPARATOR;
        $fakeApplication = new FakeApplication($DS . 'var' . $DS . 'www' . $DS);
        $fakeApplication->setViewPath(FilePath::parseAsDirectory('views'));

        self::assertCount(1, $fakeApplication->getViewPaths());
        self::assertSame($DS . 'var' . $DS . 'www' . $DS . 'views' . $DS, $fakeApplication->getViewPaths()[0]->__toString());
    }

    /**
     * Test setViewPaths method.
     */
    public function testSetViewPaths()
    {
        $DS = DIRECTORY_SEPARATOR;
        $fakeApplication = new FakeApplication($DS . 'var' . $DS . 'www' . $DS);
        $fakeApplication->setViewPaths(
            [
                FilePath::parseAsDirectory('views'),
                FilePath::parseAsDirectory('views2'),
            ]
        );

        self::assertCount(2, $fakeApplication->getViewPaths());
        self::assertSame($DS . 'var' . $DS . 'www' . $DS . 'views' . $DS, $fakeApplication->getViewPaths()[0]->__toString());
        self::assertSame($DS . 'var' . $DS . 'www' . $DS . 'views2' . $DS, $fakeApplication->getViewPaths()[1]->__toString());
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
        $fakeApplication->setTempPath(FilePath::parseAsDirectory('temp'));

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
     * Test setErrorControllerClass method with non-existing class name.
     */
    public function testSetErrorControllerClassWithNonExistingClassName()
    {
        self::expectException(InvalidControllerClassException::class);
        self::expectExceptionMessage('"\BlueMvc\Fakes\Foo" is not a valid error controller class.');

        $fakeApplication = new FakeApplication();
        $fakeApplication->setErrorControllerClass('\\BlueMvc\\Fakes\\Foo');
    }

    /**
     * Test setErrorControllerClass method with non-controller class name.
     */
    public function testSetErrorControllerWithNonControllerClassName()
    {
        self::expectException(InvalidControllerClassException::class);
        self::expectExceptionMessage('"BlueMvc\Fakes\FakeApplication" is not a valid error controller class.');

        $fakeApplication = new FakeApplication();
        $fakeApplication->setErrorControllerClass(FakeApplication::class);
    }

    /**
     * Test setErrorControllerClass method with ordinary controller class name.
     */
    public function testSetErrorControllerWithOrdinaryControllerClassName()
    {
        self::expectException(InvalidControllerClassException::class);
        self::expectExceptionMessage('"BlueMvc\Fakes\Tests\Helpers\TestController" is not a valid error controller class.');

        $fakeApplication = new FakeApplication();
        $fakeApplication->setErrorControllerClass(TestController::class);
    }

    /**
     * Test getCustomItems method.
     */
    public function testGetCustomItems()
    {
        $fakeApplication = new FakeApplication();

        self::assertSame([], iterator_to_array($fakeApplication->getCustomItems()));
    }

    /**
     * Test setCustomItems method.
     */
    public function testSetCustomItems()
    {
        $customItems = new CustomItemCollection();
        $customItems->set('Foo', 'Bar');

        $fakeApplication = new FakeApplication();
        $fakeApplication->setCustomItems($customItems);

        self::assertSame(['Foo' => 'Bar'], iterator_to_array($fakeApplication->getCustomItems()));
    }

    /**
     * Test getCustomItem method.
     */
    public function testGetCustomItem()
    {
        $customItems = new CustomItemCollection();
        $customItems->set('Foo', 'Bar');

        $fakeApplication = new FakeApplication();
        $fakeApplication->setCustomItems($customItems);

        self::assertSame('Bar', $fakeApplication->getCustomItem('Foo'));
        self::assertNull($fakeApplication->getCustomItem('foo'));
        self::assertNull($fakeApplication->getCustomItem('Bar'));
    }

    /**
     * Test setCustomItem method.
     */
    public function testSetCustomItem()
    {
        $fakeApplication = new FakeApplication();
        $fakeApplication->setCustomItem('Foo', 'Bar');

        self::assertSame(['Foo' => 'Bar'], iterator_to_array($fakeApplication->getCustomItems()));
    }
}
