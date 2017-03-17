<?php

use BlueMvc\Core\Exceptions\InvalidFilePathException;
use BlueMvc\Core\Route;
use BlueMvc\Fakes\FakeApplication;
use DataTypes\Exceptions\FilePathInvalidArgumentException;
use DataTypes\FilePath;

require_once __DIR__ . '/Helpers/TestController.php';
require_once __DIR__ . '/Helpers/TestViewRenderer.php';

/**
 * Test FakeApplication class.
 */
class FakeApplicationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test default constructor.
     */
    public function testDefaultConstructor()
    {
        $DS = DIRECTORY_SEPARATOR;
        $fakeApplication = new FakeApplication();

        $this->assertSame(FilePath::parse(getcwd() . $DS)->__toString(), $fakeApplication->getDocumentRoot()->__toString());
    }

    /**
     * Test constructor with document root.
     */
    public function testConstructorWithDocumentRoot()
    {
        $DS = DIRECTORY_SEPARATOR;
        $fakeApplication = new FakeApplication($DS . 'var' . $DS . 'www' . $DS);

        $this->assertSame($DS . 'var' . $DS . 'www' . $DS, $fakeApplication->getDocumentRoot()->__toString());
    }

    /**
     * Test constructor with document root with missing trailing directory separator.
     */
    public function testConstructorWithDocumentRootWithMissingTrailingDirectorySeparator()
    {
        $DS = DIRECTORY_SEPARATOR;
        $fakeApplication = new FakeApplication($DS . 'var' . $DS . 'www');

        $this->assertSame($DS . 'var' . $DS . 'www' . $DS, $fakeApplication->getDocumentRoot()->__toString());
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

        $this->assertSame('Document root "var' . $DS . 'www' . $DS . '" is not an absolute path.', $exceptionMessage);
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

        $this->assertSame('File path "' . $DS . 'var' . $DS . "\0" . 'www' . $DS . '" is invalid: Part of directory "' . "\0" . 'www" contains invalid character "' . "\0" . '".', $exceptionMessage);
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

        $this->assertSame($DS . 'var' . $DS . 'www' . $DS, $fakeApplication->getDocumentRoot()->__toString());
    }

    /**
     * Test getRoutes method.
     */
    public function testGetRoutes()
    {
        $fakeApplication = new FakeApplication();
        $routes = $fakeApplication->getRoutes();

        $this->assertSame(0, count($routes));
    }

    /**
     * Test addRoute method.
     */
    public function testAddRoute()
    {
        $fakeApplication = new FakeApplication();
        $fakeApplication->addRoute(new Route('', TestController::class));
        $routes = $fakeApplication->getRoutes();

        $this->assertSame(1, count($routes));
    }

    /**
     * Test getViewPath method.
     */
    public function testGetViewPath()
    {
        $DS = DIRECTORY_SEPARATOR;
        $fakeApplication = new FakeApplication($DS . 'var' . $DS . 'www' . $DS);

        $this->assertSame($DS . 'var' . $DS . 'www' . $DS, $fakeApplication->getViewPath()->__toString());
    }

    /**
     * Test setViewPath method.
     */
    public function testSetViewPath()
    {
        $DS = DIRECTORY_SEPARATOR;
        $fakeApplication = new FakeApplication($DS . 'var' . $DS . 'www' . $DS);
        $fakeApplication->setViewPath(FilePath::parse('views/'));

        $this->assertSame($DS . 'var' . $DS . 'www' . $DS . 'views' . $DS, $fakeApplication->getViewPath()->__toString());
    }

    /**
     * Test getTempPath method.
     */
    public function testGetTempPath()
    {
        $DS = DIRECTORY_SEPARATOR;
        $fakeApplication = new FakeApplication();

        $this->assertSame(sys_get_temp_dir() . $DS . 'bluemvc' . $DS . sha1($fakeApplication->getDocumentRoot()->__toString()) . $DS, $fakeApplication->getTempPath()->__toString());
    }

    /**
     * Test setTempPath method.
     */
    public function testSetTempPath()
    {
        $DS = DIRECTORY_SEPARATOR;
        $fakeApplication = new FakeApplication($DS . 'var' . $DS . 'www' . $DS);
        $fakeApplication->setTempPath(FilePath::parse('temp/'));

        $this->assertSame($DS . 'var' . $DS . 'www' . $DS . 'temp' . $DS, $fakeApplication->getTempPath()->__toString());
    }

    /**
     * Test getViewRenderers method.
     */
    public function testGetViewRenderers()
    {
        $fakeApplication = new FakeApplication();

        $this->assertSame(0, count($fakeApplication->getViewRenderers()));
    }

    /**
     * Test addViewRenderer method.
     */
    public function testAddViewRenderer()
    {
        $fakeApplication = new FakeApplication();
        $fakeApplication->addViewRenderer(new TestViewRenderer());
        $viewRenderers = $fakeApplication->getViewRenderers();

        $this->assertSame(1, count($viewRenderers));
        $this->assertInstanceOf(TestViewRenderer::class, $viewRenderers[0]);
    }

    /**
     * Test isDebug method.
     */
    public function testIsDebug()
    {
        $fakeApplication = new FakeApplication();

        $this->assertFalse($fakeApplication->isDebug());
    }
}
