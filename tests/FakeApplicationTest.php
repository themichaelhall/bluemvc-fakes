<?php

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
        $fakeApplication = new FakeApplication(FilePath::parse($DS . 'var' . $DS . 'www' . $DS));

        $this->assertSame($DS . 'var' . $DS . 'www' . $DS, $fakeApplication->getDocumentRoot()->__toString());
    }

    /**
     * Test setDocumentRoot method.
     */
    public function testSetDocumentRoot()
    {
        $DS = DIRECTORY_SEPARATOR;
        $fakeApplication = new FakeApplication();
        $fakeApplication->setDocumentRoot(FilePath::parse($DS . 'var' . $DS . 'www' . $DS));

        $this->assertSame($DS . 'var' . $DS . 'www' . $DS, $fakeApplication->getDocumentRoot()->__toString());
    }
}
