<?php

use BlueMvc\Core\Exceptions\InvalidFilePathException;
use BlueMvc\Fakes\FakeApplication;
use DataTypes\FilePath;

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
        } catch (InvalidFilePathException $e) {
            $exceptionMessage = $e->getMessage();
        }

        $this->assertSame('Document root "' . $DS . 'var' . $DS . "\0" . 'www' . $DS . '" is not valid: File path "' . $DS . 'var' . $DS . "\0" . 'www' . $DS . '" is invalid: Part of directory "' . "\0" . 'www" contains invalid character "' . "\0" . '".', $exceptionMessage);
    }
}
